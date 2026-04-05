<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('My Blogs') }}
            </h2>
            <a href="{{ route('blogs.create') }}" 
               class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                Create New Blog
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            @if($blogs->count() > 0)
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    @foreach($blogs as $blog)
                        <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg hover:shadow-lg transition">
                            @if($blog->featured_image)
                                <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                                     alt="{{ $blog->title }}"
                                     class="w-full h-48 object-cover">
                            @endif
                            <div class="p-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-2">
                                    {{ $blog->title }}
                                </h3>
                                <div class="flex items-center justify-between mb-4">
                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full 
                                        @if($blog->status === 'published') bg-green-100 text-green-800
                                        @elseif($blog->status === 'draft') bg-yellow-100 text-yellow-800
                                        @else bg-gray-100 text-gray-800
                                        @endif">
                                        {{ ucfirst($blog->status) }}
                                    </span>
                                    <span class="text-sm text-gray-500">
                                        {{ $blog->updated_at->diffForHumans() }}
                                    </span>
                                </div>
                                <p class="text-gray-600 text-sm mb-4">
                                    {{ $blog->excerpt }}
                                </p>
                                <div class="flex justify-between items-center text-sm text-gray-500 mb-4">
                                    <span>{{ $blog->views_count }} views</span>
                                    <span>{{ $blog->reading_time }} min read</span>
                                </div>
                                <div class="flex gap-2">
                                    <a href="{{ route('blogs.show', $blog->slug) }}" 
                                       class="flex-1 text-center bg-purple-100 hover:bg-purple-200 text-purple-700 px-4 py-2 rounded-lg transition text-sm">
                                        View
                                    </a>
                                    <a href="{{ route('blogs.edit', $blog) }}" 
                                       class="flex-1 text-center bg-blue-100 hover:bg-blue-200 text-blue-700 px-4 py-2 rounded-lg transition text-sm">
                                        Edit
                                    </a>
                                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST" 
                                          onsubmit="return confirm('Delete this blog?');"
                                          class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="w-full bg-red-100 hover:bg-red-200 text-red-700 px-4 py-2 rounded-lg transition text-sm">
                                            Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>

                <!-- Pagination -->
                <div class="mt-8">
                    {{ $blogs->links() }}
                </div>
            @else
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6 text-center">
                    <p class="text-gray-600 mb-4">You haven't created any blogs yet.</p>
                    <a href="{{ route('blogs.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2 rounded-lg transition inline-block">
                        Create Your First Blog
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
