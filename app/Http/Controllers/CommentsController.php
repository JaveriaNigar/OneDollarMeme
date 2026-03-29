<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\MemeComment;
use App\Models\Meme;
use App\Models\EngagementAudit;
use App\EngagementAuditService;
use Illuminate\Support\Facades\Auth;

class CommentsController extends Controller
{
    // Store a new comment or reply
    public function store(Request $request, Meme $meme)
    {
        $request->validate([
            'body' => 'required|string|max:500',
            'parent_id' => 'nullable|exists:meme_comments,id', // Validate parent_id exists if provided
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
            'body' => $request->body,
            'parent_id' => $parentId,
        ]);

        // Record engagement for audit
        $engagement = EngagementAudit::recordEngagement(
            Auth::id(),
            $meme->id,
            'comment',
            $request->ip(),
            $request->userAgent()
        );
        
        // Run audit check
        EngagementAuditService::auditEngagement($engagement);

        // Load the user relationship to get user info for response
        $comment->load('user');

        // Recalculate score if it's a contest meme
        if ($meme->is_contest) {
            $meme->recalculateScore();
        }

        return response()->json([
            'success' => true,
            'comment' => [
                'id' => $comment->id,
                'body' => $comment->body,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'parent_id' => $comment->parent_id,
                'created_at' => $comment->created_at->diffForHumans(),
                'is_reply' => $comment->isReply(),
            ],
            'comments_count' => $meme->comments()->count(),
        ]);
    }

    // Get comments in a tree structure for a meme
    public function getCommentsTree(Meme $meme)
    {
        // Get all comments for the meme and arrange them in a tree structure
        $allComments = $meme->comments()
            ->with('user')
            ->orderBy('created_at', 'asc') // Order by oldest first for building tree
            ->get();

        // Build the tree structure
        $commentTree = [];
        $commentMap = [];

        // First, map all comments by ID for easy lookup
        foreach ($allComments as $comment) {
            $commentMap[$comment->id] = [
                'id' => $comment->id,
                'body' => $comment->body,
                'user' => [
                    'id' => $comment->user->id,
                    'name' => $comment->user->name,
                ],
                'parent_id' => $comment->parent_id,
                'created_at' => $comment->created_at->diffForHumans(),
                'is_reply' => $comment->isReply(),
                'replies' => [],
            ];
        }

        // Then, build the tree structure by linking children to parents
        foreach ($allComments as $comment) {
            if ($comment->parent_id === null) {
                // This is a root comment
                $commentTree[] = &$commentMap[$comment->id];
            } else {
                // This is a reply, add it to its parent's replies
                if (isset($commentMap[$comment->parent_id])) {
                    $commentMap[$comment->parent_id]['replies'][] = &$commentMap[$comment->id];
                }
            }
        }

        // Clean up reference to avoid memory issues
        unset($ref);

        // Return comments ordered with newest first
        return response()->json([
            'success' => true,
            'comments' => array_reverse($commentTree),
        ]);
    }


    // Delete a comment (only owner can delete)
    public function destroy(MemeComment $comment)
    {
        // Check if the authenticated user is the owner of the comment
        if ($comment->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized to delete this comment'
            ], 403);
        }

        // Soft delete the comment
        $comment->delete();

        // Recalculate score if the meme belongs to a contest
        $meme = $comment->meme;
        if ($meme && $meme->is_contest) {
            $meme->recalculateScore();
        }

        return response()->json([
            'success' => true,
            'message' => 'Comment deleted successfully'
        ]);
    }
}
