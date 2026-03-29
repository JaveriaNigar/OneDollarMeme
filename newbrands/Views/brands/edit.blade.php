@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-4xl mx-auto">
        <div class="bg-white rounded-3xl p-8 shadow-sm border border-gray-100">
            <div class="text-center mb-8">
                <h1 class="text-3xl font-black text-gray-900 mb-2">Edit Brand</h1>
                <p class="text-gray-600 font-bold">Update your brand information</p>
            </div>

            <form method="POST" action="{{ route('brands.update', $brand) }}" enctype="multipart/form-data">
                @csrf
                @method('PUT')
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-3">Company Name *</label>
                        <input type="text" 
                               name="company_name" 
                               value="{{ old('company_name', $brand->company_name) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                               placeholder="Enter your company name">
                        @error('company_name')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-3">Website URL</label>
                        <input type="url" 
                               name="website" 
                               value="{{ old('website', $brand->website) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                               placeholder="https://yourcompany.com">
                        @error('website')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-3">Brand Description</label>
                    <textarea name="brand_description"
                              rows="4"
                              class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                              placeholder="Tell us about your brand and what makes it unique...">{{ old('brand_description', $brand->brand_description) }}</textarea>
                    @error('brand_description')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
                    <div>
                        <label class="block text-gray-700 font-bold mb-3">Campaign Title</label>
                        <input type="text"
                               name="campaign_title"
                               value="{{ old('campaign_title', $brand->campaign_title) }}"
                               class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                               placeholder="e.g., Summer Meme Contest">
                        @error('campaign_title')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>

                    <div>
                        <label class="block text-gray-700 font-bold mb-3">Theme Color</label>
                        <div class="flex items-center gap-4">
                            <input type="color"
                                   name="theme_color"
                                   value="{{ old('theme_color', $brand->theme_color) }}"
                                   class="w-12 h-12 border-0 rounded-lg cursor-pointer">
                            <input type="text"
                                   name="theme_color_text"
                                   value="{{ old('theme_color', $brand->theme_color) }}"
                                   class="w-32 bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                                   placeholder="#000000"
                                   maxlength="7">
                        </div>
                        @error('theme_color')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-3">Campaign Description</label>
                    <textarea name="campaign_description"
                              rows="4"
                              class="w-full bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                              placeholder="Describe your campaign and what kind of memes you're looking for...">{{ old('campaign_description', $brand->campaign_description) }}</textarea>
                    @error('campaign_description')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-3">Social Media Links</label>
                    <div class="space-y-3" id="social-links-container">
                        @if($brand->social_links && is_array($brand->social_links))
                            @foreach($brand->social_links as $index => $link)
                                <div class="flex items-center gap-3">
                                    <input type="url" 
                                           name="social_links[]" 
                                           value="{{ $link }}"
                                           class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                                           placeholder="https://facebook.com/yourbrand">
                                    <button type="button" class="remove-social-link text-red-500 hover:text-red-700">
                                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                        </svg>
                                    </button>
                                </div>
                            @endforeach
                        @endif
                    </div>
                    <button type="button" id="add-social-link" class="mt-3 text-purple font-bold hover:text-orange transition-colors">
                        + Add Another Social Link
                    </button>
                    @error('social_links')
                        <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                    @enderror
                </div>

                <div class="mb-6">
                    <label class="block text-gray-700 font-bold mb-3">Upload Brand Logo</label>
                    <div class="border-2 border-dashed border-gray-200 rounded-2xl p-8 text-center hover:border-purple/30 transition-colors">
                        <input type="file" 
                               name="logo" 
                               accept="image/*"
                               class="hidden" 
                               id="logo-upload">
                        <label for="logo-upload" class="cursor-pointer">
                            <div class="w-16 h-16 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-4">
                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <p class="text-gray-600 font-bold mb-1">Click to upload logo</p>
                            <p class="text-gray-400 text-sm">PNG, JPG, GIF up to 10MB</p>
                        </label>
                        @error('logo')
                            <p class="text-red-500 text-xs mt-2">{{ $message }}</p>
                        @enderror
                    </div>
                    
                    @if($brand->logo)
                        <div class="mt-4 text-center">
                            <p class="text-gray-600 font-bold mb-2">Current Logo:</p>
                            <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->company_name }}" class="w-32 h-32 object-contain mx-auto rounded-lg">
                        </div>
                    @endif
                </div>

                <div class="flex flex-col sm:flex-row gap-4">
                    <button type="submit" class="flex-1 bg-gradient-to-r from-purple to-orange text-white py-4 rounded-2xl font-black uppercase text-sm tracking-widest hover:from-purple-600 hover:to-orange-600 transition-all">
                        Update Brand
                    </button>
                    <a href="{{ route('brands.index') }}" class="flex-1 bg-gray-100 text-gray-700 py-4 rounded-2xl font-black uppercase text-sm tracking-widest hover:bg-gray-200 transition-all text-center">
                        Cancel
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const container = document.getElementById('social-links-container');
    const addButton = document.getElementById('add-social-link');
    
    addButton.addEventListener('click', function() {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3';
        div.innerHTML = `
            <input type="url" 
                   name="social_links[]" 
                   class="flex-1 bg-gray-50 border border-gray-200 rounded-2xl py-4 px-5 focus:ring-2 focus:ring-purple/20 focus:border-purple outline-none transition-all"
                   placeholder="https://twitter.com/yourbrand">
            <button type="button" class="remove-social-link text-red-500 hover:text-red-700">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        container.appendChild(div);
        
        // Add event listener to the remove button
        div.querySelector('.remove-social-link').addEventListener('click', function() {
            container.removeChild(div);
        });
    });
    
    // Add event listeners to existing remove buttons
    document.querySelectorAll('.remove-social-link').forEach(button => {
        button.addEventListener('click', function() {
            const parentDiv = this.closest('.flex');
            parentDiv.remove();
        });
    });
    
    // Sync color picker with text input
    const colorPicker = document.querySelector('input[type="color"]');
    const colorTextInput = document.querySelector('input[name="theme_color_text"]');
    
    colorPicker.addEventListener('input', function() {
        colorTextInput.value = this.value;
    });
    
    colorTextInput.addEventListener('input', function() {
        if (/^#[0-9A-F]{6}$/i.test(this.value)) {
            colorPicker.value = this.value;
        }
    });
});
</script>
@endsection