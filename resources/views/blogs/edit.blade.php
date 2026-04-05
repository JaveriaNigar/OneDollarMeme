<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Blog') }}
        </h2>
    </x-slot>

    <div class="py-4 sm:py-12">
        <div class="max-w-4xl mx-auto sm:px-6 lg:px-8 px-4 sm:px-6">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-4 sm:p-8">
                    <form action="{{ route('blogs.update', $blog) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')

                        <!-- Title -->
                        <div class="mb-6">
                            <label for="title" class="block text-sm font-medium text-gray-700 mb-2">
                                Title <span class="text-red-500">*</span>
                            </label>
                            <input type="text" 
                                   name="title" 
                                   id="title"
                                   value="{{ old('title', $blog->title) }}"
                                   required
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('title') border-red-500 @enderror"
                                   placeholder="Enter blog title...">
                            @error('title')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                        </div>

                        <!-- Featured Image -->
                        <div class="mb-6">
                            <label for="featured_image" class="block text-sm font-medium text-gray-700 mb-2">
                                Featured Image
                            </label>
                            @if($blog->featured_image)
                                <div class="mb-4">
                                    <img src="{{ asset('storage/' . $blog->featured_image) }}" 
                                         alt="Current image"
                                         class="max-h-48 rounded-lg">
                                    <p class="text-sm text-gray-500 mt-2">Current featured image</p>
                                </div>
                            @endif
                            <input type="file" 
                                   name="featured_image" 
                                   id="featured_image"
                                   accept="image/*"
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('featured_image') border-red-500 @enderror"
                                   onchange="previewImage(event)">
                            @error('featured_image')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <div id="image_preview" class="mt-4 hidden">
                                <img id="preview" src="#" alt="Preview" class="max-h-48 rounded-lg">
                            </div>
                        </div>

                        <!-- Content -->
                        <div class="mb-6">
                            <label for="content" class="block text-sm font-medium text-gray-700 mb-2">
                                Content <span class="text-red-500">*</span>
                            </label>
                            <textarea name="content"
                                      id="content"
                                      rows="15"
                                      class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('content') border-red-500 @enderror"
                                      placeholder="Write your blog content here...">{{ old('content', $blog->content) }}</textarea>
                            @error('content')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-2">
                                <i class="bi bi-info-circle me-1"></i>Use the editor toolbar above to add links, images, headings, and formatting. You can paste URLs directly or use the link button.
                            </p>
                        </div>

                        <!-- Meta Description -->
                        <div class="mb-6">
                            <label for="meta_description" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Description (SEO)
                            </label>
                            <textarea name="meta_description" 
                                      id="meta_description"
                                      rows="2"
                                      maxlength="160"
                                      class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('meta_description') border-red-500 @enderror"
                                      placeholder="Brief description for search engines (max 160 characters)">{{ old('meta_description', $blog->meta_description) }}</textarea>
                            @error('meta_description')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-1">
                                <span id="meta_count">0</span>/160 characters
                            </p>
                        </div>

                        <!-- Meta Keywords -->
                        <div class="mb-6">
                            <label for="meta_keywords" class="block text-sm font-medium text-gray-700 mb-2">
                                Meta Keywords (SEO)
                            </label>
                            <input type="text" 
                                   name="meta_keywords" 
                                   id="meta_keywords"
                                   value="{{ old('meta_keywords', $blog->meta_keywords) }}"
                                   class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500 @error('meta_keywords') border-red-500 @enderror"
                                   placeholder="keyword1, keyword2, keyword3">
                            @error('meta_keywords')
                                <p class="text-red-500 text-sm mt-1">{{ $message }}</p>
                            @enderror
                            <p class="text-sm text-gray-500 mt-2">
                                Separate keywords with commas
                            </p>
                        </div>

                        <!-- Status -->
                        <div class="mb-6">
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-2">
                                Status
                            </label>
                            <select name="status"
                                    id="status"
                                    class="w-full border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                <option value="draft" {{ old('status', $blog->status) === 'draft' ? 'selected' : '' }}>Draft</option>
                                <option value="published" {{ old('status', $blog->status) === 'published' ? 'selected' : '' }}>Published</option>
                                <option value="archived" {{ old('status', $blog->status) === 'archived' ? 'selected' : '' }}>Archived</option>
                            </select>
                            <p class="text-sm text-gray-500 mt-2">
                                Drafts are only visible to you. Published blogs are visible to everyone. Archived blogs are hidden from public.
                            </p>
                        </div>

                        <!-- App Links -->
                        <div class="mb-6">
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                <i class="bi bi-link-45deg me-1"></i>Add Links
                            </label>
                            <p class="text-sm text-gray-500 mb-4">
                                Add any URLs or links you want to include in this blog post.
                            </p>

                            <div id="app_links_container" class="space-y-3">
                                @php
                                    $appLinks = old('app_links', $blog->appLinks->pluck('url')->toArray());
                                @endphp
                                @foreach($appLinks as $index => $link)
                                    <div class="app-link-row flex gap-3 p-3 bg-gray-50 rounded-lg">
                                        <input type="text"
                                               name="app_links[{{ $index }}]"
                                               value="{{ $link }}"
                                               placeholder="Enter URL (e.g., https://example.com)"
                                               class="flex-1 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                                        <button type="button"
                                                onclick="this.parentElement.remove()"
                                                class="text-red-600 hover:text-red-700 px-2">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </div>
                                @endforeach
                            </div>

                            <button type="button"
                                    id="add_app_link_btn"
                                    onclick="addAppLink()"
                                    class="mt-3 text-purple-600 hover:text-purple-700 text-sm font-medium">
                                <i class="bi bi-plus-circle me-1"></i>Add Another Link
                            </button>
                        </div>

                        <!-- Submit Buttons -->
                        <div class="flex flex-col sm:flex-row gap-3">
                            <button type="submit" name="status" value="published" class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-3 rounded-lg transition w-full sm:w-auto">
                                <i class="bi bi-check-circle me-2"></i>Update & Publish
                            </button>
                            <button type="submit" name="status" value="draft" class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-3 rounded-lg transition w-full sm:w-auto">
                                <i class="bi bi-file-earmark me-2"></i>Save Draft
                            </button>
                            <a href="{{ route('blogs.show', $blog->slug) }}" class="bg-gray-500 hover:bg-gray-600 text-white px-6 py-3 rounded-lg transition text-center w-full sm:w-auto">
                                Cancel
                            </a>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    @section('scripts')
    <!-- CKEditor 5 Rich Text Editor (Free, No API Key Required) -->
    <script src="https://cdn.ckeditor.com/ckeditor5/41.0.0/classic/ckeditor.js"></script>
    <script>
        let editorInstance;
        const existingContent = @json($blog->content);

        ClassicEditor
            .create(document.querySelector('#content'), {
                initialData: existingContent,
                toolbar: [
                    'heading', '|',
                    'bold', 'italic', 'underline', 'strikethrough', '|',
                    'link', 'insertImage', 'mediaEmbed', '|',
                    'bulletedList', 'numberedList', '|',
                    'blockQuote', 'insertTable', '|',
                    'undo', 'redo'
                ],
                link: {
                    addTargetToExternalLinks: true,
                    defaultProtocol: 'https://'
                },
                image: {
                    toolbar: ['imageTextAlternative']
                },
                table: {
                    contentToolbar: ['tableColumn', 'tableRow', 'mergeTableCells']
                }
            })
            .then(editor => {
                editorInstance = editor;
                console.log('CKEditor initialized successfully');
            })
            .catch(error => {
                console.error('CKEditor initialization error:', error);
            });

        // Ensure editor data is submitted with form and validate content
        document.querySelector('form').addEventListener('submit', function(e) {
            if (editorInstance) {
                const content = editorInstance.getData();
                document.querySelector('#content').value = content;
                
                // Validate content is not empty
                if (!content || content.trim() === '') {
                    e.preventDefault();
                    alert('Please write some content for your blog post.');
                    return false;
                }
            }
        });

        // Image preview
        function previewImage(event) {
            const input = event.target;
            const preview = document.getElementById('image_preview');
            const img = document.getElementById('preview');

            if (input.files && input.files[0]) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    img.src = e.target.result;
                    preview.classList.remove('hidden');
                }
                reader.readAsDataURL(input.files[0]);
            }
        }

        // Meta description character count
        document.getElementById('meta_description').addEventListener('input', function() {
            document.getElementById('meta_count').textContent = this.value.length;
        });

        // Initialize character count
        document.getElementById('meta_count').textContent = document.getElementById('meta_description').value.length;

        // Handle submit buttons - set status based on which button is clicked
        document.querySelectorAll('button[type="submit"][name="status"]').forEach(button => {
            button.addEventListener('click', function(e) {
                // Remove name/value from other submit buttons to prevent conflicts
                document.querySelectorAll('button[type="submit"][name="status"]').forEach(btn => {
                    if (btn !== this) {
                        btn.removeAttribute('name');
                        btn.removeAttribute('value');
                    }
                });
            });
        });

        // App link counter
        let appLinkCount = {{ count($appLinks) }};

        // Add app link function
        function addAppLink() {
            const container = document.getElementById('app_links_container');
            const index = appLinkCount++;

            const linkRow = document.createElement('div');
            linkRow.className = 'app-link-row flex gap-3 p-3 bg-gray-50 rounded-lg';
            linkRow.innerHTML = `
                <input type="text"
                       name="app_links[${index}]"
                       placeholder="Enter URL (e.g., https://example.com)"
                       class="flex-1 border-gray-300 rounded-lg focus:ring-purple-500 focus:border-purple-500">
                <button type="button"
                        onclick="this.parentElement.remove()"
                        class="text-red-600 hover:text-red-700 px-2">
                    <i class="bi bi-trash"></i>
                </button>
            `;

            container.appendChild(linkRow);
        }
    </script>
    @endsection
</x-app-layout>
