<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Meme;
use App\Models\MemeComment;
use App\Models\Reaction;
use App\Models\WeeklyChallenge;
use App\Models\ShareEvent;
use App\Models\ChallengeEntry;
use App\Models\Report;
use App\Models\User;
use App\Models\EngagementAudit;
use App\EngagementAuditService;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;

class MemeController extends Controller
{

    // Show Upload Form
    public function create()
    {
        return view('upload-meme');
    }

    // Store Uploaded Meme
    public function store(Request $request)
    {
        $request->validate([
            'title' => 'nullable|string|max:1500', // Limit title to 1500 characters
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048', // Limit to 2MB, only jpg, png, webp
            'template' => 'nullable|in:square,portrait,landscape'
        ]);

        // Prevent empty posts (no image + no title)
        if (!$request->filled('title') && !$request->hasFile('image')) {
            return redirect()->back()->with('error', 'Please provide either a title or an image.')->withInput();
        }

        $memeData = [
            'user_id' => Auth::id(),
            'template' => $request->input('template', 'square'), // Default to square if not provided
        ];

        $title = $request->input('title');
        $tags = $request->input('tags');

        if ($tags) {
            $title = $title ? $title . "\n" . $tags : $tags;
        }

        $memeData['title'] = $title ?? 'Untitled Meme';

        if ($request->hasFile('image')) {
            $path = $request->file('image')->store('memes', 'public');
            $memeData['image_path'] = $path;
        }


        
        $newMeme = Meme::create($memeData);

        // Check if user clicked "Pay for Meme"
        if ($request->input('action') === 'pay') {
            return redirect()->route('memes.pay', $newMeme->id);
        }

        return redirect('/')->with('success', 'Meme uploaded successfully!')->with('highlight_meme_id', $newMeme->id);
    }

    // Helper to fetch sidebar data
    private function getSidebarData()
    {
        $activeChallenge = WeeklyChallenge::current();
        
        // 1. Current Challenge Data
        $sidebarEndTime = $activeChallenge && $activeChallenge->end_at 
            ? $activeChallenge->end_at->toIso8601String() 
            : null;
            
        $sidebarPrizePool = $activeChallenge && $activeChallenge->prize_pool_cents 
            ? number_format($activeChallenge->prize_pool_cents / 100, 0)
            : '0';

        // 2. Top 3 Global Creators based on total engagement (likes, shares, comments)
        $sidebarTop3 = User::whereHas('memes')
            ->withSum('memes as total_score', 'score')
            ->orderByDesc('total_score')
            ->limit(3)
            ->get()
            ->map(function($user) {
                return (object) [
                    'user_id' => $user->id,
                    'user_name' => $user->name,
                    'user_avatar' => $user->profile_photo_url,
                    'score' => $user->total_score ?? 0,
                ];
            });

        // 3. Last Week's Winners
        $sidebarLastWeekWinners = [];
        $previousChallenge = WeeklyChallenge::previous();
        
        if ($previousChallenge) {
            if ($previousChallenge->winner_meme_id) {
                $winnerMeme = Meme::find($previousChallenge->winner_meme_id);
                if ($winnerMeme) {
                     $sidebarLastWeekWinners[] = (object) [
                        'rank' => 1,
                        'user_name' => $winnerMeme->user->name,
                        'user_avatar' => $winnerMeme->user->profile_photo_url,
                        'prize' => '$' . number_format(($previousChallenge->prize_pool_cents * 0.50) / 100, 0),
                    ];
                }
            } else {
                $prevLeaders = $previousChallenge->getLeaderboard(1);
                foreach($prevLeaders as $leader) {
                    $sidebarLastWeekWinners[] = (object) [
                        'rank' => 1,
                        'user_name' => $leader->user->name,
                         'user_avatar' => $leader->user->profile_photo_url,
                        'prize' => '$' . number_format(($previousChallenge->prize_pool_cents * 0.50) / 100, 0),
                    ];
                }
            }
        }

        // 4. Live Activity: Top 10 Memes by Score
        $sidebarLiveActivity = Meme::with(['user'])
            ->where('status', '!=', 'rejected')
            ->whereNull('brand_id')
            ->orderByDesc('score')
            ->limit(10)
            ->get()
            ->map(function($meme) {
                return [
                    'user' => $meme->user->name ?? 'User',
                    'title' => $meme->title ?? 'Untitled',
                    'score' => $meme->score,
                    'meme_id' => $meme->id,
                    'meme' => $meme
                ];
            })
            ->values();

    // 5. Brand Campaign Winners (Added for integration)
    $brandWinners = \App\Models\Brand::where('status', 'active')
        ->with(['memes' => function($q) {
            $q->where('status', 'active');
        }, 'memes.user', 'memes.reactions', 'memes.comments'])
        ->get()
        ->filter(function($brand) {
            return $brand->memes->count() > 0;
        })
        ->map(function($brand) {
            foreach ($brand->memes as $meme) {
                // Ensure reactions and comments are loaded and calculate score
                $meme->calculated_score = ($meme->reactions->count() * 2) + ($meme->comments->count() * 3) + (($meme->shares_count ?? 0) * 5);
            }
            return [
                'brand' => $brand,
                'winners' => $brand->memes->sortByDesc('calculated_score')->take(3)
            ];
        })
        ->take(3)
        ->all();

    $brands = \App\Models\Brand::where('status', 'active')->orderBy('start_date', 'asc')->take(5)->get();

    return compact('sidebarEndTime', 'sidebarPrizePool', 'sidebarTop3', 'sidebarLastWeekWinners', 'sidebarLiveActivity', 'brandWinners', 'brands');
    }

