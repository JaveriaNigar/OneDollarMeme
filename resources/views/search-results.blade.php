@extends('layouts.app')

@section('content')
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
</style>
<div class="container my-4">
    <div class="row g-4 align-items-start">
        <!-- Main Content -->
        <div class="col-12">
            <h2 class="mb-4">Search Results for "{{ $query }}"</h2>

            @if(isset($matchedPages) && $matchedPages->count() > 0)
                <div class="mb-4">
                    <h5 class="text-muted fw-bold mb-3 small text-uppercase">Pages & Features</h5>
                    <div class="row g-3">
                        @foreach($matchedPages as $page)
                        <div class="col-md-6">
                            <a href="{{ route($page->route) }}" class="text-decoration-none">
                                <div class="card border-0 shadow-sm h-100 hover-shadow text-center p-3">
                                    <div class="mb-2">
                                        <i class="{{ $page->icon ?? 'bi-link' }} text-primary" style="font-size: 2rem;"></i>
                                    </div>
                                    <h5 class="fw-bold text-dark">{{ $page->title }}</h5>
                                    <p class="text-muted small mb-0">{{ $page->desc }}</p>
                                </div>
                            </a>
                        </div>
                        @endforeach
                    </div>
                </div>
            @endif

            @if(count($memes) > 0)
                <div class="d-flex flex-column gap-4">
                    @foreach($memes as $meme)
                        <div class="post-card meme relative p-3 bg-white rounded shadow-sm" id="meme-{{ $meme->id }}" data-id="{{ $meme->id }}" data-created-at="{{ $meme->created_at }}" data-is-contest="{{ $meme->is_contest ? 1 : 0 }}" data-score="{{ $meme->score ?? 0 }}" style="scroll-margin-top: 100px;">
                            
                            <!-- 3-dot menu for post owner -->
                            @if(auth()->check() && $meme->user_id == auth()->id())
                            <div class="position-relative">
                                <button class="menu-button btn btn-sm position-absolute" type="button" style="top: 0; right: 0; z-index: 10;" data-bs-toggle="dropdown" aria-expanded="false">
                                    ⋮
                                </button>
                                <ul class="dropdown-menu dropdown-menu-end">
                                    <li><a class="dropdown-item" href="{{ route('memes.edit', $meme) }}">Edit Post</a></li>
                                    <li>
                                        <form method="POST" action="{{ route('memes.destroy', $meme) }}">
                                            @csrf
                                            @method('DELETE')
                                            <button type="button" class="dropdown-item text-danger delete-meme-btn">Delete Post</button>
                                        </form>
                                    </li>
                                </ul>
                            </div>
                            @endif

                            <!-- User Info Profile Section -->
                            <div class="d-flex align-items-center mb-3">
                                <img src="{{ $meme->user->profile_photo_url }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $meme->user->name ?? 'Avatar' }}">
                                <div class="flex-grow-1 text-start">
                                    <span class="fw-bold small d-block text-dark">{{ $meme->user->name ?? 'Unknown' }}</span>
                                    @if($meme->is_contest)
                                        <span class="badge bg-warning text-dark small" style="font-size: 0.65rem;">🏆 CONTENDER</span>
                                    @endif
                                </div>
                            </div>

                            <div class="row mb-2">
                                <div class="col-12 text-center">
                                    @if($meme->title && $meme->title !== 'Untitled')
                                        <h6 class="meme-title fw-bold mb-2 text-start" style="white-space: pre-line;">{{ $meme->title }}</h6>
                                    @endif

                                    @if($meme->image_path)
                                        <div class="text-center rounded border overflow-hidden bg-light" style="max-height: 600px;">
                                            <img src="{{ asset('storage/'.$meme->image_path) }}"
                                                 class="img-fluid" 
                                                 style="max-height: 600px; object-fit: contain; width: 100%; display: block;">
                                        </div>
                                    @endif
                                </div>
                            </div>

                            <!-- Stats / Actions Layout -->
                            <div class="mt-3 d-flex align-items-center gap-3 border-top pt-2">
                                <!-- Smile/Reactions -->
                                <button class="react-toggle-btn btn btn-light btn-sm d-flex align-items-center gap-1 rounded-pill" type="button" data-meme-id="{{ $meme->id }}">
                                    <span class="reaction-emoji text-warning shadow-sm rounded-circle bg-white" style="font-size: 1.1rem;">{{ $meme->userEmoji ?? '😆' }}</span>
                                    <span class="reaction-count fw-bold text-dark">{{ $meme->reactions->count() }}</span>
                                </button>

                                <!-- Comments -->
                                <button class="comment-toggle-btn btn btn-light btn-sm d-flex align-items-center gap-1 rounded-pill" type="button" data-meme-id="{{ $meme->id }}">
                                    <i class="bi bi-chat-fill text-primary"></i>
                                    <span class="comment-count fw-bold text-dark">{{ $meme->comments->count() }}</span>
                                </button>
                                
                                <!-- Share -->
                                <button class="meme-share-btn btn btn-light btn-sm d-flex align-items-center gap-1 rounded-pill ms-auto" type="button" onclick="showMemeShareModal(event, {{ $meme->id }})">
                                     <i class="bi bi-share-fill text-muted"></i>
                                </button>
                                
                                @if($meme->is_contest)
                                    <span class="badge bg-warning text-dark border border-warning rounded-pill ms-1" title="Paid Meme" style="font-size: 0.9rem;">⭐</span>
                                @endif

                                <!-- View Details -->
                                @if($meme->brand_id)
                                    <a href="{{ route('brands.show', ['brand' => $meme->brand_id, 'highlight' => $meme->id]) }}#meme-{{ $meme->id }}" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                @else
                                    <a href="{{ route('home', ['highlight' => $meme->id]) }}#meme-{{ $meme->id }}" class="btn btn-sm btn-outline-primary rounded-pill">View</a>
                                @endif
                            </div>

                            <!-- User-specific reactions line -->
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

                                    usort($userReactions, function($a, $b) {
                                        if ($a['user_reacted'] && !$b['user_reacted']) return -1;
                                        if (!$a['user_reacted'] && $b['user_reacted']) return 1;
                                        return $b['count'] - $a['count'];
                                    });
                                @endphp

                                @foreach($userReactions as $reactionData)
                                    <span class="user-reaction-item"
                                          style="display: flex; align-items: center; padding: 2px 6px; border-radius: 12px; font-size: 0.8rem;
                                                 {{ $reactionData['user_reacted'] ? 'background-color: #d1ecf1; border: 1px solid #bee5eb;' : 'background-color: #f8f9fa; border: 1px solid #dee2e6;' }}">
                                        <span class="reaction-emoji">{{ $reactionData['emoji'] }}</span>
                                        <span class="reaction-count ms-1">{{ $reactionData['count'] }}</span>
                                    </span>
                                @endforeach
                            </div>

                            <!-- Emoji Section -->
                            <div id="emoji-section-{{ $meme->id }}" style="display:none;" class="emoji-reactions mt-2 text-start">
                                <emoji-picker id="emoji-picker-{{ $meme->id }}"></emoji-picker>
                            </div>

                            <!-- Comment Section -->
                            <div id="comment-section-{{ $meme->id }}" style="display:none;" class="comment-box mt-2">
                                <form class="comment-form d-flex mt-2" data-id="{{ $meme->id }}">
                                    @csrf
                                    <input type="text" name="content" class="form-control form-control-sm me-1" placeholder="Add a comment">
                                    <button type="submit" class="btn btn-sm" style="background-color: var(--brand-purple); color: white;">Comment</button>
                                </form>

                                <ul class="comment-list mt-2 list-unstyled">
                                    @foreach($meme->comments->whereNull('parent_id') as $comment)
                                        @include('partials._comment_item', ['comment' => $comment, 'meme' => $meme])
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endforeach
                </div>
            @elseif(!isset($matchedPages) || $matchedPages->count() == 0)
                <div class="alert alert-info">
                    No results found for "{{ $query }}"
                </div>
            @endif
        </div>


    </div>
</div>
@endsection

@section('scripts')
<script>
    window.loggedIn = {{ auth()->check() ? 'true' : 'false' }};
    window.userId = {{ auth()->id() ?? 'null' }};
    window.user = { id: window.userId };
</script>
<script src="{{ asset('js/memes-interactions.js') }}?v={{ time() }}"></script>
@endsection