<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Symfony\Component\Process\Process;
use App\Models\Conversation;
use App\Models\Message;
use Illuminate\Support\Facades\Auth;
use GuzzleHttp\Client;

class MemeAgentController extends Controller
{
    public function index(Request $request)
    {
        // Check if this is a new session visit to clear old local history
        $clearHistory = false;
        if (!$request->session()->has('meme_agent_initialized')) {
            $clearHistory = true;
            $request->session(['meme_agent_initialized' => true]);
        }

        $brands = \App\Models\Brand::where('status', 'active')->orderBy('company_name')->get();
        $selected_brand_id = $request->query('brand_id');

        return view('meme-agent', compact('clearHistory', 'brands', 'selected_brand_id'));
    }

    public function generate(Request $request)
    {
        set_time_limit(300);
        $topic = trim((string) $request->input('topic', ''));
        $style = trim((string) $request->input('style', 'relatable'));
        $tone = trim((string) $request->input('tone', 'funny'));
        $template = trim((string) $request->input('template', 'AUTO'));

        if ($topic === '') {
            return response()->json(['error' => 'Topic is required.'], 422);
        }

        $scriptPath = base_path('meme_agent/main.py');
        $input = implode("\n", [$topic, $style, $tone, $template, 'exit']) . "\n";

        $process = new Process(['python', $scriptPath], base_path());
        $process->setInput($input);
        $process->setTimeout(300);
        $process->run();

        if (!$process->isSuccessful()) {
            return response()->json([
                'error' => 'Meme agent failed to run.',
                'details' => $process->getErrorOutput(),
            ], 500);
        }

        $output = $process->getOutput();
        $pattern = "/(^|\\n)\\s*(\\d+)\\.\\s*\\[(T\\d+)\\]\\s*(.*?)(?=\\n\\s*\\d+\\.\\s*\\[T\\d+\\]|\\s*$)/s";
        $memes = [];

        if (preg_match_all($pattern, $output, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $match) {
                $memes[] = [
                    'template' => $match[3],
                    'caption' => trim($match[4])
                ];
            }
        }

        // STRIKE RULE: Exactly 3 memes
        $memes = array_slice($memes, 0, 3);
        while (count($memes) < 3) {
            $memes[] = [
                'template' => 'auto',
                'caption' => "Bhai ye wala meme epic hai! 😂"
            ];
        }

        return response()->json(['memes' => $memes]);
    }

    public function createConversation(Request $request)
    {
        $conversation = Conversation::create([
            'user_id' => Auth::id(),
            'title' => 'New Chat',
        ]);

        return response()->json([
            'conversation_id' => $conversation->id,
            'title' => $conversation->title,
        ]);
    }