    public function index(Request $request)
    {
        $activeChallenge = WeeklyChallenge::current();
        $currentChallengeId = $activeChallenge->challenge_id;
        $highlightMemeId = $request->query('highlight');

        // Get top 3 memes for the leaderboard
        $leaderboard = $activeChallenge->getLeaderboard(3);

        $query = Meme::with(['reactions', 'comments.user', 'user', 'likes', 'shareEvents'])
            ->where('status', '!=', 'rejected')
            ->whereNull('brand_id');

        // Apply sorting based on request or default logic
        // Default Logic: Paid memes first, then score, then latest
        $query->orderBy('is_contest', 'desc')
              ->orderBy('contest_week_id', 'desc')
              ->orderBy('score', 'desc')
              ->orderBy('created_at', 'desc');

        $memes = $query->take(20)->get(); // Limit for efficiency, switch to paginate() if UI supports it

        // Contestant flag logic (for display/badges)
        foreach ($memes as $meme) {
            $meme->is_contestant = ($meme->is_contest === 1 && $meme->contest_week_id === $currentChallengeId);
        }

        // Add user selected emoji for each meme
        $userId = Auth::id();
        foreach ($memes as $meme) {
            $meme->userEmoji = $meme->reactions->where('user_id', $userId)->first()?->emoji;
        }

        // Load user's memes for the dropdown if authenticated
        if(Auth::check()) {
            Auth::user()->load('memes');
        }

        $sidebarData = $this->getSidebarData();
        return view('memes', array_merge(compact('memes', 'currentChallengeId', 'leaderboard', 'highlightMemeId'), $sidebarData));
    }

    // Show Pay for Meme Page
    public function payForMeme(Meme $meme)
    {
        // Check if the authenticated user is the owner of the meme
        if ($meme->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to view this page');
        }

        return view('pay-for-meme', compact('meme'));
    }

