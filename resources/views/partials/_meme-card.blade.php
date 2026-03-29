<div class="post-card" id="meme-{{ $meme->id }}">
    <div class="d-flex align-items-center mb-3">
        <img src="{{ $meme->user->profile_photo_url }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $meme->user->name ?? 'Avatar' }}">
        <div class="flex-grow-1 text-start">
            <span class="fw-bold small d-block">{{ $meme->user->name ?? 'Unknown' }}</span>
        </div>
    </div>

    @if($meme->title && $meme->title !== 'Untitled')
        <h5 style="white-space: pre-line;">{{ $meme->title }}</h5>
    @endif

    <img src="{{ asset('storage/'.$meme->image_path) }}" class="img-fluid rounded mt-2">

    <div class="mt-2 d-flex align-items-center gap-2">
        <!-- Emoji Breeze reactions -->
        <div class="emoji-reactions" data-meme-id="{{ $meme->id }}"></div>

        <!-- Comment Button -->
        <button class="btn btn-sm action-btn comment-toggle" type="button" data-bs-toggle="collapse" data-bs-target="#comments-{{ $meme->id }}" onclick="@if(!$userLoggedIn) window.location='{{ route('login') }}' @endif">
            💬 <span class="comment-count">{{ $meme->comments->count() }}</span>
        </button>
    </div>

    <!-- Comment List -->
    <div class="collapse mt-2" id="comments-{{ $meme->id }}">
        <div class="comment-list">
            @foreach($meme->comments->whereNull('parent_id') as $comment)
                <div class="comment-item border-bottom pb-1 mb-1" data-comment-id="{{ $comment->id }}">
                    <div class="comment-content">
                        <strong>{{ $comment->user->name ?? 'Unknown' }}</strong>: {{ $comment->body }}
                        <div class="comment-actions">
                            <button class="comment-action-btn reply-btn" data-comment-id="{{ $comment->id }}">Reply</button>
                            <button class="comment-action-btn copy-btn" data-comment-body="{{ $comment->body }}" title="Copy comment" style="display: none;">📋</button>
                            @if(auth()->check() && $comment->user_id == auth()->id())
                                <button class="comment-action-btn delete-btn" data-comment-id="{{ $comment->id }}" title="Delete comment" style="display: none;">🗑️</button>
                            @endif
                        </div>
                    </div>
                    <div class="reply-form-container" style="display: none;">
                        <form class="reply-form mt-2" data-parent-id="{{ $comment->id }}" data-meme-id="{{ $meme->id }}">
                            @csrf
                            <input type="hidden" name="parent_id" value="{{ $comment->id }}">
                            <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                            <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
                            <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
                        </form>
                    </div>
                    <div class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;">
                        @foreach($comment->replies as $reply)
                            <div class="reply-item" data-comment-id="{{ $reply->id }}">
                                <div class="comment-content">
                                    <strong>{{ $reply->user->name ?? 'Unknown' }}</strong>: {{ $reply->body }}
                                    <div class="comment-actions">
                                        <button class="comment-action-btn reply-btn" data-comment-id="{{ $reply->id }}">Reply</button>
                                        <button class="comment-action-btn copy-btn" data-comment-body="{{ $reply->body }}" title="Copy reply" style="display: none;">📋</button>
                                        @if(auth()->check() && $reply->user_id == auth()->id())
                                            <button class="comment-action-btn delete-btn" data-comment-id="{{ $reply->id }}" title="Delete reply" style="display: none;">🗑️</button>
                                        @endif
                                    </div>
                                </div>
                                <div class="reply-form-container" style="display: none;">
                                    <form class="reply-form mt-2" data-parent-id="{{ $reply->id }}" data-meme-id="{{ $meme->id }}">
                                        @csrf
                                        <input type="hidden" name="parent_id" value="{{ $reply->id }}">
                                        <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                                        <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
                                        <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
                                    </form>
                                </div>
                                <div class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;">
                                    @foreach($reply->replies as $nestedReply)
                                        <div class="reply-item" data-comment-id="{{ $nestedReply->id }}">
                                            <div class="comment-content">
                                                <strong>{{ $nestedReply->user->name ?? 'Unknown' }}</strong>: {{ $nestedReply->body }}
                                                <div class="comment-actions">
                                                    <button class="comment-action-btn reply-btn" data-comment-id="{{ $nestedReply->id }}">Reply</button>
                                                    <button class="comment-action-btn copy-btn" data-comment-body="{{ $nestedReply->body }}" title="Copy reply" style="display: none;">📋</button>
                                                    @if(auth()->check() && $nestedReply->user_id == auth()->id())
                                                        <button class="comment-action-btn delete-btn" data-comment-id="{{ $nestedReply->id }}" title="Delete reply" style="display: none;">🗑️</button>
                                                    @endif
                                                </div>
                                            </div>
                                            <div class="reply-form-container" style="display: none;">
                                                <form class="reply-form mt-2" data-parent-id="{{ $nestedReply->id }}" data-meme-id="{{ $meme->id }}">
                                                    @csrf
                                                    <input type="hidden" name="parent_id" value="{{ $nestedReply->id }}">
                                                    <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                                                    <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
                                                    <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
                                                </form>
                                            </div>
                                            <div class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></div>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            @endforeach
        </div>

        <form class="comment-form mt-2" data-id="{{ $meme->id }}" method="POST">
            @csrf
            <input type="text" name="content" class="form-control form-control-sm" placeholder="Add a comment">
        </form>
    </div>
