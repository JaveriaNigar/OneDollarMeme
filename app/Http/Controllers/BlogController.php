<?php

namespace App\Http\Controllers;

use App\Models\Blog;
use App\Models\BlogAppLink;
use App\Models\BlogComment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

class BlogController extends Controller
{
    use AuthorizesRequests;
    /**
     * Display a listing of published blogs.
     */
    public function index(Request $request)
    {
        $query = Blog::with('author')
            ->published()
            ->orderBy('published_at', 'desc');

        // Search functionality
        if ($request->has('search') && $request->search) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('title', 'LIKE', "%{$search}%")
                  ->orWhere('content', 'LIKE', "%{$search}%")
                  ->orWhere('meta_keywords', 'LIKE', "%{$search}%");
            });
        }

        // Filter by author
        if ($request->has('author') && $request->author) {
            $query->whereHas('author', function ($q) use ($request) {
                $q->where('name', 'LIKE', "%{$request->author}%");
            });
        }

        $blogs = $query->paginate(12);

        return view('blogs.index', compact('blogs'));
    }

    /**
     * Display a single blog post.
     */
    public function show($slug)
    {
        $blog = Blog::with([
            'author', 
            'comments.user', 
            'comments.replies.user',
            'comments.blog',
            'comments.replies.blog',
        ])
            ->where('slug', $slug)
            ->published()
            ->firstOrFail();

        // Increment view count
        $blog->incrementViews();

        // Get related blogs
        $relatedBlogs = Blog::published()
            ->where('id', '!=', $blog->id)
            ->where(function ($q) use ($blog) {
                $q->where('meta_keywords', 'LIKE', "%{$blog->meta_keywords}%")
                  ->orWhereHas('author', function ($q2) use ($blog) {
                      $q2->where('user_id', $blog->user_id);
                  });
            })
            ->orderBy('published_at', 'desc')
            ->limit(4)
            ->get();

        return view('blogs.show', compact('blog', 'relatedBlogs'));
    }

    /**
     * Show the form for creating a new blog (Blogger only).
     */
    public function create()
    {
        $this->authorize('create', Blog::class);

        return view('blogs.create');
    }

    /**
     * Store a newly created blog.
     */
    public function store(Request $request)
    {
        $this->authorize('create', Blog::class);

        // Get status from button click (overrides dropdown)
        $status = $request->input('status', 'draft');

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'in:draft,published',
            'app_links' => 'nullable|array',
            'app_links.*' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('featured_image', 'status', 'app_links');
        $data['status'] = $status;
        $data['user_id'] = Auth::id();

        // Sanitize HTML content - allow safe tags including links, images, formatting
        $allowedTags = '<a><p><br><strong><em><u><ul><ol><li><h1><h2><h3><h4><h5><h6><img><blockquote><pre><code><table><thead><tbody><tr><th><td><hr><span><div><iframe>';
        $data['content'] = strip_tags($data['content'], $allowedTags);

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            $data['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        // Set published_at if status is published
        if ($data['status'] === 'published') {
            $data['published_at'] = now();
        }

        $blog = Blog::create($data);

        // Save app links
        $this->saveAppLinks($blog, $request->input('app_links', []));

        return redirect()->route('blogs.show', $blog->slug)
            ->with('success', 'Blog created successfully!');
    }

    /**
     * Show the form for editing a blog (Author only).
     */
    public function edit(Blog $blog)
    {
        $this->authorize('update', $blog);

        return view('blogs.edit', compact('blog'));
    }

    /**
     * Update the specified blog.
     */
    public function update(Request $request, Blog $blog)
    {
        $this->authorize('update', $blog);

        // Get status from button click (overrides dropdown)
        $status = $request->input('status', $blog->status);

        $validator = Validator::make($request->all(), [
            'title' => 'required|string|max:255',
            'content' => 'required|string',
            'featured_image' => 'nullable|image|max:2048',
            'meta_description' => 'nullable|string|max:160',
            'meta_keywords' => 'nullable|string|max:255',
            'status' => 'in:draft,published,archived',
            'app_links' => 'nullable|array',
            'app_links.*' => 'nullable|url|max:500',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $data = $request->except('featured_image', 'status', 'app_links');
        $data['status'] = $status;

        // Sanitize HTML content - allow safe tags including links, images, formatting
        $allowedTags = '<a><p><br><strong><em><u><ul><ol><li><h1><h2><h3><h4><h5><h6><img><blockquote><pre><code><table><thead><tbody><tr><th><td><hr><span><div><iframe>';
        $data['content'] = strip_tags($data['content'], $allowedTags);

        // Handle featured image removal
        if ($request->input('remove_featured_image') && $blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
            $data['featured_image'] = null;
        }

        // Handle featured image upload
        if ($request->hasFile('featured_image')) {
            // Delete old image
            if ($blog->featured_image) {
                Storage::disk('public')->delete($blog->featured_image);
            }
            $data['featured_image'] = $request->file('featured_image')->store('blogs', 'public');
        }

        // Set published_at if status changed to published
        if ($data['status'] === 'published' && $blog->status !== 'published') {
            $data['published_at'] = now();
        }

        $blog->update($data);

        // Save app links (delete old ones and create new)
        $this->saveAppLinks($blog, $request->input('app_links', []));

        return redirect()->route('blogs.show', $blog->slug)
            ->with('success', 'Blog updated successfully!');
    }

    /**
     * Remove the specified blog (Author only).
     */
    public function destroy(Blog $blog)
    {
        $this->authorize('delete', $blog);

        // Delete featured image if exists
        if ($blog->featured_image) {
            Storage::disk('public')->delete($blog->featured_image);
        }

        // App links will be cascade deleted by the database foreign key
        $blog->delete();

        return redirect()->route('blogs.index')
            ->with('success', 'Blog deleted successfully!');
    }

    /**
     * Save app links for a blog.
     */
    private function saveAppLinks(Blog $blog, array $linksData)
    {
        // Delete existing links
        $blog->appLinks()->delete();

        // Create new links (skip empty ones)
        if (empty($linksData)) {
            return;
        }

        $sortOrder = 0;
        foreach ($linksData as $url) {
            $url = trim($url);

            // Only save if URL is provided
            if ($url) {
                $blog->appLinks()->create([
                    'label' => $url,
                    'url' => $url,
                    'icon' => 'bi-link-45deg',
                    'sort_order' => $sortOrder++,
                ]);
            }
        }
    }

    /**
     * Store a comment on a blog.
     */
    public function storeComment(Request $request, Blog $blog)
    {
        $request->validate([
            'comment' => 'required|string|max:2000',
            'parent_id' => 'nullable|exists:blog_comments,id',
        ]);

        $comment = BlogComment::create([
            'blog_id' => $blog->id,
            'user_id' => Auth::id(),
            'comment' => $request->comment,
            'parent_id' => $request->parent_id,
            'is_approved' => true,
        ]);

        return redirect()->route('blogs.show', $blog->slug)
            ->with('success', 'Comment added successfully!');
    }

    /**
     * Update a comment.
     */
    public function updateComment(Request $request, BlogComment $comment)
    {
        $this->authorize('updateComment', $comment);

        $request->validate([
            'comment' => 'required|string|max:2000',
        ]);

        $comment->update([
            'comment' => $request->comment,
        ]);

        return redirect()->route('blogs.show', $comment->blog->slug)
            ->with('success', 'Comment updated successfully!');
    }

    /**
     * Delete a comment.
     */
    public function deleteComment(BlogComment $comment)
    {
        $this->authorize('deleteComment', $comment);

        $blog = $comment->blog;
        $comment->delete();

        return redirect()->route('blogs.show', $blog->slug)
            ->with('success', 'Comment deleted successfully!');
    }

    /**
     * Display blogger dashboard.
     */
    public function dashboard()
    {
        $this->authorize('create', Blog::class);

        $user = Auth::user();

        $stats = [
            'total_blogs' => $user->blogs()->count(),
            'published' => $user->blogs()->published()->count(),
            'drafts' => $user->blogs()->draft()->count(),
            'total_views' => $user->blogs()->sum('views_count'),
            'total_comments' => $user->blogComments()->count(),
        ];

        $recentBlogs = $user->blogs()
            ->withCount('comments')
            ->orderBy('updated_at', 'desc')
            ->limit(10)
            ->get();

        return view('blogs.dashboard', compact('stats', 'recentBlogs'));
    }

    /**
     * Display user's own blogs.
     */
    public function myBlogs()
    {
        $this->authorize('create', Blog::class);

        $blogs = Auth::user()->blogs()
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('blogs.my-blogs', compact('blogs'));
    }

    /**
     * Admin: Display all blogs for moderation.
     */
    public function adminIndex()
    {
        $blogs = Blog::with('author')
            ->orderBy('created_at', 'desc')
            ->paginate(20);

        return view('admin.blogs.index', compact('blogs'));
    }

    /**
     * Admin: Update blog status.
     */
    public function adminUpdateStatus(Request $request, Blog $blog)
    {
        $request->validate([
            'status' => 'required|in:draft,published,archived',
        ]);

        $data = ['status' => $request->status];

        if ($request->status === 'published' && $blog->status !== 'published') {
            $data['published_at'] = now();
        }

        $blog->update($data);

        return redirect()->back()->with('success', 'Blog status updated successfully!');
    }
}
