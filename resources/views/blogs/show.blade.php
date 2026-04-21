<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-semibold text-lg sm:text-xl text-gray-800 leading-tight line-clamp-2">
                {{ $blog->title }}
            </h2>
            @can('update', $blog)
                <div class="flex gap-2 w-full sm:w-auto">
                    <a href="{{ route('blogs.edit', $blog) }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-sm sm:text-base flex-1 sm:flex-none text-center inline-flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                        </svg>
                        Edit
                    </a>
                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST"
                          onsubmit="return confirm('Are you sure you want to delete this blog?');" class="flex-1 sm:flex-none">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 sm:px-4 py-2 rounded-lg transition text-sm sm:text-base w-full inline-flex items-center justify-center gap-2 shadow-sm hover:shadow-md">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                            </svg>
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
                    <div class="relative">
                        <img src="{{ asset('storage/' . $blog->featured_image) }}"
                             alt="{{ $blog->title }}"
                             class="w-full h-48 sm:h-64 lg:h-80 object-cover">
                        <div class="absolute inset-0 bg-gradient-to-t from-black/50 to-transparent"></div>
                    </div>
                @endif
                <div class="p-4 sm:p-8">
                    <!-- Meta Info -->


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
                        <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"></path>
                                </svg>
                                Links
                            </h4>
                            <div class="flex flex-col gap-3">
                                @foreach($blog->appLinks as $link)
                                    <a href="{{ $link->url }}" target="_blank" rel="noopener noreferrer"
                                       class="inline-flex items-center gap-2 px-4 py-3 bg-gradient-to-r from-purple-50 to-purple-100 text-purple-700 hover:from-purple-100 hover:to-purple-200 rounded-lg transition shadow-sm hover:shadow-md text-sm sm:text-base break-all group">
                                        <svg class="w-4 h-4 group-hover:scale-110 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path>
                                        </svg>
                                        <span class="font-medium">{{ $link->url }}</span>
                                    </a>
                                @endforeach
                            </div>
                        </div>
                    @endif

                    <!-- Meta Keywords -->
                    @if($blog->meta_keywords)
                        <div class="mt-6 sm:mt-8 pt-4 sm:pt-6 border-t border-gray-200">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3 flex items-center gap-2">
                                <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                                </svg>
                                Tags
                            </h4>
                            <div class="flex flex-wrap gap-2">
                                @foreach(explode(',', $blog->meta_keywords) as $keyword)
                                    <span class="bg-gradient-to-r from-purple-100 to-purple-50 text-purple-700 px-3 py-1.5 rounded-full text-xs sm:text-sm font-medium hover:from-purple-200 hover:to-purple-100 transition shadow-sm">
                                        {{ trim($keyword) }}
                                    </span>
                                @endforeach
                            </div>
                        </div>
                    @endif

                </div>
                                    <div class="flex flex-wrap items-center gap-2 sm:gap-4 text-xs sm:text-sm font-bold text-gray-500 mb-4 sm:mb-6">
                        <span class="inline-flex items-center gap-2 bg-purple-100 text-purple-700 px-3 py-1 rounded-full font-semibold">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path>
                            </svg>
                            OneDollarMeme
                        </span>
                        <span class="inline-flex items-center gap-1 text-black font-bold">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m4 4V3m-2 8h4m-4 4h4m-6 4h12a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            {{ $blog->published_at->format('M j, Y') }}
                        </span>


                    </div>
            </div>

            <!-- Comments Section -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-6">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                        </svg>
                        Comments (<span id="comment-count" class="text-purple-600">{{ $blog->comments->count() }}</span>)
                    </h3>

                    @auth
                        <!-- Comment Form -->
                        <div class="mb-6 sm:mb-8 bg-gray-50 rounded-lg p-4 sm:p-6">
                            <h4 class="text-sm font-semibold text-gray-700 mb-3">Leave a Comment</h4>
                            <form id="blog-comment-form" action="{{ route('blogs.comment.store', $blog) }}" method="POST">
                                @csrf
                                <textarea name="comment" rows="4"
                                          placeholder="Share your thoughts..."
                                          class="w-full border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-purple-500 @error('comment') border-red-500 @enderror text-sm sm:text-base resize-y"></textarea>
                                @error('comment')
                                    <p class="text-red-500 text-sm mt-1 flex items-center gap-1">
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                        </svg>
                                        {{ $message }}
                                    </p>
                                @enderror
                                <button type="submit" class="mt-3 bg-purple-600  hover:from-purple-700 hover:to-purple-800 text-white px-4 sm:px-6 py-2.5 rounded-lg transition text-sm sm:text-base font-medium shadow-sm hover:shadow-md inline-flex items-center gap-2">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8"></path>
                                    </svg>
                                    Post Comment
                                </button>
                            </form>
                        </div>
                    @else
                        <div class="mb-6 sm:mb-8 bg-gradient-to-r from-purple-50 to-blue-50 rounded-lg p-4 sm:p-6 text-center">
                            <svg class="w-12 h-12 text-purple-400 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-gray-600 text-sm sm:text-base">
                                <a href="{{ route('login') }}" class="text-purple-600 hover:text-purple-700 font-semibold underline">Login</a>
                                to join the conversation.
                            </p>
                        </div>
                    @endauth

                    <!-- Comments List -->
                    @if($blog->comments->count() > 0)
                        <div id="blog-comments-list" class="space-y-4 sm:space-y-6">
                            @foreach($blog->comments as $comment)
                                <div class="bg-gray-50 rounded-lg p-4 sm:p-5 hover:bg-gray-100 transition-colors" data-comment-id="{{ $comment->id }}">
                                    <div class="flex items-start gap-3 sm:gap-4">
                                        <img src="{{ $comment->user->profile_photo_url }}"
                                             alt="{{ $comment->user->name }}"
                                             class="w-10 h-10 sm:w-12 sm:h-12 rounded-full flex-shrink-0 shadow-sm border-2 border-white">
                                        <div class="flex-1 min-w-0">
                                            <div class="flex items-center justify-between gap-2 mb-2">
                                                <div class="flex items-center gap-2">
                                                    <span class="font-semibold text-gray-900 text-sm sm:text-base">{{ $comment->user->name }}</span>
            
                                                </div>
                                                <span class="text-xs sm:text-sm text-gray-500 flex-shrink-0 flex items-center gap-1">
                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                    </svg>
                                                    {{ $comment->created_at->diffForHumans() }}
                                                </span>
                                            </div>
                                            <p class="text-gray-700 text-sm sm:text-base break-words comment-text leading-relaxed">{{ $comment->comment }}</p>

                                            @auth
                                                <div class="flex gap-3 sm:gap-4 mt-3 pt-3 border-t border-gray-200">
                                                    @can('updateComment', $comment)
                                                        <button onclick="toggleEditForm({{ $comment->id }})" class="text-blue-600 hover:text-blue-700 text-xs sm:text-sm font-medium flex items-center gap-1 hover:underline">
                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                            </svg>
                                                            Edit
                                                        </button>
                                                    @endcan
                                                    @can('deleteComment', $comment)
                                                        <form class="blog-comment-delete-form" data-comment-id="{{ $comment->id }}" action="{{ route('blogs.comment.delete', $comment) }}" method="POST"
                                                              style="display: inline;">
                                                            @csrf
                                                            @method('DELETE')
                                                            <button type="submit" class="text-red-600 hover:text-red-700 text-xs sm:text-sm font-medium flex items-center gap-1 hover:underline">
                                                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                </svg>
                                                                Delete
                                                            </button>
                                                        </form>
                                                    @endcan
                                                </div>

                                                <!-- Edit Form (Hidden by default) -->
                                                <div id="edit-form-{{ $comment->id }}" class="hidden mt-4 bg-white rounded-lg p-4 shadow-sm border border-gray-200">
                                                    <form class="blog-comment-edit-form" data-comment-id="{{ $comment->id }}" action="{{ route('blogs.comment.update', $comment) }}" method="POST">
                                                        @csrf
                                                        @method('PUT')
                                                        <label class="block text-xs font-medium text-gray-700 mb-2">Edit Comment</label>
                                                        <textarea name="comment" rows="3" class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-sm resize-y">{{ $comment->comment }}</textarea>
                                                        <div class="flex gap-2 mt-3">
                                                            <button type="submit" class="px-4 py-2  bg-purple-600 text-white text-sm font-medium rounded-md hover:from-purple-700 hover:to-purple-800 transition shadow-sm flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                </svg>
                                                                Save Changes
                                                            </button>
                                                            <button type="button" onclick="toggleEditForm({{ $comment->id }})" class="px-4 py-2 bg-gray-200 text-gray-700 text-sm font-medium rounded-md hover:bg-gray-300 transition flex items-center gap-1">
                                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                </svg>
                                                                Cancel
                                                            </button>
                                                        </div>
                                                    </form>
                                                </div>
                                            @endauth

                                            <!-- Replies -->
                                            @if($comment->replies->count() > 0)
                                                <div class="mt-4 sm:mt-5 ml-4 sm:ml-6 space-y-3 sm:space-y-4 border-l-2 border-purple-200 pl-4 sm:pl-6">
                                                    @foreach($comment->replies as $reply)
                                                        <div class="bg-white rounded-lg p-3 sm:p-4 shadow-sm hover:shadow-md transition-shadow" data-comment-id="{{ $reply->id }}">
                                                            <div class="flex items-start gap-2 sm:gap-3">
                                                                <img src="{{ $reply->user->profile_photo_url }}"
                                                                     alt="{{ $reply->user->name }}"
                                                                     class="w-8 h-8 sm:w-10 sm:h-10 rounded-full flex-shrink-0 shadow-sm border-2 border-purple-100">
                                                                <div class="flex-1 min-w-0">
                                                                    <div class="flex items-center justify-between gap-2 mb-1">
                                                                        <div class="flex items-center gap-2">
                                                                            <span class="font-semibold text-gray-900 text-xs sm:text-sm">{{ $reply->user->name }}</span>
                                                                            <span class="text-xs text-purple-600 bg-purple-100 px-2 py-0.5 rounded-full font-medium">Reply</span>
                                                                        </div>
                                                                        <span class="text-xs text-gray-500 flex-shrink-0 flex items-center gap-1">
                                                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                                                            </svg>
                                                                            {{ $reply->created_at->diffForHumans() }}
                                                                        </span>
                                                                    </div>
                                                                    <p class="text-gray-700 mt-1 text-xs sm:text-sm break-words comment-text leading-relaxed">{{ $reply->comment }}</p>
                                                                    @auth
                                                                        <div class="flex gap-3 sm:gap-4 mt-2 pt-2 border-t border-gray-100">
                                                                            @can('updateComment', $reply)
                                                                                <button onclick="toggleEditForm({{ $reply->id }})" class="text-blue-600 hover:text-blue-700 text-xs font-medium flex items-center gap-1 hover:underline">
                                                                                    <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                                                    </svg>
                                                                                    Edit
                                                                                </button>
                                                                            @endcan
                                                                            @can('deleteComment', $reply)
                                                                                <form class="blog-comment-delete-form" data-comment-id="{{ $reply->id }}" action="{{ route('blogs.comment.delete', $reply) }}" method="POST"
                                                                                      style="display: inline;">
                                                                                    @csrf
                                                                                    @method('DELETE')
                                                                                    <button type="submit" class="text-red-600 hover:text-red-700 text-xs font-medium flex items-center gap-1 hover:underline">
                                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                                                        </svg>
                                                                                        Delete
                                                                                    </button>
                                                                                </form>
                                                                            @endcan
                                                                        </div>

                                                                        <!-- Edit Form (Hidden by default) -->
                                                                        <div id="edit-form-{{ $reply->id }}" class="hidden mt-3 bg-gray-50 rounded-lg p-3 shadow-sm border border-gray-200">
                                                                            <form class="blog-comment-edit-form" data-comment-id="{{ $reply->id }}" action="{{ route('blogs.comment.update', $reply) }}" method="POST">
                                                                                @csrf
                                                                                @method('PUT')
                                                                                <label class="block text-xs font-medium text-gray-700 mb-1">Edit Reply</label>
                                                                                <textarea name="comment" rows="2" class="w-full px-2 py-1.5 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500 focus:border-purple-500 text-xs resize-y">{{ $reply->comment }}</textarea>
                                                                                <div class="flex gap-2 mt-2">
                                                                                    <button type="submit" class="px-3 py-1.5 bg-gradient-to-r from-purple-600 to-purple-700 text-white text-xs font-medium rounded-md hover:from-purple-700 hover:to-purple-800 transition shadow-sm flex items-center gap-1">
                                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                                                                                        </svg>
                                                                                        Save
                                                                                    </button>
                                                                                    <button type="button" onclick="toggleEditForm({{ $reply->id }})" class="px-3 py-1.5 bg-gray-200 text-gray-700 text-xs font-medium rounded-md hover:bg-gray-300 transition flex items-center gap-1">
                                                                                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                                                                        </svg>
                                                                                        Cancel
                                                                                    </button>
                                                                                </div>
                                                                            </form>
                                                                        </div>
                                                                    @endauth
                                                                </div>
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
                        <div id="no-comments-message" class="text-gray-500 text-center py-8 sm:py-12">
                            <svg class="w-16 h-16 text-gray-300 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-sm sm:text-base">No comments yet. Be the first to share your thoughts!</p>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Related Blogs -->
            @if($relatedBlogs->count() > 0)
                <div class="mt-6 sm:mt-8">
                    <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-4 sm:mb-6 flex items-center gap-2">
                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path>
                        </svg>
                        Related Articles
                    </h3>
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 sm:gap-6">
                        @foreach($relatedBlogs as $relatedBlog)
                            <a href="{{ route('blogs.show', $relatedBlog->slug) }}"
                               class="bg-white rounded-lg shadow-sm hover:shadow-xl transition-all duration-300 overflow-hidden group border border-gray-200 hover:border-purple-300">
                                @if($relatedBlog->featured_image)
                                    <div class="relative overflow-hidden">
                                        <img src="{{ asset('storage/' . $relatedBlog->featured_image) }}"
                                             alt="{{ $relatedBlog->title }}"
                                             class="w-full h-40 sm:h-48 object-cover group-hover:scale-105 transition-transform duration-300">
                                        <div class="absolute inset-0 bg-gradient-to-t from-black/40 to-transparent opacity-0 group-hover:opacity-100 transition-opacity"></div>
                                    </div>
                                @endif
                                <div class="p-4 sm:p-5">
                                    <h4 class="font-semibold text-gray-900 mb-2 text-sm sm:text-base line-clamp-2 group-hover:text-purple-700 transition-colors">{{ $relatedBlog->title }}</h4>
                                    <p class="text-xs sm:text-sm text-gray-600 line-clamp-2 mb-3 leading-relaxed">{{ $relatedBlog->excerpt }}</p>
                                    <div class="flex items-center justify-between">
                                        <span class="text-xs text-gray-500 flex items-center gap-1">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m4 4V3m-2 8h4m-4 4h4m-6 4h12a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            {{ $relatedBlog->published_at->format('M j, Y') }}
                                        </span>
                                        <span class="text-purple-600 hover:text-purple-700 font-medium text-xs sm:text-sm flex items-center gap-1 group-hover:gap-2 transition-all">
                                            Read More
                                            <svg class="w-4 h-4 group-hover:translate-x-1 transition-transform" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                            </svg>
                                        </span>
                                    </div>
                                </div>
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
        .line-clamp-3 {
            display: -webkit-box;
            -webkit-line-clamp: 3;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
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

    <!-- CSRF Token Meta Tag -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <script>
        function toggleEditForm(commentId) {
            const form = document.getElementById(`edit-form-${commentId}`);
            form.classList.toggle('hidden');
        }
    </script>

    <!-- Blog Comments AJAX Script -->
    <script src="{{ asset('js/blog-comments.js') }}"></script>
</x-app-layout>
