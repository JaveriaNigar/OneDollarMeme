/**
 * Memes Interactions JS
 * Handles share modal and other meme-related interactions.
 */
console.log('Meme interactions loaded');

function showMemeShareModal(event, memeId) {
    if (event) {
        event.preventDefault();
        event.stopPropagation();
    }

    // Get the meme URL
    // Construct absolute URL based on current origin
    const memeUrl = window.location.origin + '/memes/' + memeId;
    const memeTitle = 'Check out this meme on OneDollarMeme!';

    // Create the share modal HTML with inline styles to ensure functionality across Bootstrap/Tailwind pages
    const shareOptions = `
        <div id="share-modal" style="
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
        ">
            <div style="
                background: white;
                border-radius: 0.5rem;
                padding: 1.5rem;
                width: 90%;
                max-width: 24rem;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            ">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1rem;">
                    <h3 style="font-size: 1.125rem; font-weight: 700; color: #000; margin: 0;">Share Meme</h3>
                    <button onclick="closeShareModal()" style="color: #6b7280; background: transparent; border: none; cursor: pointer; font-size: 1.25rem;">
                        <i class="fas fa-times"></i>
                    </button>
                    <!-- Fallback close button if generic icon fails -->
                    <button onclick="closeShareModal()" style="display: none; color: #6b7280;">✕</button>
                </div>
                
                <div style="display: flex; flex-direction: column; gap: 0.5rem; max-height: 400px; overflow-y: auto; padding-right: 5px;" class="custom-scrollbar">
                    <!-- Copy Link -->
                    <button onclick="copyMemeLink('${memeUrl}')" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        background: transparent;
                        border: none;
                        border-radius: 0.5rem;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fas fa-copy" style="color: #4b5563; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Copy Link</span>
                    </button>

                    <!-- Twitter / X -->
                    <a href="https://twitter.com/intent/tweet?url=${encodeURIComponent(memeUrl)}&text=${encodeURIComponent(memeTitle)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-x-twitter" style="color: #000; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Twitter / X</span>
                    </a>

                    <!-- Facebook -->
                    <a href="https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(memeUrl)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-facebook" style="color: #1877F2; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Facebook</span>
                    </a>

                    <!-- Instagram -->
                    <a href="https://www.instagram.com/" target="_blank" onclick="if(window.showToast) { window.showToast('Copy the link to share on Instagram!', 'info'); } else { console.log('Copy the link to share on Instagram!'); }" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-instagram" style="color: #E4405F; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Instagram</span>
                    </a>

                    <!-- WhatsApp -->
                    <a href="https://wa.me/?text=${encodeURIComponent(memeTitle + ': ' + memeUrl)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-whatsapp" style="color: #25D366; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">WhatsApp</span>
                    </a>

                    <!-- Telegram -->
                    <a href="https://t.me/share/url?url=${encodeURIComponent(memeUrl)}&text=${encodeURIComponent(memeTitle)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-telegram" style="color: #0088cc; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Telegram</span>
                    </a>

                    <!-- Reddit -->
                    <a href="https://reddit.com/submit?url=${encodeURIComponent(memeUrl)}&title=${encodeURIComponent(memeTitle)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-reddit" style="color: #FF4500; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Reddit</span>
                    </a>

                    <!-- Pinterest -->
                    <a href="https://pinterest.com/pin/create/button/?url=${encodeURIComponent(memeUrl)}&description=${encodeURIComponent(memeTitle)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-pinterest" style="color: #BD081C; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">Pinterest</span>
                    </a>

                    <!-- LinkedIn -->
                    <a href="https://www.linkedin.com/sharing/share-offsite/?url=${encodeURIComponent(memeUrl)}" target="_blank" style="
                        width: 100%;
                        display: flex;
                        align-items: center;
                        gap: 0.75rem;
                        padding: 0.6rem;
                        text-align: left;
                        text-decoration: none;
                        border-radius: 0.5rem;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f3f4f6'" onmouseout="this.style.backgroundColor='transparent'">
                        <i class="fab fa-linkedin" style="color: #0A66C2; width: 20px; text-align: center;"></i>
                        <span style="color: #374151; font-weight: 500; font-size: 0.9rem;">LinkedIn</span>
                    </a>
                </div>
            </div>
        </div>
    `;

    // Remove any existing modals
    closeShareModal();

    // Add the new modal to the page
    document.body.insertAdjacentHTML('beforeend', shareOptions);

    // Ensure FontAwesome is available if not already
    if (!document.querySelector('link[href*="font-awesome"]')) {
        const fa = document.createElement('link');
        fa.rel = 'stylesheet';
        fa.href = 'https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css';
        document.head.appendChild(fa);
    }
}