    // Show user's own memes
    public function myMemes()
    {
        $activeChallenge = WeeklyChallenge::current();
        $currentChallengeId = $activeChallenge->challenge_id;

        // Get top 3 memes for the leaderboard
        $leaderboard = $activeChallenge->getLeaderboard(3);

        $memes = Meme::where('user_id', Auth::id())
            ->with(['reactions', 'comments.user', 'user', 'likes', 'shareEvents'])
            ->orderByRaw("CASE WHEN is_contest = 1 AND contest_week_id = ? THEN 0 ELSE 1 END", [$currentChallengeId])
            ->latest()
            ->get();

        // Add user selected emoji for each meme
        $userId = Auth::id();
        foreach ($memes as $meme) {
            $meme->userEmoji = $meme->reactions->where('user_id', $userId)->first()?->emoji;
        }

        $sidebarData = $this->getSidebarData();
        return view('my-memes', array_merge(compact('memes', 'currentChallengeId', 'leaderboard'), $sidebarData));
    }

    // Home page
    public function home(Request $request)
    {
        $highlightMemeId = $request->query('highlight');
        $activeChallenge = WeeklyChallenge::current();
        $currentChallengeId = $activeChallenge->challenge_id;

        // Get top 3 memes for the leaderboard
        $leaderboard = $activeChallenge->getLeaderboard(3);

        // Trending/Contest feed: prioritize current paid contest memes, then sort by score (desc) and date
    $highlightMemeId = $request->input('highlight') ?: session('highlight_meme_id');
    
    // Filter memes from the last 30 days for home page feed
    $thirtyDaysAgo = now()->subDays(30);
    
    $query = Meme::with(['reactions', 'comments.user', 'user', 'shareEvents', 'brand'])
        ->where('status', '!=', 'rejected')
        ->whereNull('brand_id')
        ->where('created_at', '>=', $thirtyDaysAgo);

    // 1. Paid / Contest Memes ALWAYS First

        // 2. Highlighted Meme (Floats to top of its respective section)
        if ($highlightMemeId) {
            $query->orderByRaw("CASE WHEN id = ? THEN 0 ELSE 1 END ASC", [$highlightMemeId]);
        }

        // 3. Score (Highest First for Paid Memes)
        // We want Paid memes sorted by score, then Unpaid memes sorted by creation date
        // Since we already ordered by is_contest desc, we can just order by score desc for the paid block
        // However, to strictly follow "This week's uploaded paid memes... highest score at top"
        // And "Unpaid below all paid"

        // Let's rely on standard multi-column sort:
        // Level 1: Paid vs Unpaid (is_contest DESC)
        // Level 2: Contest Week (Current week first) (contest_week_id DESC)
        // Level 3: Score (DESC) -- This applies to paid memes principally
        // Level 4: Created At (DESC) -- Tie breaker

        $query->orderBy('contest_week_id', 'desc')
              ->orderBy('score', 'desc')
              ->orderBy('created_at', 'desc');

        $memes = $query->take(20)->get();

        // Add user selected emoji for each meme
        $userId = Auth::id();
        foreach ($memes as $meme) {
            $meme->userEmoji = $meme->reactions->where('user_id', $userId)->first()?->emoji;
        }

        // Load user's memes for the dropdown if authenticated
        if(Auth::check()) {
            Auth::user()->load('memes');
        }

        $sidebarData = $this->getSidebarData();
        return view('home', array_merge(compact('memes', 'currentChallengeId', 'leaderboard', 'highlightMemeId'), $sidebarData));
    }

    /**
     * API: Get current score for a meme
     */
    public function getScore(Meme $meme)
    {
        return response()->json([
            'success' => true,
            'score' => $meme->score
        ]);
    }

    public function winnersPage()
    {
        $activeChallenge = WeeklyChallenge::current();
        $currentChallengeId = $activeChallenge->challenge_id;
        $leaderboard = $activeChallenge->getLeaderboard(3);

        $pastWinners = WeeklyChallenge::where('status', 'closed')
            ->whereNotNull('winner_meme_id')
            ->with(['winnerMeme.user'])
            ->orderBy('end_at', 'desc')
            ->get();

        $sidebarData = $this->getSidebarData();
        return view('winners', array_merge(compact('currentChallengeId', 'leaderboard', 'pastWinners'), $sidebarData));
    }

