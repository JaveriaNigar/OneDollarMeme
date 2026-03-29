@php $userLoggedIn = auth()->check(); @endphp

<div class="emoji-section">
    @foreach($memes as $meme)
    <div class="post-card meme" id="meme-{{ $meme->id }}" data-id="{{ $meme->id }}">
        <div class="d-flex align-items-center mb-2">
            <img src="{{ $meme->user->avatar ?? 'default.png' }}" class="avatar-circle me-2">
            <strong>{{ $meme->user->name ?? 'Unknown' }}</strong>
        </div>

        <h5>{{ $meme->title ?? 'Untitled' }}</h5>

        <div class="comment-react-container">
            <button
                class="react-toggle-btn"
                onclick="@if($userLoggedIn) toggleEmoji({{ $meme->id }}) @else window.location='{{ route('login') }}' @endif">
                {{ $meme->userEmoji ?? '😃' }}
            </button>
        </div>

        <!-- User-specific reactions line (WhatsApp style) -->
        <div class="user-reactions-line mt-2" style="display: flex; flex-wrap: wrap; gap: 5px; align-items: center;">
            @php
                $userReactions = [];
                $allReactions = $meme->reactions->groupBy('emoji');

                foreach($allReactions as $emoji => $reactions) {
                    $count = count($reactions);
                    $userReacted = $reactions->contains(function($reaction) {
                        return $reaction->user_id == auth()->id();
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
            @endphp

            @foreach($userReactions as $reactionData)
                <span class="user-reaction-item"
                      style="display: flex; align-items: center; padding: 2px 6px; border-radius: 12px;
                             {{ $reactionData['user_reacted'] ? 'background-color: #d1ecf1; border: 1px solid #bee5eb;' : 'background-color: #f8f9fa; border: 1px solid #dee2e6;' }}">
                    <span class="reaction-emoji">{{ $reactionData['emoji'] }}</span>
                    <span class="reaction-count ms-1">{{ $reactionData['count'] }}</span>
                </span>
            @endforeach
        </div>

        <div class="emoji-reactions mt-2 d-none" id="emoji-box-{{ $meme->id }}">
            <emoji-picker id="emoji-picker-{{ $meme->id }}"></emoji-picker>
            <div class="emoji-list mt-1 d-flex flex-wrap gap-1">
                @php
                    $counts = $meme->reactions->groupBy('emoji')->map(fn($c) => count($c));
                @endphp
                @foreach($counts as $emoji => $count)
                    <span class="emoji-btn" data-emoji="{{ $emoji }}">{{ $emoji }} <span class="emoji-reaction-count">{{ $count }}</span></span>
                @endforeach
            </div>
        </div>
    </div>
    @endforeach
</div>

<style>
.emoji-section .post-card { background: white; border-radius: 1rem; padding: 1rem; margin-bottom: 1rem; box-shadow: 0 2px 6px rgba(0,0,0,0.1); }
.emoji-section .avatar-circle { width: 40px; height: 40px; border-radius: 50%; object-fit: cover; }
.comment-react-container { display: flex; align-items: center; gap: 0.25rem; margin-top: 5px; }
.react-toggle-btn { cursor: pointer; margin-right: 0.5rem; border: 1px solid black; background: inherit; padding: 0 8px; font-size: 1rem; height: 35px; }
.react-toggle-btn:hover { background: inherit; }
.emoji-reactions { margin-top: 10px; display: flex; gap: 5px; flex-direction: column; }
.emoji-list { display: none; }
.emoji-reaction-count { font-size: 0.85rem; margin-left: 2px; }
.user-reactions-line { margin-top: 10px; }
.user-reaction-item { font-size: 1.1rem; }
</style>
