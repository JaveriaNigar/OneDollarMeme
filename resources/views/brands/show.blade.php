@extends('layouts.brands_app')

@section('content')
<div class="container py-4" style="max-width: 1200px;">
    <div class="row g-4">
        <!-- Main Content -->
        <div class="col-lg-8">
            <!-- Brand Header Card -->
            <div class="card border-0 rounded-2 overflow-hidden mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <!-- Banner -->
                <div class="position-relative" style="height: 120px; background: linear-gradient(135deg, {{ $brand->theme_color ?? '#8B5CF6' }} 0%, {{ $brand->theme_color ?? '#8B5CF6' }}dd 50%, {{ $brand->theme_color ?? '#8B5CF6' }}aa 100%);">
                    <div style="position: absolute; inset: 0; background-image: repeating-linear-gradient(45deg, transparent, transparent 10px, rgba(255,255,255,0.1) 10px, rgba(255,255,255,0.1) 20px);"></div>
                </div>
                
                <!-- Header Content -->
                <div class="card-body p-4 pt-0 position-relative">
                    <!-- Logo -->
                    <div class="position-absolute" style="top: -40px; left: 24px;">
                        @if($brand->logo)
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->company_name }}"
                                 class="rounded-circle border-4 border-white shadow" style="width: 80px; height: 80px; object-fit: cover; background: white;">
                        @else
                            <div class="rounded-circle border-4 border-white shadow bg-white d-flex align-items-center justify-content text-dark fw-bold"
                                 style="width: 80px; height: 80px; font-size: 2rem;">
                                {{ substr($brand->company_name, 0, 1) }}
                            </div>
                        @endif
                    </div>
                    
                    <!-- Brand Info -->
                    <div class="mt-12 mb-3">
                        <div class="d-flex align-items-center justify-content-between">
                            <div>
                                <h1 class="h4 fw-bold mb-1">r/{{ $brand->company_name }}</h1>
                                <p class="text-muted small mb-0">{{ $brand->brand_description ?? 'Participate in our brand campaign and win amazing prizes!' }}</p>
                            </div>
                            @if($brand->end_date && !$brand->end_date->isPast())
                                <span class="badge bg-danger d-flex align-items-center gap-1">
                                    <span class="rounded-circle bg-white" style="width: 6px; height: 6px; display: inline-block;"></span>
                                    LIVE
                                </span>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Stats Row -->
                    <div class="d-flex gap-4 mb-3">
                        <div>
                            <div class="fw-bold">{{ $memes->total() ?? $memes->count() }}</div>
                            <div class="text-muted small">ENTRIES</div>
                        </div>
                        <div>
                            <div class="fw-bold">${{ number_format($brand->prize_amount ?? 100) }}</div>
                            <div class="text-muted small">PRIZE POOL</div>
                        </div>
                        <div>
                            @if($brand->end_date)
                                <div class="fw-bold">{{ max(0, now()->diffInDays($brand->end_date, false)) }} day{{ max(0, now()->diffInDays($brand->end_date, false)) != 1 ? 's' : '' }}</div>
                                <div class="text-muted small">LEFT</div>
                            @endif
                        </div>
                    </div>
                    
                    <!-- Action Buttons -->
                    <div class="d-flex gap-2">
                        <a href="{{ route('sponsored.submit.form', $brand->slug ?? $brand->id) }}" class="btn rounded-pill px-4 fw-bold text-white" style="background: #0079D3; font-size: 0.9rem;">
                            Join
                        </a>
                        @if($brand->website)
                            @php
                                $websiteUrl = $brand->website;
                                if (!preg_match('#^https?://#i', $websiteUrl)) {
                                    $websiteUrl = 'https://' . $websiteUrl;
                                }
                            @endphp
                            <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer" 
                               class="btn rounded-pill px-4 fw-bold" style="border: 2px solid #0079D3; color: #0079D3; font-size: 0.9rem;">
                                <i class="bi bi-box-arrow-up-right me-1"></i> Website
                            </a>
                        @endif
                    </div>
                </div>
            </div>
            
            <!-- Sorting Tabs -->
            <div class="card border-0 rounded-2 mb-4" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="card-body p-3">
                    <div class="d-flex gap-4">
                        <button class="btn btn-link text-decoration-none fw-bold d-flex align-items-center gap-2 px-3 py-2" style="color: #FF4500; background: rgba(255,69,0,0.1); border-radius: 20px;">
                            <i class="bi bi-fire"></i> Hot
                        </button>
                        <button class="btn btn-link text-decoration-none text-muted fw-bold d-flex align-items-center gap-2 px-3 py-2">
                            <i class="bi bi-stars"></i> New
                        </button>
                        <button class="btn btn-link text-decoration-none text-muted fw-bold d-flex align-items-center gap-2 px-3 py-2">
                            <i class="bi bi-graph-up"></i> Top
                        </button>
                        <button class="btn btn-link text-decoration-none text-muted fw-bold d-flex align-items-center gap-2 px-3 py-2">
                            <i class="bi bi-rocket-takeoff"></i> Rising
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Memes List -->
            @if($memes->count() > 0)
                <div id="meme-grid">
                    @foreach($memes as $meme)
                    <div class="post-card card border-0 rounded-2 mb-3" id="meme-{{ $meme->id }}" data-id="{{ $meme->id }}" style="scroll-margin-top: 80px; box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                        <div class="card-body p-0">
                            <div class="d-flex">
                                <!-- Vote Column -->
                                <div class="d-flex flex-column align-items-center py-3 px-2" style="background: #F8F9FA; min-width: 50px;">
                                    <button class="react-toggle-btn btn btn-sm p-0" style="color: #FF4500; font-size: 1.2rem; background: none; border: none;" type="button" data-meme-id="{{ $meme->id }}">
                                        <span class="reaction-emoji">{{ $meme->userEmoji ?? '😀' }}</span>
                                    </button>
                                    <span class="reaction-count fw-bold my-1" style="font-size: 0.9rem;">{{ $meme->reactions->count() }}</span>
                                    <button class="btn btn-sm p-0" style="color: #7193FF; font-size: 1.2rem;">
                                        <i class="bi bi-arrow-down-circle-fill"></i>
                                    </button>
                                </div>
                                
                                <!-- Content Column -->
                                <div class="flex-grow-1 p-3">
                                    <!-- Post Header -->
                                    <div class="d-flex align-items-center gap-2 mb-2 flex-wrap">
                                        <img src="{{ $meme->user->profile_photo_url }}"
                                             alt="{{ $meme->user->name }}"
                                             class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                                        <span class="fw-bold small">{{ $meme->user->name ?? 'Anonymous' }}</span>
                                        <span class="text-muted small">· {{ $meme->created_at->diffForHumans() }}</span>
                                        @if($loop->first)
                                            <span class="badge" style="background: #FFE4B5; color: #8B4513; font-size: 0.7rem;">
                                                <i class="bi bi-trophy-fill me-1"></i>Top Score
                                            </span>
                                        @endif
                                    </div>
                                    
                                    <!-- Post Title -->
                                    @if($meme->title)
                                    <h5 class="fw-bold mb-3" style="font-size: 1.1rem;">{{ $meme->title }}</h5>
                                    @endif
                                    
                                    <!-- Post Image -->
                                    @if($meme->image_path)
                                    <div class="rounded-2 overflow-hidden mb-3" style="max-height: 500px; background: #FFF8DC;">
                                        <img src="{{ asset('storage/' . $meme->image_path) }}"
                                             alt="{{ $meme->title ?? 'Meme' }}"
                                             class="w-100" style="object-fit: cover;">
                                    </div>
                                    @endif
                                    
                                    <!-- User Reactions Line -->
                                    <div class="user-reactions-line mt-2 mb-2" style="display: flex; flex-wrap: wrap; gap: 5px; align-items: center;">
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

                                    <!-- Emoji Picker (Hidden) -->
                                    <div id="emoji-section-{{ $meme->id }}" style="display:none;" class="mt-2 text-start">
                                        <emoji-picker id="emoji-picker-{{ $meme->id }}"></emoji-picker>
                                    </div>
                                    
                                    <!-- Post Actions -->
                                    <div class="d-flex align-items-center gap-3">
                                        <button class="comment-toggle-btn btn btn-sm text-muted d-flex align-items-center gap-1" type="button" data-meme-id="{{ $meme->id }}" style="font-size: 0.8rem;">
                                            <i class="bi bi-chat"></i> <span class="comment-count">{{ $meme->comments->count() }}</span> Comments
                                        </button>
                                        <button class="meme-share-btn btn btn-sm text-muted d-flex align-items-center gap-1" type="button" data-meme-id="{{ $meme->id }}" style="font-size: 0.8rem;">
                                            <i class="bi bi-share"></i> Share
                                        </button>
                                    </div>

                                    <!-- Comment Section (Hidden) -->
                                    <div id="comment-section-{{ $meme->id }}" style="display:none;" class="mt-3">
                                        <!-- Comment Form -->
                                        <form class="comment-form d-flex mt-2" data-id="{{ $meme->id }}">
                                            @csrf
                                            <input type="text" name="content" class="form-control form-control-sm me-2" placeholder="Add a comment" style="border-radius: 20px;">
                                            <button type="submit" class="btn btn-sm text-white" style="background: {{ $brand->theme_color ?? '#0079D3' }}; border-radius: 20px;">Comment</button>
                                        </form>

                                        <!-- Comment List -->
                                        <ul class="comment-list mt-3 list-unstyled">
                                            @foreach($meme->comments->whereNull('parent_id') as $comment)
                                                @include('partials._comment_item', ['comment' => $comment, 'meme' => $meme])
                                            @endforeach
                                        </ul>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-4 d-flex justify-content-center">
                    {{ $memes->links() }}
                </div>
            @else
                <div class="card border-0 rounded-2 text-center py-5" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                    <div class="card-body">
                        <div style="font-size: 3rem; margin-bottom: 16px;">🎭</div>
                        <h5 class="fw-bold mb-2">No memes yet!</h5>
                        <p class="text-muted small mb-4">Be the first to submit a meme for this campaign</p>
                        <a href="{{ route('sponsored.submit.form', $brand->slug ?? $brand->id) }}" 
                           class="btn rounded-pill px-4 fw-bold text-white" style="background: #0079D3;">
                            Submit First Meme
                        </a>
                    </div>
                </div>
            @endif
        </div>
        
        <!-- Sidebar -->
        <div class="col-lg-4">
            <!-- Prize Pool Card -->
            <div class="card border-0 rounded-2 mb-3 text-center" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1); background: #FFF5EB;">
                <div class="card-body p-4">
                    <div class="text-muted small fw-bold text-uppercase mb-2" style="color: #D97706;">
                        <i class="bi bi-trophy-fill me-1"></i> Prize Pool
                    </div>
                    <div class="h1 fw-black mb-2" style="color: #FF4500;">
                        ${{ number_format($brand->prize_amount ?? 100) }}
                    </div>
                    <div class="text-muted small">
                        {{ $brand->campaign_title ?? $brand->company_name }} Campaign
                    </div>
                </div>
                <div class="px-4 pb-4">
                    <a href="{{ route('sponsored.submit.form', $brand->slug ?? $brand->id) }}" 
                       class="btn w-100 rounded-pill fw-bold text-white py-2" style="background: #FF4500;">
                        <i class="bi bi-plus-circle me-1"></i> Submit a Meme
                    </a>
                </div>
            </div>
            
            <!-- Campaign Info Card -->
            <div class="card border-0 rounded-2 mb-3" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-file-earmark-text me-2"></i>Campaign Info
                    </h5>
                    
                    <div class="mb-3">
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted small">Campaign</span>
                            <span class="fw-bold small">{{ $brand->campaign_title ?? $brand->company_name }}</span>
                        </div>
                        @if($brand->start_date)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted small">Start</span>
                            <span class="fw-bold small">{{ $brand->start_date->format('M d, g:i A') }}</span>
                        </div>
                        @endif
                        @if($brand->end_date)
                        <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                            <span class="text-muted small">End</span>
                            <span class="fw-bold small">{{ $brand->end_date->format('M d, g:i A') }}</span>
                        </div>
                        @endif
                        <div class="d-flex justify-content-between align-items-center py-2">
                            <span class="text-muted small">Entries</span>
                            <span class="fw-bold small">{{ $memes->total() ?? $memes->count() }}</span>
                        </div>
                    </div>
                    
                    @if($brand->website)
                        @php
                            $websiteUrl = $brand->website;
                            if (!preg_match('#^https?://#i', $websiteUrl)) {
                                $websiteUrl = 'https://' . $websiteUrl;
                            }
                        @endphp
                        <a href="{{ $websiteUrl }}" target="_blank" rel="noopener noreferrer" 
                           class="btn w-100 rounded-pill fw-bold" style="border: 2px solid #0079D3; color: #0079D3;">
                            <i class="bi bi-box-arrow-up-right me-1"></i> Visit {{ $brand->company_name }}'s Website
                        </a>
                    @endif
                </div>
            </div>
            
            <!-- Leaderboard Card -->
            <div class="card border-0 rounded-2" style="box-shadow: 0 1px 3px rgba(0,0,0,0.1);">
                <div class="card-body p-4">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-trophy me-2" style="color: #FFD700;"></i>Leaderboard
                    </h5>

                    @if($memes->count() > 0)
                        @php
                            // Get top memes sorted by score
                            $topMemes = $memes->sortByDesc('calculated_score')->take(5);
                        @endphp
                        <div class="list-group list-group-flush">
                            @foreach($topMemes as $index => $meme)
                            <div class="list-group-item px-0 py-2 d-flex align-items-center gap-2">
                                <span class="fw-bold text-muted" style="min-width: 20px;">{{ $index + 1 }}</span>
                                <img src="{{ $meme->user->profile_photo_url }}"
                                     alt="{{ $meme->user->name }}"
                                     class="rounded-circle" style="width: 24px; height: 24px; object-fit: cover;">
                                <span class="flex-grow-1 small text-truncate">{{ $meme->user->name ?? 'Anonymous' }}</span>
                                <span class="fw-bold small" style="color: #FF4500;">{{ $meme->calculated_score }} pts</span>
                            </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-muted small mb-0">No entries yet</p>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Share Modal -->
