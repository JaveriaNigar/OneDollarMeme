/**
 * Blog Comment System - AJAX Interactions
 * Handles instant comment updates without page reload
 */

document.addEventListener('DOMContentLoaded', function() {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content');
    
    if (!csrfToken) {
        console.error('CSRF token not found');
        return;
    }

    // ====================
    // Store Comment
    // ====================
    const commentForm = document.getElementById('blog-comment-form');
    if (commentForm) {
        commentForm.addEventListener('submit', async function(e) {
            e.preventDefault();
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const textarea = this.querySelector('textarea[name="comment"]');
            const originalText = submitBtn.textContent;
            
            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Posting...';
            
            try {
                const response = await fetch(this.action, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': csrfToken,
                        'X-Requested-With': 'XMLHttpRequest',
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify({
                        comment: textarea.value,
                        _token: csrfToken
                    })
                });
                
                const data = await response.json();
                
                if (response.ok && data.success) {
                    // Add comment to the DOM
                    appendCommentToDOM(data.comment);
                    
                    // Clear form
                    textarea.value = '';
                    
                    // Update comment count
                    updateCommentCount(1);
                    
                    // Show success message
                    showNotification('Comment added successfully!', 'success');
                } else {
                    showNotification(data.message || 'Failed to add comment', 'error');
                }
            } catch (error) {
                console.error('Error:', error);
                showNotification('An error occurred. Please try again.', 'error');
            } finally {
                submitBtn.disabled = false;
                submitBtn.textContent = originalText;
            }
        });
    }

    // ====================
    // Update Comment (Event Delegation)
    // ====================
    document.addEventListener('submit', async function(e) {
        const editForm = e.target.closest('.blog-comment-edit-form');
        if (!editForm) return;
        
        e.preventDefault();
        
        const submitBtn = editForm.querySelector('button[type="submit"]');
        const textarea = editForm.querySelector('textarea[name="comment"]');
        const commentId = editForm.dataset.commentId;
        const originalText = submitBtn.textContent;
        
        // Disable button and show loading
        submitBtn.disabled = true;
        submitBtn.textContent = 'Saving...';
        
        try {
            const response = await fetch(editForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    comment: textarea.value,
                    _method: 'PUT',
                    _token: csrfToken
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Update comment text in DOM
                const commentText = document.querySelector(`[data-comment-id="${commentId}"] .comment-text`);
                if (commentText) {
                    commentText.textContent = data.comment.comment;
                }
                
                // Hide edit form
                const editFormContainer = document.getElementById(`edit-form-${commentId}`);
                if (editFormContainer) {
                    editFormContainer.classList.add('hidden');
                }
                
                showNotification('Comment updated successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to update comment', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = originalText;
        }
    });

    // ====================
    // Delete Comment (Event Delegation)
    // ====================
    document.addEventListener('submit', async function(e) {
        const deleteForm = e.target.closest('.blog-comment-delete-form');
        if (!deleteForm) return;
        
        e.preventDefault();
        
        if (!confirm('Delete this comment?')) {
            return;
        }
        
        const commentId = deleteForm.dataset.commentId;
        const commentElement = document.querySelector(`[data-comment-id="${commentId}"]`);
        
        try {
            const response = await fetch(deleteForm.action, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    _method: 'DELETE',
                    _token: csrfToken
                })
            });
            
            const data = await response.json();
            
            if (response.ok && data.success) {
                // Remove comment from DOM with animation
                if (commentElement) {
                    commentElement.style.opacity = '0';
                    commentElement.style.transition = 'opacity 0.3s ease';
                    
                    setTimeout(() => {
                        commentElement.remove();
                        
                        // Update comment count
                        updateCommentCount(-1);
                        
                        // Check if no comments left
                        checkNoComments();
                    }, 300);
                }
                
                showNotification('Comment deleted successfully!', 'success');
            } else {
                showNotification(data.message || 'Failed to delete comment', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showNotification('An error occurred. Please try again.', 'error');
        }
    });

    // ====================
    // Helper Functions
    // ====================
    
    function appendCommentToDOM(comment) {
        const commentsContainer = document.getElementById('blog-comments-list');
        const noCommentsMessage = document.getElementById('no-comments-message');
        
        // Hide "no comments" message if it exists
        if (noCommentsMessage) {
            noCommentsMessage.style.display = 'none';
        }
        
        // Create comment HTML
        const commentHTML = `
            <div class="border-b pb-3 sm:pb-4 last:border-b-0" data-comment-id="${comment.id}" style="opacity: 0; transition: opacity 0.3s ease;">
                <div class="flex items-start gap-2 sm:gap-3">
                    <img src="${comment.user.profile_photo_url}"
                         alt="${comment.user.name}"
                         class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex-shrink-0">
                    <div class="flex-1 min-w-0">
                        <div class="flex items-center justify-between gap-2">
                            <span class="font-medium text-gray-900 text-sm sm:text-base truncate">${comment.user.name}</span>
                            <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0">${comment.created_at}</span>
                        </div>
                        <p class="text-gray-700 mt-1 text-sm sm:text-base break-words comment-text">${comment.comment}</p>

                        <div class="flex gap-3 sm:gap-4 mt-2">
                            <button onclick="toggleEditForm(${comment.id})" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm">
                                Edit
                            </button>
                            <form class="blog-comment-delete-form" data-comment-id="${comment.id}" action="/blog-comment/${comment.id}" method="POST" style="display: inline;">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="text-red-600 hover:text-red-700 text-xs sm:text-sm">
                                    Delete
                                </button>
                            </form>
                        </div>

                        <!-- Edit Form (Hidden by default) -->
                        <div id="edit-form-${comment.id}" class="hidden mt-3">
                            <form class="blog-comment-edit-form" data-comment-id="${comment.id}" action="/blog-comment/${comment.id}" method="POST">
                                <input type="hidden" name="_token" value="${csrfToken}">
                                <input type="hidden" name="_method" value="PUT">
                                <textarea name="comment" rows="2" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-blue-500 text-sm">${comment.comment}</textarea>
                                <div class="flex gap-2 mt-2">
                                    <button type="submit" class="px-3 py-1 bg-blue-600 text-white text-xs rounded-md hover:bg-blue-700">
                                        Save
                                    </button>
                                    <button type="button" onclick="toggleEditForm(${comment.id})" class="px-3 py-1 bg-gray-300 text-gray-700 text-xs rounded-md hover:bg-gray-400">
                                        Cancel
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        `;
        
        // Insert at the top of comments list
        if (commentsContainer) {
            commentsContainer.insertAdjacentHTML('afterbegin', commentHTML);
            
            // Fade in animation
            const newComment = commentsContainer.firstElementChild;
            setTimeout(() => {
                newComment.style.opacity = '1';
            }, 50);
        }
    }
    
    function updateCommentCount(change) {
        const countElement = document.querySelector('#comment-count');
        if (countElement) {
            const currentCount = parseInt(countElement.textContent) || 0;
            countElement.textContent = Math.max(0, currentCount + change);
        }
    }
    
    function checkNoComments() {
        const commentsContainer = document.getElementById('blog-comments-list');
        const noCommentsMessage = document.getElementById('no-comments-message');
        
        if (commentsContainer && commentsContainer.children.length === 0) {
            if (noCommentsMessage) {
                noCommentsMessage.style.display = 'block';
            }
        }
    }
    
    function showNotification(message, type = 'success') {
        // Create notification element
        const notification = document.createElement('div');
        notification.className = `fixed bottom-4 right-4 px-6 py-4 rounded-lg shadow-2xl z-[9999] transition-all duration-300 transform translate-y-full opacity-0`;
        
        if (type === 'success') {
            notification.classList.add('bg-green-500', 'text-white');
        } else {
            notification.classList.add('bg-red-500', 'text-white');
        }
        
        notification.textContent = message;
        notification.style.fontSize = '1rem';
        notification.style.fontWeight = '500';
        document.body.appendChild(notification);
        
        // Slide in
        setTimeout(() => {
            notification.style.transform = 'translateY(0)';
            notification.style.opacity = '1';
        }, 100);
        
        // Slide out and remove
        setTimeout(() => {
            notification.style.transform = 'translateY(100%)';
            notification.style.opacity = '0';
            setTimeout(() => {
                notification.remove();
            }, 300);
        }, 3000);
    }
    
    // Make toggleEditForm globally accessible
    window.toggleEditForm = function(commentId) {
        const form = document.getElementById(`edit-form-${commentId}`);
        if (form) {
            form.classList.toggle('hidden');
        }
    };
});
