<x-app-layout>
    <x-slot name="header">
        <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-3">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Blogs') }}
            </h2>
            @auth
                @if(auth()->user()->isAdmin())
                    <a href="{{ route('blogs.create') }}"
                       class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition w-full sm:w-auto text-center">
                        Create Blog
                    </a>
                @endif
            @endauth
        </div>
    </x-slot>

    <div class="py-6 sm:py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 px-4 sm:px-6">
            <!-- Search Bar -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg mb-4 sm:mb-6">
                <div class="p-4 sm:p-6">
                    <form action="{{ route('blogs.index') }}" method="GET" class="flex flex-col sm:flex-row gap-3">
                        <input type="text" name="search"
                               placeholder="Search blogs..."
                               value="{{ request('search') }}"
                               class="flex-1 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                        <div class="flex gap-2">
                            <button type="submit" class="bg-purple-600 hover:bg-purple-700 text-white px-4 sm:px-6 py-2 rounded-lg transition flex-1 sm:flex-none">
                                Search
                            </button>
                            @if(request('search'))
                                <a href="{{ route('blogs.index') }}" class="bg-gray-500 hover:bg-gray-600 text-white px-4 sm:px-6 py-2 rounded-lg transition flex-1 sm:flex-none text-center">
                                    Clear
                                </a>
                            @endif
                        </div>
                    </form>
                </div>
            </div>

            <!-- Blogs Grid -->
            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-4 sm:gap-6">
                    @foreach($blogs as $blog)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                            @if($blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}"
                                     alt="{{ $blog->title }}"
                                     class="w-full h-40 sm:h-48 object-cover">
                            @endif
                            <div class="p-4 sm:p-6">
                                <h3 class="text-base sm:text-lg font-semibold text-gray-900 mb-2 line-clamp-2">
                                    <a href="{{ route('blogs.show', $blog->slug) }}"
                                       class="hover:text-purple-600 transition">
                                        {{ $blog->title }}
                                    </a>
                                </h3>
                                <p class="text-gray-600 text-sm mb-3 sm:mb-4 line-clamp-3">
                                    {{ $blog->excerpt }}
                                </p>
                                <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center text-sm text-gray-500 gap-1 sm:gap-0">
                                    <span class="truncate">By {{ $blog->author->name }}</span>
                                    <span class="text-xs sm:text-sm">{{ $blog->published_at->diffForHumans() }}</span>
                                </div>
                                <div class="mt-3 sm:mt-4 flex justify-between items-center">
                                    <span class="text-xs text-gray-400">
                                        {{ $blog->reading_time }} min · {{ $blog->views_count }} views
                                    </span>
                                    <a href="{{ route('blogs.show', $blog->slug) }}"
                                       class="text-purple-600 hover:text-purple-700 font-medium text-sm">
                                        Read More →
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-6 sm:mt-8 overflow-x-auto">
                    {{ $blogs->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-600">No blogs found.</p>
                    @auth
                        @if(auth()->user()->isAdmin())
                            <a href="{{ route('blogs.create') }}" class="text-purple-600 hover:text-purple-700 mt-2 inline-block">
                                Create the first blog!
                            </a>
                        @endif
                    @endauth
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
    </style>
</x-app-layout>
