@extends('layouts.app')

@section('content')
<div class="container mt-4">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card post-card" data-id="{{ $meme->id }}">
                <div class="card-body">
                    <!-- User Info Profile Section -->
                    <div class="d-flex align-items-center mb-3">
                        <img src="{{ $meme->user->profile_photo_url }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $meme->user->name ?? 'Avatar' }}">
                        <div class="flex-grow-1 text-start">
                            <span class="fw-bold small d-block text-dark">{{ $meme->user->name ?? 'Unknown' }}</span>
                            @if($meme->title && $meme->title !== 'Untitled')
                                <h4 class="fw-bold mb-0 mt-1" style="font-size: 1.1rem; color: #111; white-space: pre-line;">{{ $meme->title }}</h4>
                            @endif
                        </div>
                    </div>
                    
                    <div class="text-center">
                        <img src="{{ asset('storage/public/' . $meme->image_path) }}"
                             alt=""
                             class="img-fluid mb-3"
                             style="max-height: 500px; object-fit: contain;">
                    </div>

                    <div class="mt-3">
                        <!-- Buttons for reactions and comments -->
                        <div class="d-flex align-items-center gap-2 mt-2">
                            @php $userLoggedIn = auth()->check(); @endphp

                            <!-- React Button -->
                            <button
                                class="react-toggle-btn btn btn-sm"
                                style="border:1px solid black; background:white; width:45px; height:35px; padding:0; display:flex; align-items:center; justify-content:center;"
                                data-meme="{{ $meme->id }}">
                                <span class="reaction-emoji">{{ $meme->userEmoji ?? '😃' }}</span>
                                <span class="reaction-count" style="margin-left: 2px; font-size: 0.8em;">{{ $meme->reactions->count() }}</span>
                            </button>

                            <!-- Comment Button -->
                            <button
                                class="comment-toggle-btn btn btn-sm action-btn"
                                style="border:1px solid black; background:white; width:120px; height:35px; padding:0;"
                                data-meme="{{ $meme->id }}">
                                💬 Comment <span class="badge bg-secondary comment-count ms-1">{{ $meme->comments_count ?? $meme->comments->count() }}</span>
                            </button>

                            <!-- Share Button -->
                            <!-- Share Button -->
                            <button
                                class="meme-share-btn btn btn-sm"
                                style="border:1px solid black; background:white; width:45px; height:35px; padding:0; display:flex; align-items:center; justify-content:center;"
                                type="button"
                                onclick="showMemeShareModal(event, {{ $meme->id }})">
                                <i class="fas fa-share text-muted"></i>
                            </button>

                            @if($meme->is_contest)
                                <span class="badge bg-warning text-dark border border-warning rounded-pill ms-1" title="Paid Meme" style="font-size: 0.9rem;">⭐</span>
                            @endif
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

                        <!-- Emoji Reactions -->
                        <div class="emoji-reactions mt-2" style="display: none;">
                            <emoji-picker id="emoji-picker-{{ $meme->id }}"></emoji-picker>
                            <div class="emoji-list d-flex flex-wrap gap-2 mt-2" style="display:none !important;">
                                @php
                                    $counts = $meme->reactions->groupBy('emoji')->map(fn($c) => count($c));
                                @endphp
                                @foreach($counts as $emoji => $count)
                                    <span class="emoji-btn" data-emoji="{{ $emoji }}" style="cursor:pointer; font-size:1.3rem; border:1px solid black; padding:0 5px; background:white; border-radius:5px; height:35px; display:flex; align-items:center;">
                                        {{ $emoji }} <span class="emoji-reaction-count ms-1">{{ $count }}</span>
                                    </span>
                                @endforeach
                            </div>
                        </div>

                        <!-- Comment Box -->
                        <div class="comment-box mt-2" style="display: none;">
                            <form class="comment-form d-flex mt-2" data-id="{{ $meme->id }}">
                                @csrf
                                <input type="text" name="content" class="form-control form-control-sm me-1" placeholder="Add a comment">
                                <button type="submit" class="btn btn-sm action-btn" style="border:1px solid black; background:white; width:auto; height:35px; padding:0 10px;">Comment</button>
                            </form>

                            <ul class="comment-list mt-2 list-unstyled">
                                @foreach($meme->comments->whereNull('parent_id') as $comment)
                                    @include('partials._comment_item', ['comment' => $comment, 'meme' => $meme])
                                @endforeach
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.comment-list {
    max-height: 450px;
    overflow-y: auto;
    overflow-x: hidden;
    padding-right: 5px;
}

.comment-list::-webkit-scrollbar {
    width: 5px;
}

.comment-list::-webkit-scrollbar-track {
    background: transparent;
}

.comment-list::-webkit-scrollbar-thumb {
    background: #e0e0e0;
    border-radius: 10px;
}

.comment-list::-webkit-scrollbar-thumb:hover {
    background: #d0d0d0;
}

.comment-actions { margin-top: 5px; }
.comment-action-btn {
    background: none;
    border: none;
    color: #6c757d;
    cursor: pointer;
    font-size: 0.85em;
    padding: 2px 6px;
    margin-right: 8px;
    border-radius: 3px;
    transition: all 0.2s;
}
.comment-action-btn:hover {
    color: #000;
    background-color: #f8f9fa;
    text-decoration: none;
}
.comment-action-btn.reply-btn {
    color: #0d6efd;
}
.comment-action-btn.reply-btn:hover {
    background-color: #e7f1ff;
}
.comment-action-btn.delete-btn {
    color: #dc3545;
}
.comment-action-btn.delete-btn:hover {
    background-color: #f8d7da;
}
.reply-form-container {
    margin-top: 10px;
    padding: 10px;
    background-color: #f8f9fa;
    border-radius: 5px;
}
.comment-content {
    padding: 5px 0;
}
</style>

@endsection

@section('scripts')
<script>
    window.loggedIn = {{ auth()->check() ? 'true' : 'false' }};
    window.userId = {{ auth()->id() ?? 'null' }};
    window.user = { id: window.userId };
    window.useGlobalMemeInteractions = true;
</script>
<script src="{{ asset('js/memes-interactions.js') }}?v=1.1"></script>
@endsection
