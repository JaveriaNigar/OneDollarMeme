<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Admin - Blog Management') }}
            </h2>
            <a href="{{ route('blogs.create') }}" class="bg-purple-600 hover:bg-purple-700 text-white px-4 py-2 rounded-lg transition">
                <i class="bi bi-plus-circle me-2"></i>Create New Blog
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Stats Cards -->
            <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-6">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-purple-100 rounded-lg p-3">
                            <i class="bi bi-journal-text text-purple-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Blogs</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $blogs->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-lg p-3">
                            <i class="bi bi-check-circle text-green-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Published</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $blogs->where('status', 'published')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-lg p-3">
                            <i class="bi bi-file-earmark text-yellow-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Drafts</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $blogs->where('status', 'draft')->count() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-gray-100 rounded-lg p-3">
                            <i class="bi bi-eye text-gray-600 text-2xl"></i>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Views</p>
                            <p class="text-2xl font-bold text-gray-900">{{ $blogs->sum('views_count') }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Blogs Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6">
                    @if($blogs->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Blog</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Author</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Views</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Created</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Actions</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($blogs as $blog)
                                        <tr class="hover:bg-gray-50">
                                            <td class="px-6 py-4">
                                                <div class="flex items-center">
                                                    @if($blog->featured_image)
                                                        <img src="{{ asset('storage/' . $blog->featured_image) }}" alt="{{ $blog->title }}" class="h-10 w-10 rounded-lg object-cover mr-3">
                                                    @endif
                                                    <div>
                                                        <div class="text-sm font-medium text-gray-900">{{ $blog->title }}</div>
                                                        <div class="text-sm text-gray-500">{{ Str::limit($blog->excerpt, 50) }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <img src="{{ $blog->author->profile_photo_url }}" alt="{{ $blog->author->name }}" class="h-8 w-8 rounded-full mr-2">
                                                    <div class="text-sm text-gray-900">{{ $blog->author->name }}</div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <form action="{{ route('admin.blogs.status', $blog) }}" method="POST" class="inline">
                                                    @csrf
                                                    <select name="status" onchange="this.form.submit()" class="text-xs font-semibold rounded-full px-3 py-1 
                                                        @if($blog->status === 'published') bg-green-100 text-green-800
                                                        @elseif($blog->status === 'draft') bg-yellow-100 text-yellow-800
                                                        @else bg-gray-100 text-gray-800 @endif">
                                                        <option value="draft" {{ $blog->status === 'draft' ? 'selected' : '' }}>Draft</option>
                                                        <option value="published" {{ $blog->status === 'published' ? 'selected' : '' }}>Published</option>
                                                        <option value="archived" {{ $blog->status === 'archived' ? 'selected' : '' }}>Archived</option>
                                                    </select>
                                                </form>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                                {{ number_format($blog->views_count) }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $blog->created_at->format('M d, Y') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                                <div class="flex gap-2">
                                                    <a href="{{ route('blogs.show', $blog->slug) }}" class="text-purple-600 hover:text-purple-900" title="View">
                                                        <i class="bi bi-eye"></i>
                                                    </a>
                                                    <a href="{{ route('blogs.edit', $blog) }}" class="text-blue-600 hover:text-blue-900" title="Edit">
                                                        <i class="bi bi-pencil"></i>
                                                    </a>
                                                    <form action="{{ route('blogs.destroy', $blog) }}" method="POST" class="inline" onsubmit="return confirm('Are you sure you want to delete this blog?');">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" class="text-red-600 hover:text-red-900" title="Delete">
                                                            <i class="bi bi-trash"></i>
                                                        </button>
                                                    </form>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-6">
                            {{ $blogs->links() }}
                        </div>
                    @else
                        <div class="text-center py-12">
                            <i class="bi bi-journal-x text-gray-400" style="font-size: 4rem;"></i>
                            <h3 class="mt-4 text-lg font-medium text-gray-900">No blogs found</h3>
                            <p class="mt-2 text-sm text-gray-500">Get started by creating your first blog post.</p>
                            <a href="{{ route('blogs.create') }}" class="mt-4 inline-flex items-center px-4 py-2 border border-transparent shadow-sm text-sm font-medium rounded-md text-white bg-purple-600 hover:bg-purple-700">
                                <i class="bi bi-plus-circle mr-2"></i>Create Blog
                            </a>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