    // Leaderboard JSON API
    public function getLeaderboardData()
    {
        $activeChallenge = WeeklyChallenge::current();
        
        // 1. Current Challenge Data (Timer & Prize Pool)
        $endTime = $activeChallenge && $activeChallenge->end_at 
            ? $activeChallenge->end_at->toIso8601String() 
            : null;
            
        $prizePool = $activeChallenge && $activeChallenge->prize_pool_cents 
            ? number_format($activeChallenge->prize_pool_cents / 100, 0)
            : '0';

        // 2. Top 3 Contestants
        $top3 = [];
        if ($activeChallenge) {
            $leaders = $activeChallenge->getLeaderboard(3);
            $top3 = $leaders->map(function($meme) {
                return [
                    'id' => $meme->id,
                    'title' => $meme->title,
                    'image_path' => $meme->image_path ? asset('storage/' . $meme->image_path) : null,
                    'user_id' => $meme->user_id,
                    'user_name' => $meme->user->name,
                    'user_avatar' => $meme->user->profile_photo_url,
                    'score' => $meme->score,
                ];
            });
        }

        // 3. Last Week's Winners
        $lastWeekWinners = [];
        $previousChallenge = WeeklyChallenge::previous();
        
        if ($previousChallenge) {
            if ($previousChallenge->winner_meme_id) {
                $winnerMeme = Meme::find($previousChallenge->winner_meme_id);
                if ($winnerMeme) {
                     $lastWeekWinners[] = [
                        'rank' => 1,
                        'user_name' => $winnerMeme->user->name,
                        'user_avatar' => $winnerMeme->user->profile_photo_url,
                        'prize' => '$' . number_format(($previousChallenge->prize_pool_cents * 0.50) / 100, 0),
                    ];
                }
            } else {
                $prevLeaders = $previousChallenge->getLeaderboard(1);
                foreach($prevLeaders as $leader) {
                    $lastWeekWinners[] = [
                        'rank' => 1,
                        'user_name' => $leader->user->name,
                         'user_avatar' => $leader->user->profile_photo_url,
                        'prize' => '$' . number_format(($previousChallenge->prize_pool_cents * 0.50) / 100, 0),
                    ];
                }
            }
        }

        return response()->json([
            'end_time' => $endTime,
            'prize_pool' => $prizePool,
            'top_contestants' => $top3,
            'last_week_winners' => $lastWeekWinners,
            'leaderboard' => $top3 
        ]);
    }


    // Comment on a Meme (AJAX)
    public function comment(Request $request, Meme $meme)
    {
        $request->validate([
            'content' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:meme_comments,id', // Allow parent_id for replies
        ]);

        // Check if this is a reply to another comment
        $parentId = null;
        if ($request->filled('parent_id')) {
            $parentComment = MemeComment::find($request->parent_id);

            // Verify that the parent comment belongs to the same meme
            if ($parentComment && $parentComment->meme_id == $meme->id) {
                $parentId = $parentComment->id;
            }
        }

        $comment = $meme->comments()->create([
            'user_id' => Auth::id(),
            'body' => $request->content,
            'parent_id' => $parentId, // Add parent_id if this is a reply
        ]);


    // Load the user relationship to get user info for response
    $comment->load('user');

    // Recalculate score if it's a contest meme
    if ($meme->is_contest) {
        $meme->recalculateScore();
    }

    return response()->json([
        'success' => true,
        'user' => $comment->user->name,
        'comment' => [
            'id' => $comment->id,
            'body' => $comment->body,
            'user' => [
                'id' => $comment->user->id,
                'name' => $comment->user->name,
            ],
            'parent_id' => $comment->parent_id,
        ],
        'comments_count' => $meme->comments()->count(),
        'comment_id' => $comment->id, // Legacy support
        'parent_id' => $comment->parent_id, // Legacy support
    ]);
}

