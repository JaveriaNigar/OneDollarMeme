<?php

namespace App\Policies;

use App\Models\Blog;
use App\Models\User;

class BlogPolicy
{
    /**
     * Determine if the user can view any blogs.
     */
    public function viewAny(?User $user): bool
    {
        return true; // Everyone can view blogs
    }

    /**
     * Determine if the user can view the blog.
     */
    public function view(?User $user, Blog $blog): bool
    {
        // Everyone can view published blogs
        if ($blog->status === 'published') {
            return true;
        }

        // Authors can view their own drafts
        if ($user && $user->id === $blog->user_id) {
            return true;
        }

        // Admins can view everything
        if ($user && $user->isAdmin()) {
            return true;
        }

        return false;
    }

    /**
     * Determine if the user can create blogs.
     */
    public function create(User $user): bool
    {
        return $user->isAdmin();
    }

    /**
     * Determine if the user can update the blog.
     */
    public function update(User $user, Blog $blog): bool
    {
        // Authors can edit their own blogs
        if ($user->id === $blog->user_id) {
            return true;
        }

        // Admins can edit any blog
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete the blog.
     */
    public function delete(User $user, Blog $blog): bool
    {
        // Authors can delete their own blogs
        if ($user->id === $blog->user_id) {
            return true;
        }

        // Admins can delete any blog
        return $user->isAdmin();
    }

    /**
     * Determine if the user can delete a comment.
     */
    public function deleteComment(User $user, \App\Models\BlogComment $comment): bool
    {
        // Comment authors can delete their own comments
        if ($user->id === $comment->user_id) {
            return true;
        }

        // Blog authors can delete comments on their blog
        if ($user->id === $comment->blog->user_id) {
            return true;
        }

        // Admins can delete any comment
        return $user->isAdmin();
    }
}