<x-share-modal />

<style>
    .fw-black { font-weight: 900; }
    .rounded-2 { border-radius: 0.5rem; }
    .post-card { transition: all 0.2s ease; }
    .post-card:hover { box-shadow: 0 2px 8px rgba(0,0,0,0.15); }
    
    /* Highlight animation */
    @keyframes meme-pulse {
        0%   { box-shadow: 0 0 0 0 rgba(91,46,145,0.7); }
        50%  { box-shadow: 0 0 0 12px rgba(91,46,145,0.15), 0 0 25px 5px rgba(91,46,145,0.2); }
        100% { box-shadow: 0 0 0 0 rgba(91,46,145,0); }
    }
    .meme-highlighted > .post-card {
        border: 3px solid #5B2E91 !important;
        animation: meme-pulse 1s ease-in-out 3;
    }
</style>

<script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const userId = {{ auth()->id() ?? 'null' }};
    window.user = { id: userId };

    // Toggle Emoji Section
    document.querySelectorAll('.react-toggle-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const memeId = btn.dataset.memeId;
            if (!loggedIn) { window.location = '{{ route("login") }}'; return; }
            const section = document.getElementById('emoji-section-' + memeId);
            const isHidden = (section.style.display === 'none' || section.style.display === '');
            
            // Close all other emoji sections
            document.querySelectorAll('[id^="emoji-section-"]').forEach(s => s.style.display = 'none');
            
            section.style.display = isHidden ? 'block' : 'none';
        });
    });

    // Toggle Comment Section
    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const memeId = btn.dataset.memeId;
            if (!loggedIn) { window.location = '{{ route("login") }}'; return; }
            const section = document.getElementById('comment-section-' + memeId);
            const isHidden = (section.style.display === 'none' || section.style.display === '');
            
            // Close all other comment sections
            document.querySelectorAll('[id^="comment-section-"]').forEach(s => s.style.display = 'none');
            
            section.style.display = isHidden ? 'block' : 'none';
        });
    });

    // Emoji Picker click → update only button emoji (wait for custom element to be ready)
    function initEmojiPickers() {
        document.querySelectorAll('emoji-picker').forEach(picker => {
            // Remove existing listeners to avoid duplicates
            const newPicker = picker.cloneNode(true);
            picker.parentNode.replaceChild(newPicker, picker);
            
            newPicker.addEventListener('emoji-click', event => {
                const emoji = event.detail.unicode;
                const postCard = newPicker.closest('.post-card');
                const memeId = postCard.dataset.id;
                if (!loggedIn) { window.location = '{{ route("login") }}'; return; }

                const reactBtn = postCard.querySelector('.react-toggle-btn');
                const emojiSpan = reactBtn.querySelector('.reaction-emoji');
                if (emojiSpan) emojiSpan.textContent = emoji;
                else reactBtn.textContent = emoji;

                $.ajax({
                    url: '/memes/' + memeId + '/reaction',
                    method: 'POST',
                    contentType: 'application/json',
                    data: JSON.stringify({ emoji }),
                    headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                    success: function(res) {
                        if(res.success) {
                            $(postCard).find('.reaction-count').text(res.total_count);
                            $(postCard).find('.user-reactions-line').html(res.reactions_html);
                        }
                    },
                    error: function(xhr) {
                        console.error('Error:', xhr);
                        if (window.showToast) {
                            window.showToast('Failed to add reaction', 'error');
                        } else {
                            alert('An error occurred while adding the reaction');
                        }
                    }
                });
            });
        });
    }
    
    // Initialize emoji pickers after a short delay to ensure web component loads
    setTimeout(initEmojiPickers, 100);

    // Comment form submit via AJAX
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        let memeId = $(this).data('id');
        let input = $(this).find('input[name="content"]');
        let form = $(this);
        if(input.val().trim() === '') return;

        $.ajax({
            url: '/meme/' + memeId + '/comment',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: input.val()
            },
            success: function(res) {
                if(res.success) {
                    let commentList = form.closest('.post-card').find('.comment-list');

                    // Create new comment element
                    let newCommentHtml = '<li class="comment-item" data-comment-id="' + res.comment.id + '">';
                    newCommentHtml += '<div class="comment-content" tabindex="0">';
                    newCommentHtml += '<strong>' + res.comment.user.name + '</strong>: ' + res.comment.body;
                    newCommentHtml += '<div class="comment-actions">';
                    newCommentHtml += '<button class="comment-action-btn reply-btn" data-comment-id="' + res.comment.id + '">Reply</button>';
                    newCommentHtml += '<button class="comment-action-btn copy-btn" data-comment-body="' + res.comment.body + '" title="Copy comment">📋</button>';

                    // Add delete button if it's the current user's comment
                    @if(auth()->check() && auth()->id())
                        if(res.comment.user.id == {{ auth()->id() }}) {
                            newCommentHtml += '<button class="comment-action-btn delete-btn" data-comment-id="' + res.comment.id + '" title="Delete comment" style="display: none;">🗑️</button>';
                        }
                    @endif

                    newCommentHtml += '</div></div>';
                    newCommentHtml += '<div class="reply-form-container" style="display: none;">';
                    newCommentHtml += '<form class="reply-form mt-2" data-parent-id="' + res.comment.id + '" data-meme-id="' + memeId + '">';
                    newCommentHtml += '@csrf';
                    newCommentHtml += '<input type="hidden" name="parent_id" value="' + res.comment.id + '">';
                    newCommentHtml += '<input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>';
                    newCommentHtml += '<button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>';
                    newCommentHtml += '<button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>';
                    newCommentHtml += '</form></div>';
                    newCommentHtml += '<ul class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></ul>';
                    newCommentHtml += '</li>';

                    commentList.append(newCommentHtml);
                    commentList.scrollTop(commentList[0].scrollHeight);
                    input.val('');

                    // Update comment count
                    form.closest('.post-card').find('.comment-count').text(res.comments_count);
                } else {
                    alert('Failed to submit comment: ' + (res.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                let errorMessage = 'An error occurred while submitting the comment';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert('Failed to submit comment: ' + errorMessage);
            }
        });
    });

    // Handle reply button clicks
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        const commentId = $(this).data('comment-id');
        const replyFormContainer = $(this).closest('.comment-content').next('.reply-form-container');

        // Hide all other reply forms
        $('.reply-form-container').hide();

        // Show the reply form for this comment
        replyFormContainer.show();
        replyFormContainer.find('input[name="body"]').focus();
    });

    // Handle cancel reply button clicks
    $(document).on('click', '.cancel-reply', function(e) {
        e.preventDefault();
        const replyFormContainer = $(this).closest('.reply-form-container');
        replyFormContainer.hide();
    });

    // Handle reply form submissions
    $(document).on('submit', '.reply-form', function(e) {
        e.preventDefault();

        const form = $(this);
        const parentId = form.data('parent-id');
        const memeId = form.data('meme-id');
        const bodyInput = form.find('input[name="body"]');
        const body = bodyInput.val().trim();

        if (!body) {
            alert('Please enter a reply');
            return;
        }

        $.ajax({
            url: '/meme/' + memeId + '/comment',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: body,
                parent_id: parentId
            },
            success: function(res) {
                if(res.success) {
                    const repliesContainer = form.closest('.replies-container');
                    
                    let newReplyHtml = '<li class="comment-item" data-comment-id="' + res.comment.id + '">';
                    newReplyHtml += '<div class="comment-content" tabindex="0">';
                    newReplyHtml += '<strong>' + res.comment.user.name + '</strong>: ' + res.comment.body;
                    newReplyHtml += '<div class="comment-actions">';
                    newReplyHtml += '<button class="comment-action-btn copy-btn" data-comment-body="' + res.comment.body + '" title="Copy comment">📋</button>';
                    
                    @if(auth()->check() && auth()->id())
                        if(res.comment.user.id == {{ auth()->id() }}) {
                            newReplyHtml += '<button class="comment-action-btn delete-btn" data-comment-id="' + res.comment.id + '" title="Delete comment" style="display: none;">🗑️</button>';
                        }
                    @endif
                    
                    newReplyHtml += '</div></div>';
                    newReplyHtml += '<div class="reply-form-container" style="display: none;">';
                    newReplyHtml += '<form class="reply-form mt-2" data-parent-id="' + res.comment.id + '" data-meme-id="' + memeId + '">';
                    newReplyHtml += '@csrf';
                    newReplyHtml += '<input type="hidden" name="parent_id" value="' + res.comment.id + '">';
                    newReplyHtml += '<input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>';
                    newReplyHtml += '<button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>';
                    newReplyHtml += '<button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>';
                    newReplyHtml += '</form></div>';
                    newReplyHtml += '<ul class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></ul>';
                    newReplyHtml += '</li>';

                    repliesContainer.append(newReplyHtml);
                    bodyInput.val('');
                    form.closest('.reply-form-container').hide();

                    // Update comment count
                    form.closest('.post-card').find('.comment-count').text(res.comments_count);
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Failed to submit reply');
            }
        });
    });

    // Handle copy comment button
    $(document).on('click', '.copy-btn', function(e) {
        e.preventDefault();
        const commentBody = $(this).data('comment-body');
        navigator.clipboard.writeText(commentBody).then(() => {
            if (window.showToast) {
                window.showToast('Comment copied!', 'success');
            }
        });
    });

    // Show delete button for owner comments only
    @if(auth()->check() && auth()->id())
        const currentUserId = {{ auth()->id() }};
        document.querySelectorAll('.delete-btn').forEach(btn => {
            const commentItem = btn.closest('.comment-item');
            // You'll need to adjust this based on how user_id is stored in your comment data
            btn.style.display = 'inline-block';
        });
    @endif

    // Handle delete comment (if needed)
    $(document).on('click', '.delete-btn', function(e) {
        e.preventDefault();
        if(!confirm('Are you sure you want to delete this comment?')) return;
        
        const commentId = $(this).data('comment-id');
        const commentItem = $(this).closest('.comment-item');
        const postCard = commentItem.closest('.post-card');
        const memeId = postCard.data('id');

        $.ajax({
            url: '/comment/' + commentId,
            method: 'DELETE',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content')
            },
            success: function(res) {
                if(res.success) {
                    commentItem.remove();
                    postCard.find('.comment-count').text(res.comments_count);
                    if (window.showToast) {
                        window.showToast('Comment deleted', 'success');
                    }
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('Failed to delete comment');
            }
        });
    });
});