    // Show single Meme - Redirect to feed with highlight (PUBLIC)
    public function show(Meme $meme)
    {
        // If meme belongs to a brand, redirect to brand page (public)
        if ($meme->brand_id) {
            return redirect()->route('brands.show', ['brand' => $meme->brand_id, 'highlight' => $meme->id])
                ->with('highlight_meme_id', $meme->id);
        }

        // Redirect to home feed with highlighted meme (works for guests too)
        return redirect()->route('home', ['highlight' => $meme->id])
            ->with('highlight_meme_id', $meme->id);
    }

    // Emoji Reaction (AJAX) - single emoji per user per meme
    public function react(Request $request, Meme $meme)
    {
        $request->validate([
            'emoji' => 'required|string|max:10',
        ]);

        $userId = Auth::id();

        // Check if user already reacted with an emoji
        $reaction = Reaction::where('user_id', $userId)
            ->where('meme_id', $meme->id)
            ->first();

        if ($reaction) {
            // Update existing emoji
            $reaction->update(['emoji' => $request->emoji]);
        } else {
            // Create new reaction
            Reaction::create([
                'user_id' => $userId,
                'meme_id' => $meme->id,
                'emoji' => $request->emoji
            ]);
        }

        // Record engagement for audit
        $engagement = EngagementAudit::recordEngagement(
            $userId,
            $meme->id,
            'like',
            $request->ip(),
            $request->userAgent()
        );
        
        // Run audit check
        EngagementAuditService::auditEngagement($engagement);

        // Fetch fresh reactions
        $meme->load('reactions'); // Reload relationship
        $totalCount = $meme->reactions->count();

        // Prepare data for the reactions line (WhatsApp style)
        $userReactions = [];
        $allReactions = $meme->reactions->groupBy('emoji');

        foreach($allReactions as $emoji => $reactions) {
            $count = count($reactions);
            $userReacted = $reactions->contains(function($reaction) use ($userId) {
                return $reaction->user_id == $userId;
            });

            $userReactions[] = [
                'emoji' => $emoji,
                'count' => $count,
                'user_reacted' => $userReacted
            ];
        }

        // Sort by user reacted first, then by count descending
        usort($userReactions, function($a, $b) {
            if ($a['user_reacted'] && !$b['user_reacted']) return -1;
            if (!$a['user_reacted'] && $b['user_reacted']) return 1;
            return $b['count'] - $a['count'];
        });

        // Build HTML string
        $reactionsHtml = '';
        foreach($userReactions as $reactionData) {
            $style = $reactionData['user_reacted'] 
                ? 'background-color: #d1ecf1; border: 1px solid #bee5eb;' 
                : 'background-color: #f8f9fa; border: 1px solid #dee2e6;';
            
            $reactionsHtml .= '<span class="user-reaction-item" style="display: flex; align-items: center; padding: 2px 6px; border-radius: 12px; margin-right: 5px; margin-bottom: 5px; ' . $style . '">';
            $reactionsHtml .= '<span class="reaction-emoji">' . $reactionData['emoji'] . '</span>';
            $reactionsHtml .= '<span class="reaction-count ms-1">' . $reactionData['count'] . '</span>';
            $reactionsHtml .= '</span>';
        }

        // Recalculate score if it's a contest meme
        if ($meme->is_contest) {
            $meme->recalculateScore();
        }

        return response()->json([
            'success' => true,
            'total_count' => $totalCount,
            'reactions_html' => $reactionsHtml
        ]);
    }

    // Show edit form for a meme
    public function edit(Meme $meme)
    {
        // Check if the authenticated user is the owner of the meme
        if ($meme->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to edit this meme');
        }

        return view('edit-meme', compact('meme'));
    }

