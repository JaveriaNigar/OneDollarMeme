@php $userLoggedIn = auth()->check(); @endphp

<div class="comment-section">
    @foreach($memes as $meme)
    <div class="post-card meme" id="meme-{{ $meme->id }}" data-id="{{ $meme->id }}">
        <div class="d-flex align-items-center mb-2">
            <img src="{{ $meme->user->avatar ?? 'default.png' }}" class="avatar-circle me-2">
            <strong>{{ $meme->user->name ?? 'Unknown' }}</strong>
        </div>

        <h5>{{ $meme->title ?? 'Untitled' }}</h5>

        <div class="comment-react-container d-flex gap-2">
            <button
                class="comment-toggle"
                type="button"
                data-bs-toggle="collapse"
                data-bs-target="#comments-{{ $meme->id }}"
                onclick="@if(!$userLoggedIn) window.location='{{ route('login') }}' @endif">
                💬 <span class="comment-count">{{ $meme->comments->count() }}</span>
            </button>
            </div>


        <div class="collapse mt-2" id="comments-{{ $meme->id }}">
            <div class="comment-list" id="comment-list-{{ $meme->id }}">
                <!-- Comments loaded via AJAX -->
            </div>

            @if($userLoggedIn)
            <form class="comment-form mt-2" data-id="{{ $meme->id }}" method="POST">
                @csrf
                <input type="text" name="content" class="form-control form-control-sm" placeholder="Add a comment" required>
            </form>
            @endif
        </div>
    </div>
    @endforeach
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

.comment-item {
    position: relative;
    margin-bottom: 10px;
}

.comment-content {
    padding: 8px;
    border-radius: 4px;
    background-color: #f8f9fa;
    margin-bottom: 5px;
}

.reply-item {
    position: relative;
    margin-bottom: 10px;
}

.replies-container {
    margin-left: 20px;
    padding-left: 15px;
    border-left: 2px solid #e9ecef;
    position: relative;
}

/* For deeper nesting levels, adjust the border color or thickness */
.replies-container .replies-container {
    border-left: 2px solid #dee2e6;
}

.replies-container .replies-container .replies-container {
    border-left: 2px solid #ced4da;
}

.replies-container .replies-container .replies-container .replies-container {
    border-left: 2px solid #adb5bd;
}
</style>
<script>
    const authUserId = {{ $userLoggedIn ? auth()->id() : 'null' }};

    function loadComments(memeId) {
        $.ajax({
            url: `/api/meme/${memeId}/comments`,
            method: 'GET',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function(res) {
                if(res.success) {
                    let html = '';
                    res.comments.forEach(c => { html += renderComment(c, memeId); });
                    $(`#comment-list-${memeId}`).html(html);
                }
            },
            error: function(err) { console.error('Error loading comments:', err); }
        });
    }

    function renderComment(comment, memeId) {
        let html = `<div class="comment-item">
            <div class="comment-content" tabindex="0">
                <strong>${comment.user.name}</strong>: ${comment.body}
                <div class="comment-actions">`;

        // Show reply button for any logged-in user
        if(authUserId !== null) {
            html += `<button class="comment-action-btn reply-btn" data-comment-id="${comment.id}">Reply</button>`;
        }

        // Show copy button for all comments
        html += `<button class="comment-action-btn copy-btn" data-comment-body="${comment.body}" title="Copy comment" style="display: none;">📋</button>`;

        // Show delete button only if it's the current user's comment
        if(comment.user.id == authUserId) {
            html += `<button class="comment-action-btn delete-btn" data-comment-id="${comment.id}" title="Delete comment" style="display: none;">🗑️</button>`;
        }

        html += `</div>
            </div>
            <div class="reply-form-container" style="display: none;">
                <form class="reply-form mt-2" data-parent-id="${comment.id}" data-meme-id="${memeId}">
                    @csrf
                    <input type="hidden" name="parent_id" value="${comment.id}">
                    <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                    <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
                    <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
                </form>
            </div>
            <div class="replies-container mt-2">`;

        if(comment.replies && comment.replies.length > 0) {
            comment.replies.forEach(function(reply){
                html += renderComment(reply, memeId);
            });
        }

        html += `</div></div>`;
        return html;
    }

    $(document).ready(function() {
        $('.comment-toggle').on('shown.bs.collapse', function() {
            const memeId = $(this).data('bs-target').replace('#comments-', '');
            loadComments(memeId);
        });

        $(document).on('submit', '.comment-form', function(e){
            e.preventDefault();
            let memeId = $(this).data('id');
            let input = $(this).find('input[name="content"]');
            let content = input.val().trim();
            if(!content) return;

            $.ajax({
                url: `/api/meme/${memeId}/comments`,
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ body: content }),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res){
                    if(res.success){
                        loadComments(memeId);
                        input.val('');
                        $(`#meme-${memeId} .comment-count`).text(res.comments_count);
                    }
                },
                error: function(err){ console.error(err); }
            });
        });

        $(document).on('click', '.reply-btn', function(e){
            e.preventDefault();

            // Check if user is logged in
            @if(!$userLoggedIn)
                window.location.href = "{{ route('login') }}";
                return;
            @endif

            // Find the reply form container within the same comment-item container
            // Traverse up to find the nearest parent that contains the reply form
            let targetContainer = null;
            
            // Look for the reply form container in the closest parent
            $(this).parents().each(function() {
                const replyForm = $(this).find('.reply-form-container').first();
                if (replyForm.length) {
                    targetContainer = replyForm;
                    return false; // break the loop
                }
            });
            
            // If we couldn't find it via parents, try the direct siblings approach
            if (!targetContainer || targetContainer.length === 0) {
                targetContainer = $(this).closest('.comment-item, .reply-item').find('.reply-form-container').first();
            }
            
            // Hide all reply containers first
            $('.reply-form-container').hide();
            
            // Show the target container and focus the input
            if (targetContainer && targetContainer.length) {
                targetContainer.show().find('input[name="body"]').focus();
            }
        });

        $(document).on('click', '.cancel-reply', function(){
            $(this).closest('.reply-form-container').hide();
        });

        $(document).on('submit', '.reply-form', function(e){
            e.preventDefault();
            const form = $(this);
            const body = form.find('input[name="body"]').val().trim();
            const parentId = form.data('parent-id');
            const memeId = form.data('meme-id');
            if(!body) return alert('Enter a reply');

            $.ajax({
                url: `/api/meme/${memeId}/comments`,
                method: 'POST',
                contentType: 'application/json',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                data: JSON.stringify({ body: body, parent_id: parseInt(parentId) }),
                success: function(res){
                    if(res.success){
                        // Reload comments to reflect the new reply - this ensures proper nesting
                        loadComments(memeId);

                        form.find('input[name="body"]').val('');
                        form.hide();
                        const countEl = $(`#meme-${memeId} .comment-count`);
                        countEl.text(parseInt(countEl.text())+1);
                    } else {
                        alert('Failed to submit reply');
                    }
                },
                error: function(err){ console.error(err); alert('Error submitting reply'); }
            });
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
                // Show temporary success feedback
                const originalText = $(this).html();
                $(this).html('✓ Copied!');

                setTimeout(() => {
                    $(this).html(originalText);
                }, 2000);
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
            // Show temporary success feedback
            const originalText = $('.copy-btn:visible').html();
            $('.copy-btn:visible').html('✓ Copied!');

            setTimeout(() => {
                $('.copy-btn:visible').html(originalText);
            }, 2000);
        } catch (err) {
            console.error('Fallback copy failed: ', err);
        } finally {
            document.body.removeChild(textArea);
        }
    }
});
</script>
