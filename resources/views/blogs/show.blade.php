<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight line-clamp-2">
                {{ $blog->title }}
            </h2>
            @can('update', $blog)
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('blogs.edit', $blog) }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-sm sm:text-base flex-1 sm:flex-none text-center">
                        Edit
                    </a>
                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this blog?');" class="flex-1 sm:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-sm sm:text-base w-full">
                            Delete
                        </button>
                    </form>
                </div>
            @endcan
        </div>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 px-4 sm:px-6">
            <!-- Blog Article -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 sm:mb-6">
                @if($blog->featured_image)
                    <img src="{{ asset('storage/' . $blog->featured_image) }}"
                         alt="{{ $blog->title }}"
                         class="w-full h-48 sm:h-64 object-cover">
                @endif
                <div class="p-4 sm:p-8">
                    <!-- Meta Info -->
                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm text-gray-500 mb-4 sm:mb-6">
                        <div class="flex items-center gap-2">
                            <img src="{{ $blog->author->profile_photo_url }}"
                                 alt="{{ $blog->author->name }}"
                                 class="w-7 h-7 sm:w-8 sm:h-8 rounded-full">
                            <span class="truncate max-w-[120px] sm:max-w-none">{{ $blog->author->name }}</span>
                        </div>
                        <span class="hidden sm:inline">·</span>
                        <span>{{ $blog->published_at->format('M j, Y') }}</span>
                        <span class="hidden sm:inline">·</span>
                        <span class="hidden sm:inline">{{ $blog->reading_time }} min read</span>
                        <span class="sm:hidden">·</span>
                        <span>{{ $blog->views_count }} views</span>
                    </div>

                    <!-- Content -->
                    <div class="prose prose-sm sm:prose-lg max-w-none blog-content">
                        <style>
                            .blog-content a {
                                color: #7c3aed;
                                text-decoration: underline;
                                font-weight: 600;
                                word-break: break-word;
                            }
                            .blog-content a:hover {
                                color: #5b2e91;
                            }
                            .blog-content img {
                                max-width: 100%;
                                height: auto;
                                border-radius: 0.5rem;
                                margin: 1rem 0;
                            }
                            .blog-content h1, .blog-content h2, .blog-content h3, .blog-content h4 {
                                margin-top: 1.5rem;
                                margin-bottom: 0.75rem;
                                font-weight: 700;
                                word-break: break-word;
                            }
                            .blog-content p {
                                margin-bottom: 1rem;
                                line-height: 1.75;
                                word-break: break-word;
                            }
                            .blog-content ul, .blog-content ol {
                                margin-left: 1.5rem;
                                margin-bottom: 1rem;
                                overflow-x: auto;
                            }
                            .blog-content blockquote {
                                border-left: 4px solid #7c3aed;
                                padding-left: 1rem;
                                margin: 1.5rem 0;
                                font-style: italic;
                                color: #6b7280;
                            }
                            .blog-content pre, .blog-content code {
                                background: #f3f4f6;
                                padding: 0.25rem 0.5rem;
                                border-radius: 0.25rem;
                                font-family: monospace;
                                overflow-x: auto;
                            }
                            .blog-content pre {
                                padding: 1rem;
                                overflow-x: auto;
                                margin: 1rem 0;
                            }
                            .blog-content table {
                                display: block;
                                overflow-x: auto;
                                width: 100%;
                            }
                        </style>
                        {!! $blog->content !!}
                    </div>

                    <!-- App Action Buttons -->
                    @if($blog->appLinks && $blog->appLinks->count() > 0)
                        <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t">
                            <div class="flex flex-col gap-2">
                                @foreach($blog->appLinks as $link)
                                    <a href="{{ $link->url }}"
                                       class="inline-flex items-center gap-2 text-purple-600 hover:text-purple-700 font-medium transition text-sm sm:text-base break-all">
                                        <i class="bi bi-link-45deg"></i>
                                        <span>{{ $link->url }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Meta Keywords -->
                    @if($blog->meta_keywords)
                        <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t">
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $blog->meta_keywords) as $keyword)
                                    <span class="bg-purple-100 text-purple-700 px-2 sm:px-3 py-1 rounded-full text-xs sm:text-sm">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 sm:mb-6">
                        Comments ({{ $blog->comments->count() }})
                    </h3>

                    @auth
                        <!-- Comment Form -->
                        <form action="{{ route('blogs.comment.store', $blog) }}" method="POST" class="mb-6 sm:mb-8">
                            @csrf
                            <textarea name="comment" rows="4"
                                      placeholder="Write a comment..."
                                      class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('comment') border-red-500 @enderror text-sm sm:text-base"></textarea>
                            @error('comment')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <button type="submit" class="mt-3 bg-purple-600 hover:bg-purple-700 text-white px-4 sm:px-6 py-2 rounded-lg transition text-sm sm:text-base">
                                Post Comment
                            </button>
                        </form>
                    @else
                        <p class="text-gray-600 mb-4 sm:mb-6 text-sm sm:text-base">
                            <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700">Login</a>
                            to leave a comment.
                        </p>
                    @endauth

                    <!-- Comments List -->
                    @if($blog->comments->count() > 0)
                        <div class="space-y-4 sm:space-y-6">
                            @foreach($blog->comments as $comment)
                                <div class="border-b pb-3 sm:pb-4 last:border-b-0">
                                    <div class="flex items-start gap-2 sm:gap-3">
                                        <img src="{{ $comment->user->profile_photo_url }}"
                                             alt="{{ $comment->user->name }}"
                                             class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex-shrink-0">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2">
                                                <span class="font-medium text-gray-900 text-sm sm:text-base truncate">{{ $comment->user->name }}</span>
                                                <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0">{{ $comment->created_at->diffForHumans() }}</span>
                                            </div>
                                            <p class="text-gray-700 mt-1 text-sm sm:text-base break-words">{{ $comment->comment }}</p>

                                            @auth
                                                <div class="flex gap-3 sm:gap-4 mt-2">
                                                    @can('deleteComment', $comment)
                                                        <form action="{{ route('blogs.comment.delete', $comment) }}" method="POST"
                                                              onsubmit="return confirm('Delete this comment?');">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs sm:text-sm">
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>
                                            @endauth

                                            <!-- Replies -->
                                            @if($comment->replies->count() > 0)
                                                <div class="mt-3 sm:mt-4 ml-6 sm:ml-8 space-y-3 sm:space-y-4">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="flex items-start gap-2 sm:gap-3">
                                                            <img src="{{ $reply->user->profile_photo_url }}"
                                                                 alt="{{ $reply->user->name }}"
                                                                 class="w-6 h-6 sm:w-8 sm:h-8 rounded-full flex-shrink-0">
                                                            <div class="flex-1 min-w-0">
                                                                <div class="flex items-center justify-between gap-2">
                                                                    <span class="font-medium text-gray-900 text-xs sm:text-sm truncate">{{ $reply->user->name }}</span>
                                                                    <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0">{{ $reply->created_at->diffForHumans() }}</span>
                                                                </div>
                                                                <p class="text-gray-700 mt-1 text-xs sm:text-sm break-words">{{ $reply->comment }}</p>
                                                                @auth
                                                                    @can('deleteComment', $reply)
                                                                        <form action="{{ route('blogs.comment.delete', $reply) }}" method="POST"
                                                                              onsubmit="return confirm('Delete this comment?');" class="mt-2">
                                                                            @csrf
                                                                            @method('DELETE')
                                                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs sm:text-sm">
                                                                                Delete
                                                                            </button>
                                                                        </form>
                                                                    @endcan
                                                                @endauth
                                                            </div>
                                                        </div>
                                                    @endforeach
                                                </div>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <p class="text-gray-500 text-center py-6 sm:py-8 text-sm sm:text-base">No comments yet. Be the first to comment!</p>
                    @endif
                </div>
            </div>

            <!-- Related Blogs -->
            @if($relatedBlogs->count() > 0)
                <div class="mt-6 sm:mt-8">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-3 sm:mb-4">Related Blogs</h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-3 sm:gap-4">
                        @foreach($relatedBlogs as $relatedBlog)
                            <a href="{{ route('blogs.show', $relatedBlog->slug) }}"
                               class="bg-white p-3 sm:p-4 rounded-lg shadow hover:shadow-md transition">
                                <h4 class="font-semibold text-gray-900 mb-2 text-sm sm:text-base line-clamp-2">{{ $relatedBlog->title }}</h4>
                                <p class="text-xs sm:text-sm text-gray-600 line-clamp-2">{{ $relatedBlog->excerpt }}</p>
                            </a>
                        @endforeach
                    </div>
                </div>
            @endif
        </div>
    </div>

    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
    </style>
</x-app-layout>