</div>

<script>
$(document).ready(function() {
    const userId = {{ auth()->id() ?? 'null' }};
    window.user = { id: userId };

    // Guest redirect for comment input or toggle
    $(document).on('focus click', '.comment-form input[name="content"], .comment-toggle', function() {
        @if(!Auth::check())
            window.location.href = "{{ route('login') }}";
        @endif
    });

    // AJAX Comment Submission
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        let memeId = $(this).data('id');
        let input = $(this).find('input[name="content"]');
        let content = input.val();
        let form = $(this);
        if(content.trim() === '') return;

        $.ajax({
            url: `/api/meme/${memeId}/comments`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ body: content }),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if(res.success) {
                    let commentList = form.closest('.collapse').find('.comment-list');

                    // Create new comment element with reply/delete functionality
                    let newCommentHtml = '<div class="comment-item border-bottom pb-1 mb-1" data-comment-id="' + res.comment.id + '">';
                    newCommentHtml += '<div class="comment-content">';
                    newCommentHtml += '<strong>' + res.comment.user.name + '</strong>: ' + res.comment.body;
                    newCommentHtml += '<div class="comment-actions">';

                    // Only show reply button if user is logged in
                    @if(auth()->check())
                        newCommentHtml += '<button class="comment-action-btn reply-btn" data-comment-id="' + res.comment.id + '">Reply</button>';
                    @endif

                    // Add delete button if it's the current user's comment
                    @if(auth()->check() && auth()->id())
                        if(res.comment.user.id == {{ auth()->id() }}) {
                            newCommentHtml += '<button class="comment-action-btn delete-btn" data-comment-id="' + res.comment.id + '">Delete</button>';
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
                    newCommentHtml += '<div class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></div>';
                    newCommentHtml += '</div>';

                    commentList.append(newCommentHtml);
                    commentList.scrollTop(commentList[0].scrollHeight);
                    input.val('');
                    form.closest('.post-card').find('.comment-count').text(res.comments_count);
                } else {
                    if (window.showToast) window.showToast(res.message || 'Failed to submit comment', 'error');
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
                if (window.showToast) {
                    window.showToast(errorMessage, 'error');
                }
            }
        });
    });

    // Handle reply button clicks
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();

        // Check if user is logged in
        @if(!auth()->check())
            window.location.href = "{{ route('login') }}";
            return;
        @endif

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
            if (window.showToast) window.showToast('Please enter a reply', 'error');
            return;
        }

        $.ajax({
            url: `/api/meme/${memeId}/comments`,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({
                body: body,
                parent_id: parseInt(parentId)
            }),
            success: function(data) {
                if (data.success) {
                    // Create new reply element with reply button and sub-container
                    let newReplyHtml = '<div class="reply-item" data-comment-id="' + data.comment.id + '">';
                    newReplyHtml += '<div class="comment-content">';
                    newReplyHtml += '<strong>' + data.comment.user.name + '</strong>: ' + data.comment.body;
                    newReplyHtml += '<div class="comment-actions">';
                    newReplyHtml += '<button class="comment-action-btn reply-btn" data-comment-id="' + data.comment.id + '">Reply</button>';
                    newReplyHtml += '<button class="comment-action-btn copy-btn" data-comment-body="' + data.comment.body + '" title="Copy reply">📋</button>';

                    if (data.comment.user.id == {{ auth()->id() ?? 'null' }}) {
                        newReplyHtml += '<button class="comment-action-btn delete-btn" data-comment-id="' + data.comment.id + '" title="Delete reply">🗑️</button>';
                    }

                    newReplyHtml += '</div></div>';
                    newReplyHtml += '<div class="reply-form-container" style="display: none;">';
                    newReplyHtml += '<form class="reply-form mt-2" data-parent-id="' + data.comment.id + '" data-meme-id="' + memeId + '">';
                    newReplyHtml += '@csrf';
                    newReplyHtml += '<input type="hidden" name="parent_id" value="' + data.comment.id + '">';
                    newReplyHtml += '<input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>';
                    newReplyHtml += '<button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>';
                    newReplyHtml += '<button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>';
                    newReplyHtml += '</form></div>';
                    newReplyHtml += '<div class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></div>';
                    newReplyHtml += '</div>';

                    // Find the replies container for this parent comment
                    const parentCommentElement = $(`[data-comment-id="${parentId}"]`);
                    const repliesContainer = parentCommentElement.find('.replies-container').first();
                    repliesContainer.append(newReplyHtml);

                    // Clear and hide the reply form
                    bodyInput.val('');
                    form.closest('.reply-form-container').hide();

                    // Update comment count
                    const countElement = $(`#meme-${memeId} .comment-count`);
                    if (countElement.length) {
                        let currentCount = parseInt(countElement.text()) || 0;
                        countElement.text(currentCount + 1);
                    }
                } else {
                    if (window.showToast) window.showToast(data.message || 'Failed to submit reply', 'error');
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                if (window.showToast) window.showToast('An error occurred while submitting the reply', 'error');
            }
        });
    });

    // Handle delete button clicks



    // Emoji Breeze
    document.querySelectorAll('.emoji-reactions').forEach(el => {
        const memeId = el.dataset.memeId;
        new EmojiBreeze(el, {
            emojis: ['😍','😂','😢','🔥','👍'],
            onSelect: function(emoji) {
                fetch('/react', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '{{ csrf_token() }}'
                    },
                    body: JSON.stringify({ meme_id: memeId, emoji: emoji })
                }).catch(err => console.error(err));
            }
        });
    });

    // Handle comment content focus/blur for delete and copy button visibility
    $(document).on('focus', '.comment-content', function() {
        // Show delete button for owner comments only
        const commentItem = $(this).closest('.comment-item, .reply-item');
        const deleteBtn = commentItem.find('.delete-btn');
        if (deleteBtn.length) {
            deleteBtn.show();
        }

        // Show copy button for all comments
        const copyBtn = commentItem.find('.copy-btn');
        if (copyBtn.length) {
            copyBtn.show();
        }
    });

    $(document).on('blur', '.comment-content', function() {
        // Hide delete button after a delay to allow for clicking
        const commentItem = $(this).closest('.comment-item, .reply-item');
        const deleteBtn = commentItem.find('.delete-btn');
        if (deleteBtn.length) {
            setTimeout(() => {
                deleteBtn.hide();
            }, 300);
        }

        // Hide copy button after a delay to allow for clicking
        const copyBtn = commentItem.find('.copy-btn');
        if (copyBtn.length) {
            setTimeout(() => {
                copyBtn.hide();
            }, 300);
        }
    });

    // Double-click to show delete and copy buttons
    $(document).on('dblclick', '.comment-content', function() {
        const commentItem = $(this).closest('.comment-item, .reply-item');
        const deleteBtn = commentItem.find('.delete-btn');
        if (deleteBtn.length) {
            deleteBtn.show();

            // Hide after delay if not clicked
            setTimeout(() => {
                deleteBtn.hide();
            }, 3000);
        }

        // Show copy button on double-click
        const copyBtn = commentItem.find('.copy-btn');
        if (copyBtn.length) {
            copyBtn.show();

            // Hide after delay if not clicked
            setTimeout(() => {
                copyBtn.hide();
            }, 3000);
        }
    });

    // Copy comment functionality
    $(document).on('click', '.copy-btn', function(e) {
        e.preventDefault();
        const commentBody = $(this).data('comment-body');

        // Modern clipboard API approach
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(commentBody).then(function() {
                if (window.showToast) {
                    window.showToast('Comment copied!', 'success');
                }
            }).catch(function(err) {
                console.error('Clipboard API failed: ', err);
                fallbackCopyTextToClipboard(commentBody);
            });
        } else {
            // Fallback for older browsers
            fallbackCopyTextToClipboard(commentBody);
        }
    });

    // Fallback function for copying text
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            if (window.showToast) {
                window.showToast('Comment copied!', 'success');
            }
        } catch (err) {
            console.error('Fallback copy failed: ', err);
        } finally {
            document.body.removeChild(textArea);
        }
    }
});
</script>

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