function copyMemeLink(text) {
    if (navigator.clipboard && window.isSecureContext) {
        navigator.clipboard.writeText(text).then(function () {
            if (window.showToast) {
                window.showToast('Link copied!', 'success');
            }
            closeShareModal();
        }).catch(function (err) {
            console.error('Could not copy text: ', err);
            fallbackCopyTextToClipboard(text);
        });
    } else {
        fallbackCopyTextToClipboard(text);
    }
}

function fallbackCopyTextToClipboard(text) {
    const textArea = document.createElement("textarea");
    textArea.value = text;
    document.body.appendChild(textArea);
    textArea.focus();
    textArea.select();
    try {
        document.execCommand('copy');
        if (window.showToast) {
            window.showToast('Link copied!', 'success');
        }
    } catch (err) {
        if (window.showToast) {
            window.showToast('Failed to copy link.', 'error');
        }
    } finally {
        document.body.removeChild(textArea);
        closeShareModal();
    }
}

function closeShareModal() {
    const modal = document.getElementById('share-modal');
    if (modal) {
        modal.remove();
    }
}


function showDeleteConfirmation(message, onConfirm) {
    const modalId = 'delete-confirm-modal';
    const modalHtml = `
        <div id="${modalId}" style="
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
            animation: fadeIn 0.2s ease-out;
        ">
            <div style="
                background: white;
                border-radius: 12px;
                padding: 1.5rem;
                width: 90%;
                max-width: 320px;
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1);
                text-align: center;
            ">
                <div style="margin-bottom: 1.5rem;">
                    <i class="fas fa-exclamation-triangle" style="color: #ef4444; font-size: 2rem; margin-bottom: 1rem; display: block;"></i>
                    <p style="font-size: 1rem; font-weight: 600; color: #111827; margin: 0;">${message}</p>
                </div>
                
                <div style="display: flex; gap: 0.75rem; justify-content: center;">
                    <button id="confirm-cancel-btn" style="
                        flex: 1;
                        padding: 0.625rem;
                        border-radius: 8px;
                        border: 1px solid #e5e7eb;
                        background: white;
                        color: #374151;
                        font-weight: 600;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#f9fafb'" onmouseout="this.style.backgroundColor='white'">
                        Cancel
                    </button>
                    <button id="confirm-delete-btn" style="
                        flex: 1;
                        padding: 0.625rem;
                        border-radius: 8px;
                        border: none;
                        background: #ef4444;
                        color: white;
                        font-weight: 600;
                        cursor: pointer;
                        transition: background-color 0.2s;
                    " onmouseover="this.style.backgroundColor='#dc2626'" onmouseout="this.style.backgroundColor='#ef4444'">
                        Delete
                    </button>
                </div>
            </div>
        </div>
        <style>
            @keyframes fadeIn { from { opacity: 0; } to { opacity: 1; } }
        </style>
    `;

    // Remove existing if any
    const existing = document.getElementById(modalId);
    if (existing) existing.remove();

    document.body.insertAdjacentHTML('beforeend', modalHtml);

    const modal = document.getElementById(modalId);
    const cancelBtn = document.getElementById('confirm-cancel-btn');
    const confirmBtn = document.getElementById('confirm-delete-btn');

    cancelBtn.onclick = () => modal.remove();
    confirmBtn.onclick = () => {
        modal.remove();
        if (typeof onConfirm === 'function') onConfirm();
    };

    // Close on background click
    modal.onclick = (e) => {
        if (e.target === modal) modal.remove();
    };
}