    // Update meme
    public function update(Request $request, Meme $meme)
    {
        // Check if the authenticated user is the owner of the meme
        if ($meme->user_id !== Auth::id()) {
            abort(403, 'Unauthorized to update this meme');
        }

        $request->validate([
            'title' => 'nullable|string|max:1500',
            'image' => 'nullable|image|mimes:jpeg,png,webp|max:2048',
        ]);

        $memeData = [
        'title' => $request->input('title'),
    ];

        if ($request->hasFile('image')) {
            // Delete old image if exists
            if ($meme->image_path) {
                \Storage::disk('public')->delete($meme->image_path);
            }

            $path = $request->file('image')->store('memes', 'public');
            $memeData['image_path'] = $path;
        }

        $meme->update($memeData);

        return redirect('/')->with('success', 'Meme updated successfully!');
    }

    // Delete meme
    public function destroy(Meme $meme, Request $request)
    {
        // Check if the authenticated user is the owner of the meme
        if ($meme->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json(['success' => false, 'message' => 'Unauthorized to delete this meme'], 403);
            }
            abort(403, 'Unauthorized to delete this meme');
        }

        // Delete the meme image if it exists
        if ($meme->image_path) {
            \Storage::disk('public')->delete($meme->image_path);
        }

        $meme->delete();

        if ($request->expectsJson()) {
            return response()->json(['success' => true, 'message' => 'Meme deleted successfully!', 'meme_id' => $meme->id]);
        }