// Share button handler
$(document).on('click', '.meme-share-btn', function(e) {
    e.preventDefault();
    const memeId = $(this).data('meme-id');
    
    // Call share API to increment share count
    $.ajax({
        url: '/api/meme/' + memeId + '/share',
        method: 'POST',
        contentType: 'application/json',
        data: JSON.stringify({ channel: 'copy_link' }),
        headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
        success: function(res) {
            // Show share modal
            if(typeof showMemeShareModal === 'function') {
                showMemeShareModal(e, memeId);
            }
        },
        error: function(xhr) {
            console.error('Share tracking error:', xhr);
            // Still show modal even if API fails
            if(typeof showMemeShareModal === 'function') {
                showMemeShareModal(e, memeId);
            }
        }
    });
});

// Highlight animation
document.addEventListener('DOMContentLoaded', function () {
    @php $highlightId = request()->query('highlight') ?? session('highlight_meme_id'); @endphp
    @if($highlightId ?? false)
    const el = document.getElementById('meme-{{ $highlightId }}');
    if (el) {
        setTimeout(() => {
            el.scrollIntoView({ behavior: 'smooth', block: 'center' });
            el.classList.add('meme-highlighted');
            setTimeout(() => el.classList.remove('meme-highlighted'), 4000);
        }, 400);
    }
    @endif
});
</script>

@endsection