/**
 * Global Meme Interaction Logic (Reactions, Comments, etc.)
 */

// Function to trigger sidebar refresh
function refreshSidebar() {
    // Trigger leaderboard update if the function exists
    if (typeof updateLeaderboard === 'function') {
        updateLeaderboard();
    }
    // Trigger live scores update if the function exists
    if (typeof updateLiveScores === 'function') {
        updateLiveScores();
    }
}

document.addEventListener('DOMContentLoaded', function () {
    // Meme Deletion - AJAX for instant UI update
    $(document).on('click', '.delete-meme-btn', function (e) {
        e.preventDefault();
        const form = $(this).closest('form');
        const actionUrl = form.attr('action');
        const memeCard = $(this).closest('.post-card');
        const memeId = memeCard.data('id');

        showDeleteConfirmation('Are you sure you want to delete this post?', () => {
            $.ajax({
                url: actionUrl,
                method: 'DELETE',
                headers: { 
                    'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content'),
                    'Accept': 'application/json'
                },
                success: function (data) {
                    if (data.success) {
                        // Animate removal for better UX
                        memeCard.fadeOut(300, function() {
                            $(this).remove();
                            // Show success toast if available
                            if (window.showToast) {
                                window.showToast('Meme deleted successfully!', 'success');
                            }
                        });
                    }
                },
                error: function (xhr) {
                    console.error('Delete error:', xhr);
                    let errorMessage = 'Failed to delete meme';
                    if (xhr.status === 403) {
                        errorMessage = 'You are not authorized to delete this meme';
                    }
                    if (window.showToast) {
                        window.showToast(errorMessage, 'error');
                    }
                }
            });
        });
    });

    // Comment Deletion - Always allow to ensure consistency across Home and Show pages
    $(document).on('click', '.delete-btn', function (e) {
        e.preventDefault();
        const commentId = $(this).data('comment-id');
        showDeleteConfirmation('Are you sure you want to delete this comment?', () => {
            $.ajax({
                url: `/api/comments/${commentId}`,
                method: 'DELETE',
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function (data) {
                    if (data.success) {
                        const commentElement = $(`[data-comment-id="${commentId}"]`);
                        const memeId = commentElement.closest('.post-card, .card').data('id');
                        commentElement.remove();

                        // Update count
                        const countSpan = $(`.post-card[data-id="${memeId}"] .comment-count, .card[data-id="${memeId}"] .comment-count`);
                        if (countSpan.length) {
                            let currentCount = parseInt(countSpan.text()) || 0;
                            countSpan.text(Math.max(0, currentCount - 1));
                        }
                    }
                }
            });
        });
    });

    // Only run this global logic if the page explicitly opts-in (e.g., Show Page)
    // to avoid conflicts with existing inline scripts on the Home Page.
    if (!window.useGlobalMemeInteractions) return;

    // Only initialize if not already initialized to prevent duplicates
    if (window.memesInteractionsInitialized) return;
    window.memesInteractionsInitialized = true;

    // Use window.loggedIn or check meta tag or fallback
    const isLoggedIn = typeof window.loggedIn !== 'undefined' ? window.loggedIn : false;

    // Toggle Emoji Section (Delegation)
    $(document).on('click', '.react-toggle-btn', function () {
        const memeId = $(this).data('meme') || $(this).data('meme-id'); // Handle both data-meme and data-meme-id
        if (!isLoggedIn) { window.location = '/login'; return; }

        // Try finding by ID first (Home/Show pattern)
        let section = $('#emoji-section-' + memeId);
        // Fallback for show.blade.php if it uses .emoji-reactions and .post-card context
        if (section.length === 0) {
            const card = $(this).closest('.post-card, .card');
            section = card.find('.emoji-reactions');
        }

        if (section.length) {
            section.slideToggle(200);
            // Also toggle .emoji-list if present (for show.blade.php)
            section.find('.emoji-list').toggle();
        }
    });

    // Toggle Comment Section (Delegation)
    $(document).on('click', '.comment-toggle-btn', function () {
        const memeId = $(this).data('meme') || $(this).data('meme-id');
        if (!isLoggedIn) { window.location = '/login'; return; }

        let section = $('#comment-section-' + memeId);
        if (section.length === 0) {
            const card = $(this).closest('.post-card, .card');
            section = card.find('.comment-box');
        }

        if (section.length) {
            section.slideToggle(200);
        }
    });

    // Emoji Picker Click (Delegation for Dynamically added pickers if needed, but usually pickers are statically in DOM or initialized)
    // Note: emoji-picker element uses custom events, so we might need direct listeners or document delegation
    document.body.addEventListener('emoji-click', function (event) {
        // Check if event came from an emoji-picker
        if (event.target.tagName.toLowerCase() !== 'emoji-picker') return;

        const picker = event.target;
        const emoji = event.detail.unicode;
        const postCard = $(picker).closest('.post-card, .card');
        const memeId = postCard.data('id') || postCard.data('meme');

        if (!isLoggedIn) { window.location = '/login'; return; }

        // Optimistic UI Update
        const reactBtn = postCard.find('.react-toggle-btn');
        const emojiSpan = reactBtn.find('.reaction-emoji');
        if (emojiSpan.length) {
            emojiSpan.text(emoji);
        } else {
            // Fallback if structure is different
            reactBtn.text(emoji);
        }

        $.ajax({
            url: '/memes/' + memeId + '/reaction',
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ emoji }),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (data) {
                // Update specific emoji count in list if exists (Home page legacy)
                const emojiList = postCard.find('.emoji-list');
                if (emojiList.length) {
                    emojiList.find('.emoji-btn').remove();
                }

                // Update count on main button
                const countSpan = reactBtn.find('.reaction-count');
                if (countSpan.length && data.total_count !== undefined) {
                    countSpan.text(data.total_count);
                }

                // Update the User Reactions Line (Show Page)
                const reactionsLine = postCard.find('.user-reactions-line');
                if (reactionsLine.length && data.reactions_html) {
                    reactionsLine.html(data.reactions_html);
                }

                // Refresh sidebar to update scores instantly
                refreshSidebar();
            }
        });
    });

    // Submit Comment
    $(document).on('submit', '.comment-form', function (e) {
        e.preventDefault();
        let form = $(this);
        let memeId = form.data('id');
        let contentInput = form.find('input[name="content"]');
        let content = contentInput.val();

        if (content.trim() === '') return;

        $.ajax({
            url: `/api/meme/${memeId}/comments`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ body: content }),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
            success: function (res) {
                if (res.success) {
                    let commentList = form.closest('.comment-box, .post-card').find('.comment-list');

                    // Construct HTML - trying to be compatible with both
                    let newCommentHtml = `<li class="comment-item" data-comment-id="${res.comment.id}">
                        <div class="comment-content" tabindex="0">
                            <strong>${res.comment.user.name}</strong>: ${res.comment.body}
                            <div class="comment-actions">
                                <button class="comment-action-btn reply-btn" data-comment-id="${res.comment.id}">Reply</button>
                                <button class="comment-action-btn copy-btn" data-comment-body="${res.comment.body}" title="Copy comment">📋</button>
                                ${res.comment.user.id == window.userId ? `<button class="comment-action-btn delete-btn" data-comment-id="${res.comment.id}">Delete</button>` : ''}
                            </div>
                        </div>
                        <div class="reply-form-container" style="display: none;">
                            <form class="reply-form mt-2" data-parent-id="${res.comment.id}" data-meme-id="${memeId}">
                                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                <input type="hidden" name="parent_id" value="${res.comment.id}">
                                <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                                <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
                                <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
                            </form>
                        </div>
                        <ul class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></ul>
                    </li>`;

                    commentList.append(newCommentHtml);
                    commentList.scrollTop(commentList[0].scrollHeight);
                    contentInput.val('');

                    // Update count using server count if available, else local increment
                    const countSpan = form.closest('.post-card').find('.comment-count');
                    if (countSpan.length) {
                        if (res.comments_count !== undefined) {
                            countSpan.text(res.comments_count);
                        } else {
                            let c = parseInt(countSpan.text()) || 0;
                            countSpan.text(c + 1);
                        }
                    }

                    // Refresh sidebar to update scores instantly
                    refreshSidebar();
                } else {
                    if (window.showToast) {
                        window.showToast(res.message || 'Failed to submit comment', 'error');
                    }
                }
            },
            error: function (xhr) {
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

    // Reply Button
    $(document).on('click', '.reply-btn', function (e) {
        e.preventDefault();
        const replyFormContainer = $(this).closest('.comment-content').next('.reply-form-container');
        $('.reply-form-container').hide();
        replyFormContainer.show();
        replyFormContainer.find('input[name="body"]').focus();
    });

    // Cancel Reply
    $(document).on('click', '.cancel-reply', function (e) {
        e.preventDefault();
        $(this).closest('.reply-form-container').hide();
    });

    // Submit Reply
    $(document).on('submit', '.reply-form', function (e) {
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
            url: `/api/meme/${memeId}/comments`, // Updated to use the correct API route structure if needed, or stick to what worked
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({
                body: body,
                parent_id: parseInt(parentId)
            }),
            success: function (data) {
                if (data.success) {
                    let newReplyHtml = `<li class="reply-item" data-comment-id="${data.comment.id}">
                        <div class="comment-content" tabindex="0">
                            <strong>${data.comment.user.name}</strong>: ${data.comment.body}
                             <div class="comment-actions mt-1">
                                <button class="comment-action-btn reply-btn small text-primary border-0 bg-transparent p-0 me-2" data-comment-id="${data.comment.id}">Reply</button>
                                <button class="comment-action-btn copy-btn small text-secondary border-0 bg-transparent p-0" data-comment-body="${data.comment.body}" title="Copy reply">📋</button>
                                ${data.comment.user.id == window.userId ? `<button class="comment-action-btn delete-btn small text-danger border-0 bg-transparent p-0" data-comment-id="${data.comment.id}" title="Delete reply">🗑️</button>` : ''}
                            </div>
                        </div>
                        <div class="reply-form-container" style="display: none;">
                            <form class="reply-form mt-2" data-parent-id="${data.comment.id}" data-meme-id="${memeId}">
                                <input type="hidden" name="_token" value="${$('meta[name="csrf-token"]').attr('content')}">
                                <input type="hidden" name="parent_id" value="${data.comment.id}">
                                <input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>
                                <button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>
                                <button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>
                            </form>
                        </div>
                        <ul class="replies-container mt-2 list-unstyled" style="margin-left: 20px; border-left: 2px solid #eee; padding-left: 10px;"></ul>
                    </li>`;

                    const parentComment = $(`[data-comment-id="${parentId}"]`);
                    parentComment.find('.replies-container').first().append(newReplyHtml);
                    bodyInput.val('');
                    form.closest('.reply-form-container').hide();

                    // Update count
                    const countSpan = form.closest('.post-card').find('.comment-count');
                    if (countSpan.length) {
                        let c = parseInt(countSpan.text()) || 0;
                        countSpan.text(c + 1);
                    }
                }
            }
        });
    });



    // Copy Button & Hover effects
    $(document).on('click', '.copy-btn', function (e) {
        e.preventDefault();
        const commentBody = $(this).data('comment-body');

        // Modern clipboard API approach
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(commentBody).then(function () {
                if (window.showToast) {
                    window.showToast('Comment copied!', 'success');
                }
            }).catch(function (err) {
                console.error('Clipboard API failed: ', err);
                fallbackCopyTextToClipboard(commentBody);
            });
        } else {
            // Fallback for older browsers
            fallbackCopyTextToClipboard(commentBody);
        }
    });

    // Handle comment content focus/blur for delete and copy button visibility
    $(document).on('focus', '.comment-content', function () {
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

    $(document).on('blur', '.comment-content', function () {
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
    $(document).on('dblclick', '.comment-content', function () {
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
});