        return redirect('/')->with('success', 'Meme deleted successfully!');
    }

    // Increment share count for a meme
    public function incrementShare(Request $request, Meme $meme)
    {
        $meme->incrementShares();

        // Track share event
        ShareEvent::create([
            'meme_id' => $meme->id,
            'user_id' => Auth::id(),
            'channel' => $request->input('channel', 'copy_link'),
        ]);

        // Record engagement for audit
        $engagement = EngagementAudit::recordEngagement(
            Auth::id(),
            $meme->id,
            'share',
            $request->ip(),
            $request->userAgent()
        );
        
        // Run audit check
        EngagementAuditService::auditEngagement($engagement);

        // Recalculate score if it's a contest meme
        if ($meme->is_contest) {
            $meme->recalculateScore();
        }


        return response()->json([
            'success' => true,
            'shares_count' => $meme->shares_count
        ]);
    }

    // Dummy Payment Logic (Reverted from Stripe)
    public function dummyPayment(Meme $meme)
    {
        // Check owner
        if ($meme->user_id !== Auth::id()) {
            abort(403, 'Unauthorized');
        }

        $activeChallenge = WeeklyChallenge::current();

        // Update meme to contest status synchronously
        $meme->update([
            'is_contest' => true,
            'contest_week_id' => $activeChallenge->challenge_id,
            'entry_paid_at' => now(),
            'payment_provider' => 'dummy',
            'payment_ref' => 'dummy_' . uniqid(),
            'price_cents' => 100, // $1
        ]);

        // Create Challenge Entry
        ChallengeEntry::updateOrCreate(
            ['meme_id' => $meme->id, 'challenge_id' => $activeChallenge->challenge_id],
            [
                'user_id' => Auth::id(),
                'paid_amount_cents' => 100, // $1
                'paid_at' => now(),
                'payment_provider' => 'dummy',
                'payment_ref' => $meme->payment_ref,
            ]
        );

        // Initial score calculation within the window
        $meme->recalculateScore();

        return redirect()->route('home')
                         ->with('highlight_meme_id', $meme->id)
                         ->with('success', 'Meme entered into Weekly Challenge (Dummy Payment)');
    }

    // Meme reporting
    public function report(Request $request, Meme $meme)
    {
        $request->validate([
            'reason' => 'required|string|max:100',
            'details' => 'nullable|string|max:500',
        ]);

        Report::create([
            'meme_id' => $meme->id,
            'user_id' => Auth::id(),
            'reason' => $request->reason,
            'details' => $request->details,
        ]);

        // If many reports, mark status as reported automatically
        if ($meme->reports()->where('status', 'pending')->count() >= 3) {
            $meme->update(['status' => 'reported']);
        }

        return response()->json(['success' => true]);
    }

    public function howItWorks()
    {
        return view('how-it-works');
    }

    public function search(Request $request)
    {
        $query = $request->input('q');

        $activeChallenge = WeeklyChallenge::current();
        $currentChallengeId = $activeChallenge ? $activeChallenge->challenge_id : null;

        $memes = Meme::with(['reactions', 'comments.user', 'comments.replies.user', 'user'])
            ->where('status', '!=', 'rejected')
            ->whereNull('brand_id');

        if (!empty($query)) {
            $memes = $memes->where(function($q) use ($query) {
                $q->where('title', 'LIKE', "%{$query}%")
                  ->orWhereHas('user', function($userQuery) use ($query) {
                      $userQuery->where('name', 'LIKE', "%{$query}%");
                  })
                  ->orWhereHas('comments', function($commentQuery) use ($query) {
                      $commentQuery->where('body', 'LIKE', "%{$query}%");
                  });
            });
        }

        $memes = $memes->get();

        // Add user selected emoji for each meme
        $userId = Auth::id();
        foreach ($memes as $meme) {
            $meme->userEmoji = $meme->reactions->where('user_id', $userId)->first()?->emoji;
        }
        
        $sidebarData = $this->getSidebarData();
        $leaderboard = $activeChallenge ? $activeChallenge->getLeaderboard(3) : collect();

        // Static Page Search Logic
        $staticPages = [
            ['title' => 'Weekly Battle Arena', 'keywords' => ['battle', 'arena', 'contest', 'week', 'win', 'prize', 'money', 'cash', 'reward'], 'route' => 'memes.winners', 'desc' => 'Compete in the weekly meme battle for cash prizes!', 'icon' => 'bi-trophy'],
            ['title' => 'Upload Meme', 'keywords' => ['upload', 'create', 'new', 'post', 'submit', 'add'], 'route' => 'upload-meme.create', 'desc' => 'Submit your best meme to the platform.', 'icon' => 'bi-cloud-upload'],
            ['title' => 'How It Works', 'keywords' => ['how', 'work', 'rules', 'guide', 'help', 'faq', 'info'], 'route' => 'how-it-works', 'desc' => 'Learn how to earn money and participate.', 'icon' => 'bi-info-circle'],
            ['title' => 'Terms of Service', 'keywords' => ['terms', 'privacy', 'legal', 'condition'], 'route' => 'terms', 'desc' => 'Read our terms and conditions.', 'icon' => 'bi-file-text'],
            ['title' => 'Blogs', 'keywords' => ['blog', 'news', 'update', 'article'], 'route' => 'blogs', 'desc' => 'Read the latest updates and news.', 'icon' => 'bi-journal-text'],
        ];

        $matchedPages = collect();
        if (!empty($query)) {
            $lowerQuery = strtolower($query);
            foreach ($staticPages as $page) {
                foreach ($page['keywords'] as $keyword) {
                    // Check if query contains keyword or keyword contains query (partial match)
                    if (str_contains($lowerQuery, $keyword) || (strlen($lowerQuery) > 2 && str_contains($keyword, $lowerQuery))) {
                         $matchedPages->push((object) $page);
                         break; 
                    }
                }
            }
        }

        return view('search-results', array_merge(compact('memes', 'query', 'currentChallengeId', 'leaderboard', 'matchedPages'), $sidebarData));
    }

    public function getMemeScore($id)
    {
        // Find the meme by ID
        $meme = Meme::with(['reactions', 'comments'])->find($id);

        // If the meme doesn't exist, return a JSON response with error
        if (!$meme) {
            return response()->json(['error' => 'Meme not found'], 404);
        }

        // Calculate the score using the same logic as the winners box (reactions*2 + comments*3 + shares*5)
        $calculatedScore = ($meme->reactions->count() * 2) +
                          ($meme->comments->count() * 3) +
                          (($meme->shares_count ?? 0) * 5);

        // Return the live calculated score for visibility
        return response()->json(['score' => $calculatedScore]);
    }
}