    public function getConversations($user_id)
    {
        if (Auth::id() != $user_id) {
            abort(403, 'Unauthorized');
        }

        $conversations = Conversation::where('user_id', $user_id)
            ->has('messages')
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'title', 'created_at']);

        return response()->json(['conversations' => $conversations]);
    }

    public function getConversation($id)
    {
        $conversation = Conversation::with('messages')->findOrFail($id);

        if ($conversation->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $messages = $conversation->messages()->orderBy('created_at', 'asc')->get();

        return response()->json([
            'conversation' => $conversation,
            'messages' => $messages,
        ]);
    }

    public function saveMessage(Request $request)
    {
        $request->validate([
            'conversation_id' => 'required|exists:conversations,id',
            'sender_type' => 'required|in:user,agent',
            'content' => 'required|string',
        ]);

        $message = Message::create([
            'conversation_id' => $request->conversation_id,
            'user_id' => $request->sender_type === 'user' ? Auth::id() : null,
            'content' => $request->content,
            'sender_type' => $request->sender_type,
            'style' => $request->style,
            'tone' => $request->tone,
            'template' => $request->template,
        ]);

        return response()->json(['success' => true, 'message_id' => $message->id]);
    }

    public function updateConversationTitle(Request $request, $id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $conversation->update([
            'title' => $request->title,
        ]);

        return response()->json(['success' => true]);
    }

    public function chat(Request $request)
    {
        $message = $request->input('message', '');
        $style = $request->input('style', 'relatable');
        $tone = $request->input('tone', 'funny');
        $template = $request->input('template', 'AUTO');

        set_time_limit(180);
        $client = new Client();
        try {
            $response = $client->post('http://127.0.0.1:8003/chat', [
                'json' => [
                    'message' => $message,
                    'style' => $style,
                    'tone' => $tone,
                    'template' => $template,
                    'user_id' => (string)Auth::id(),
                ],
                'timeout' => 170,
            ]);

            $body = $response->getBody()->getContents();
            \Log::info('[CONTROLLER] Raw backend response: ' . $body);

            $result = json_decode($body, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                \Log::error('[CONTROLLER] JSON decode error: ' . json_last_error_msg());
                throw new \Exception('Invalid JSON from backend');
            }

            // ✅ FIX: Handle new server.py response format
            // Server returns: {success, type, items, conversation_id}
            // Old format was: {memes, reply, meme_intent}
            $type = $result['type'] ?? 'content';
            $items = $result['items'] ?? [];
            $memeIntent = $type === 'content';

            // Convert items array to memes format
            $memes = $result['memes'] ?? [];
            if (empty($memes) && !empty($items)) {
                foreach ($items as $item) {
                    $memes[] = [
                        'style' => 'relatable',
                        'caption' => $item,
                        'template' => 'auto'
                    ];
                }
            }

            // Reply: for chat mode show first item, for content mode show intro
            $reply = $result['reply'] ?? ($type === 'chat'
                ? ($items[0] ?? "Meme hazir hai! 😂")
                : "Ye dekho! 😂");

            \Log::info('[CONTROLLER] Meme intent: ' . ($memeIntent ? 'YES' : 'NO'));
            \Log::info('[CONTROLLER] Meme count from backend: ' . count($memes));

            // If it's a normal chat (no intent), don't pad memes
            if (!$memeIntent) {
                return response()->json([
                    'success' => true,
                    'response' => [
                        'reply' => $reply,
                        'memes' => []
                    ],
                    'memes' => [],
                    'meme_intent' => false
                ]);
            }

            // Ensure exactly 3 memes ONLY if intent is true
            $memes = array_slice($memes, 0, 3);
            while (count($memes) < 3) {
                $styles = ['relatable', 'savage', 'desi'];
                $memes[] = [
                    'style' => $styles[count($memes) % 3],
                    'caption' => "Bhai {$message} ka scene hi alag hai! 😂",
                    'template' => 'auto'
                ];
            }

            // Return structured JSON matching frontend expectation
            $responseData = [
                'success' => true,
                'response' => [
                    'reply' => $reply,
                    'memes' => $memes
                ],
                'memes' => $memes
            ];

            // AUTO-SAVE to Meme model if brand_id is provided
            if ($request->filled('brand_id')) {
                foreach ($memes as $memeData) {
                    \App\Models\Meme::create([
                        'user_id' => Auth::id(),
                        'brand_id' => $request->brand_id,
                        'title' => is_array($memeData) ? ($memeData['caption'] ?? '') : $memeData,
                        'status' => 'pending',
                        'template' => is_array($memeData) ? ($memeData['template'] ?? null) : null,
                    ]);
                }
            }

            return response()->json($responseData);

        } catch (\Exception $e) {
            \Log::error('[CONTROLLER] Chat error: ' . $e->getMessage());

            $fallbackMemes = [
                ['style' => 'relatable', 'caption' => "When {$message} hits different! 😂", 'template' => 'auto'],
                ['style' => 'savage', 'caption' => "{$message} walla epic moment! 💀", 'template' => 'auto'],
                ['style' => 'desi', 'caption' => "Bhai {$message} ka scene check karo! 😲", 'template' => 'auto'],
            ];

            return response()->json([
                'success' => false,
                'response' => [
                    'reply' => "Sorry, there was an error.",
                    'memes' => $fallbackMemes
                ],
                'memes' => $fallbackMemes,
                'error' => $e->getMessage()
            ]);
        }
    }

    public function generateMemeFromConversation(Request $request)
    {
        $conversation = Conversation::with('messages')->find($request->conversation_id);

        if (!$conversation) {
            $conversation = Conversation::create([
                'user_id' => Auth::id(),
                'title' => 'New Chat',
            ]);
        }

        if ($conversation->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $messages = $conversation->messages()
            ->orderBy('created_at', 'asc')
            ->get()
            ->pluck('content')
            ->join(' ');

        $topic = substr($messages, 0, 200) . ' ' . $request->request_message;

        set_time_limit(180);
        $client = new Client();
        try {
            $response = $client->post('http://127.0.0.1:8003/generate-meme', [
                'json' => [
                    'message' => $topic,
                    'style' => $request->style ?? 'relatable',
                    'tone' => $request->tone ?? 'funny',
                    'template' => $request->template ?? 'AUTO',
                    'user_id' => (string)Auth::id(),
                ],
                'timeout' => 170,
            ]);

            $result = json_decode($response->getBody()->getContents(), true);
            $success = $result['success'] ?? false;

            $memes = [];
            if ($success && isset($result['data']['memes'])) {
                $memes = array_slice($result['data']['memes'], 0, 3);
            }

            while (count($memes) < 3) {
                $memes[] = [
                    'style' => 'relatable',
                    'caption' => "Scene hi alag hai bhai! 😂",
                    'template' => 'auto'
                ];
            }

            if ($request->filled('brand_id')) {
                foreach ($memes as $memeData) {
                    \App\Models\Meme::create([
                        'user_id' => Auth::id(),
                        'brand_id' => $request->brand_id,
                        'title' => is_array($memeData) ? ($memeData['caption'] ?? '') : $memeData,
                        'status' => 'pending',
                        'template' => is_array($memeData) ? ($memeData['template'] ?? null) : null,
                    ]);
                }
            }

            return response()->json([
                'success' => $success,
                'memes' => $memes,
                'intro' => $result['intro'] ?? null,
                'summary' => substr($topic, 0, 50) . '...',
                'error' => $result['error'] ?? null
            ]);
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'error' => $e->getMessage()]);
        }
    }

    public function searchConversations(Request $request)
    {
        $userId = Auth::id();
        $searchTerm = $request->input('q', '');

        $conversations = Conversation::where('user_id', $userId)
            ->where(function($query) use ($searchTerm) {
                $query->where('title', 'LIKE', "%{$searchTerm}%")
                      ->orWhereHas('messages', function($q) use ($searchTerm) {
                          $q->where('content', 'LIKE', "%{$searchTerm}%");
                      });
            })
            ->orderBy('updated_at', 'desc')
            ->get(['id', 'title', 'created_at']);

        return response()->json(['conversations' => $conversations]);
    }

    public function deleteConversation($id)
    {
        $conversation = Conversation::findOrFail($id);

        if ($conversation->user_id != Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $conversation->delete();

        return response()->json(['success' => true]);
    }
}    