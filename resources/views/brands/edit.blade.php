@extends('layouts.brands_blank')

@section('title', 'Edit Brand Campaign - OneDollarMeme')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        
        <!-- Header -->
        <div class="mb-8 text-center">
            <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit Brand Campaign</h1>
            <p class="text-gray-600">Update your brand campaign information</p>
        </div>

        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-red-800 font-bold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Form -->
        <form action="{{ route('brands.update', $brand->id) }}" method="POST" enctype="multipart/form-data" id="campaign-form">
            @csrf
            @method('PUT')

            <!-- Step 1: Brand Account -->
            <div id="step-1" class="step-content bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-6">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Step 1: Brand Account</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Name <span class="text-red-500">*</span></label>
                        <input type="text" name="company_name" value="{{ old('company_name', $brand->company_name) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Email <span class="text-red-500">*</span></label>
                        <input type="email" name="contact_email" value="{{ old('contact_email', $brand->contact_email) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                            <input type="url" name="website" value="{{ old('website', $brand->website) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tags</label>
                            <input type="text" name="tags" value="{{ old('tags', is_array($brand->tags) ? implode(', ', $brand->tags) : $brand->tags) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                        </div>
                    </div>

                    <!-- Logo -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3 text-center">Brand Logo</label>
                        <div class="flex flex-col items-center gap-3">
                            <div class="w-64 h-64 rounded-xl border-2 border-dashed border-gray-300 flex items-center justify-center bg-gray-50 overflow-hidden">
                                @if($brand->logo)
                                    <img id="logo-preview-img" src="{{ asset('storage/' . $brand->logo) }}" class="w-full h-full object-contain">
                                @else
                                    <img id="logo-preview-img" src="{{ asset('WhatsApp Image 2026-02-12 at 12.16.49 PM.jpeg') }}" class="w-full h-full object-contain">
                                @endif
                            </div>
                            <input type="file" name="brand_logo" id="brand_logo_input" class="hidden" accept="image/*" onchange="previewLogo(event)">
                            <button type="button" onclick="document.getElementById('brand_logo_input').click()" class="bg-purple-50 text-purple-700 px-6 py-2 rounded-lg font-semibold hover:bg-purple-100 transition">Upload Logo</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Step 2: Campaign Details -->
            <div id="step-2" class="step-content bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-6 hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Step 2: Campaign Details</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Campaign Title <span class="text-red-500">*</span></label>
                        <input type="text" name="campaign_title" value="{{ old('campaign_title', $brand->campaign_title) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Product Description <span class="text-red-500">*</span></label>
                        <textarea name="product_content" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none" required>{{ old('product_content', $brand->product_content) }}</textarea>
                    </div>

                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Start Date <span class="text-red-500">*</span></label>
                            <input type="date" name="start_date" value="{{ old('start_date', $brand->start_date?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">End Date <span class="text-red-500">*</span></label>
                            <input type="date" name="end_date" value="{{ old('end_date', $brand->end_date?->format('Y-m-d')) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                        </div>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">✓ Do's Guidelines</label>
                        <textarea name="dos_guidelines" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none">{{ old('dos_guidelines', $brand->dos_guidelines) }}</textarea>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">✗ Don'ts Guidelines</label>
                        <textarea name="donts_guidelines" rows="4" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none">{{ old('donts_guidelines', $brand->donts_guidelines) }}</textarea>
                    </div>
                </div>
            </div>

            <!-- Step 3: Target & Budget -->
            <div id="step-3" class="step-content bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-6 hidden">
                <h2 class="text-2xl font-bold text-gray-800 mb-6">Step 3: Target & Budget</h2>
                
                <div class="space-y-6">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Campaign Goal <span class="text-red-500">*</span></label>
                        <select name="campaign_goal" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                            <option value="">Select Goal</option>
                            <option value="Brand Awareness" {{ old('campaign_goal', $brand->campaign_goal) == 'Brand Awareness' ? 'selected' : '' }}>Brand Awareness</option>
                            <option value="User Engagement" {{ old('campaign_goal', $brand->campaign_goal) == 'User Engagement' ? 'selected' : '' }}>User Engagement</option>
                            <option value="Product Launch" {{ old('campaign_goal', $brand->campaign_goal) == 'Product Launch' ? 'selected' : '' }}>Product Launch</option>
                            <option value="Drive Sales" {{ old('campaign_goal', $brand->campaign_goal) == 'Drive Sales' ? 'selected' : '' }}>Drive Sales</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prize Amount (USD) <span class="text-red-500">*</span></label>
                        <input type="number" 
                               name="prize_amount" 
                               id="prize_amount"
                               value="{{ old('prize_amount', $brand->prize_amount) }}" 
                               placeholder="e.g., 500"
                               class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" 
                               min="100" 
                               required
                               oninput="updatePaymentSummary()">
                        <p class="text-xs text-gray-500 mt-1">Minimum prize amount is $100</p>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Target Audience Location <span class="text-red-500">*</span></label>
                        <select name="audience_location" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                            <option value="">Select Location</option>
                            <option value="Global" {{ old('audience_location', $brand->audience_location) == 'Global' ? 'selected' : '' }}>Global</option>
                            <option value="North America" {{ old('audience_location', $brand->audience_location) == 'North America' ? 'selected' : '' }}>North America</option>
                            <option value="Europe" {{ old('audience_location', $brand->audience_location) == 'Europe' ? 'selected' : '' }}>Europe</option>
                            <option value="Asia" {{ old('audience_location', $brand->audience_location) == 'Asia' ? 'selected' : '' }}>Asia</option>
                        </select>
                    </div>
                </div>
            </div>

            <!-- Step 4: Campaign Creatives + Payment -->
            <div id="step-4" class="step-content bg-white rounded-2xl p-8 shadow-sm border border-gray-200 mb-6 hidden">
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Creatives Content -->
                    <div class="lg:col-span-2 space-y-6">
                        <h2 class="text-2xl font-bold text-gray-800 mb-6">Campaign Creatives</h2>

                        <!-- Campaign Images (up to 5) -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-3">Campaign Images <span class="text-gray-400 font-normal">(up to 5 images)</span> <span class="text-red-500">*</span></label>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-purple-400 transition cursor-pointer" onclick="document.getElementById('product_images').click()">
                                <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <p class="text-gray-600 font-medium mb-1">Upload Campaign Images</p>
                                <p class="text-sm text-gray-500">Select up to 5 images (all formats)</p>
                                <input type="file" name="product_images[]" id="product_images" class="hidden" accept="image/*" multiple onchange="previewMultipleFiles(event, 'product-images-preview', 5)">
                            </div>
                            <div id="product-images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4"></div>
                            
                            <!-- Show existing images -->
                            @if($brand->product_images && is_array($brand->product_images) && count($brand->product_images) > 0)
                                <div class="mt-4">
                                    <p class="text-sm font-semibold text-gray-700 mb-2">Existing Images:</p>
                                    <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                                        @foreach($brand->product_images as $image)
                                            <div class="relative aspect-square rounded-lg overflow-hidden border-2 border-gray-200">
                                                <img src="{{ asset('storage/' . $image) }}" class="w-full h-full object-cover">
                                            </div>
                                        @endforeach
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Image Description -->
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Image Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                            <textarea
                                name="image_description"
                                id="image_description"
                                rows="3"
                                placeholder="Describe what these images represent for your campaign..."
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none">{{ old('image_description', $brand->image_description) }}</textarea>
                        </div>

                        <!-- Theme Color & Slogan -->
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Theme Color</label>
                                <div class="flex items-center gap-4">
                                    <input type="color" name="theme_color" id="theme_color" value="{{ old('theme_color', $brand->theme_color ?? '#6f42c1') }}" class="w-12 h-12 border-0 rounded-lg cursor-pointer">
                                    <input type="text" name="theme_color_text" id="theme_color_text" value="{{ old('theme_color', $brand->theme_color ?? '#6f42c1') }}" class="w-32 px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                                </div>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Slogan</label>
                                <input type="text" name="slogan" value="{{ old('slogan', $brand->slogan) }}" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                            </div>
                        </div>
                    </div>

                    <!-- Payment Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 sticky top-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Summary</h3>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Prize Budget</span>
                                    <span class="font-semibold text-gray-800" id="review-prize-pool">${{ number_format($brand->prize_amount ?? 100, 2) }}</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Platform Fee</span>
                                    <span class="font-semibold text-gray-800" id="review-platform-fee">$100</span>
                                </div>
                                <div class="h-px bg-gray-200"></div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">Total</span>
                                    <span class="text-3xl font-black text-purple-600" id="review-total">${{ number_format(($brand->prize_amount ?? 100) + 100, 2) }}</span>
                                </div>
                            </div>

                            <!-- Simple Update Button (No Payment) -->
                            <button
                                type="button"
                                onclick="submitWithoutPayment()"
                                id="simple-update-btn"
                                class="w-full px-6 py-4 bg-gray-100 text-gray-700 rounded-xl font-bold hover:bg-gray-200 transition-all shadow-sm flex items-center justify-center gap-2 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                </svg>
                                Update Info Only
                            </button>

                            <!-- Pay & Update Button -->
                            <button
                                type="button"
                                onclick="openPaymentModal()"
                                id="final-submit-btn"
                                class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg flex items-center justify-center gap-2 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Pay & Update Campaign
                            </button>

                            <p class="text-xs text-gray-500 text-center leading-relaxed mb-2">
                                <strong class="text-gray-700">Update Info Only:</strong> Edit details without payment
                            </p>
                            <p class="text-xs text-gray-500 text-center leading-relaxed">
                                <strong class="text-gray-700">Pay & Update:</strong> Change prize amount + payment
                            </p>

                            <div class="mt-6 pt-6 border-t border-gray-200">
                                <p class="text-xs font-semibold text-gray-500 uppercase tracking-wide mb-3">Payment Methods</p>
                                <div class="flex gap-2">
                                    <div class="bg-gray-100 p-2 rounded">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32"><rect width="48" height="32" rx="4" fill="#1434CB"/></svg>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32"><rect width="48" height="32" rx="4" fill="#EB001B"/></svg>
                                    </div>
                                    <div class="bg-gray-100 p-2 rounded">
                                        <svg class="h-6 w-10" viewBox="0 0 48 32"><rect width="48" height="32" rx="4" fill="#00457C"/></svg>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="flex justify-between">
                <button type="button" id="prev-btn" class="px-6 py-3 bg-gray-200 text-gray-700 rounded-lg font-semibold hover:bg-gray-300 transition hidden">← Previous</button>
                <div class="flex gap-3">
                    <button type="button" id="next-btn" class="px-6 py-3 bg-purple-600 text-white rounded-lg font-semibold hover:bg-purple-700 transition">Next →</button>
                </div>
            </div>
        </form>

        <div class="mt-6 text-center">
            <a href="{{ route('brands.index') }}" class="text-gray-600 hover:text-gray-800">← Back to My Brands</a>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
        <!-- Modal Header -->
        <div class="bg-gradient-to-r from-purple-600 to-blue-600 px-6 py-4">
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-3">
                    <svg class="w-8 h-8 text-white" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M17.84 7.14c-1.36-.65-3.12-1.06-5.04-1.06-4.48 0-8 2.24-8 5.6s3.52 5.6 8 5.6c1.92 0 3.68-.41 5.04-1.06l.56 1.68c-1.6.8-3.68 1.28-5.92 1.28-5.28 0-9.44-2.8-9.44-7.5s4.16-7.5 9.44-7.5c2.24 0 4.32.48 5.92 1.28l-.56 1.68zM12 4.64c.8 0 1.6.08 2.32.24l-.8 2.4c-.48-.16-1.04-.24-1.6-.24-2.4 0-4.24 1.2-4.24 3.2s1.84 3.2 4.24 3.2c.56 0 1.12-.08 1.6-.24l.8 2.4c-.72.16-1.52.24-2.32.24-3.52 0-6.24-1.92-6.24-5.6s2.72-5.6 6.24-5.6z"/>
                    </svg>
                    <span class="text-white font-bold text-lg">Secure Payment</span>
                </div>
                <button onclick="closePaymentModal()" class="text-white hover:text-gray-200 transition">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Modal Body -->
        <div class="p-6">
            <div class="mb-6">
                <div class="flex justify-between items-center mb-4">
                    <span class="text-gray-600">Total Amount</span>
                    <span class="text-3xl font-black text-purple-600" id="modal-total-amount">${{ number_format(($brand->prize_amount ?? 100) + 100, 2) }}</span>
                </div>
                <div class="space-y-2 text-sm">
                    <div class="flex justify-between">
                        <span class="text-gray-600">Prize Budget</span>
                        <span class="font-semibold" id="modal-prize-budget">${{ number_format($brand->prize_amount ?? 100, 2) }}</span>
                    </div>
                    <div class="flex justify-between">
                        <span class="text-gray-600">Platform Fee</span>
                        <span class="font-semibold">$100</span>
                    </div>
                </div>
            </div>

            <!-- Card Details Form -->
            <form id="payment-form" action="{{ route('brands.update', $brand->id) }}" method="POST">
                @csrf
                @method('PUT')
                <input type="hidden" name="prize_amount" id="modal-prize-amount-hidden" value="{{ $brand->prize_amount ?? 100 }}">
                <input type="hidden" name="platform_fee" value="100">
                
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Card Number</label>
                        <input type="text" placeholder="1234 5678 9012 3456" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                    </div>
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Expiry Date</label>
                            <input type="text" placeholder="MM/YY" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">CVC</label>
                            <input type="text" placeholder="123" class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition" required>
                        </div>
                    </div>
                </div>

                <button type="submit" class="w-full mt-6 px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg" id="modal-submit-btn">
                    Pay ${{ number_format(($brand->prize_amount ?? 100) + 100, 2) }} & Update Campaign
                </button>
            </form>
        </div>
    </div>
</div>

<script>
function previewLogo(event) {
    const reader = new FileReader();
    reader.onload = function() {
        document.getElementById('logo-preview-img').src = reader.result;
    }
    if (event.target.files[0]) {
        reader.readAsDataURL(event.target.files[0]);
    }
}

// Color picker sync
const colorPicker = document.getElementById('theme_color');
const colorTextInput = document.getElementById('theme_color_text');
if (colorPicker && colorTextInput) {
    colorPicker.addEventListener('input', function() {
        colorTextInput.value = this.value;
    });
    colorTextInput.addEventListener('input', function() {
        if (/^#[0-9A-Fa-f]{6}$/.test(this.value)) {
            colorPicker.value = this.value;
        }
    });
}

// Multi-step form logic
let currentStep = 1;
const totalSteps = 4;

function updateStepDisplay() {
    for (let i = 1; i <= totalSteps; i++) {
        const step = document.getElementById('step-' + i);
        if (step) step.classList.add('hidden');
    }
    const currentStepEl = document.getElementById('step-' + currentStep);
    if (currentStepEl) currentStepEl.classList.remove('hidden');
    
    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');
    const submitBtn = document.getElementById('submit-btn');
    
    if (prevBtn) prevBtn.classList.toggle('hidden', currentStep === 1);
    if (nextBtn) nextBtn.classList.toggle('hidden', currentStep === totalSteps);
    if (submitBtn) submitBtn.classList.toggle('hidden', currentStep !== totalSteps);
}

// Initialize
updateStepDisplay();

// Next button
const nextBtn = document.getElementById('next-btn');
if (nextBtn) {
    nextBtn.addEventListener('click', function() {
        if (currentStep < totalSteps) {
            currentStep++;
            updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
}

// Previous button
const prevBtn = document.getElementById('prev-btn');
if (prevBtn) {
    prevBtn.addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
}

// Form submit - disable button to prevent double submission
const form = document.getElementById('campaign-form');
const submitBtn = document.getElementById('submit-btn');
if (form && submitBtn) {
    form.addEventListener('submit', function() {
        submitBtn.disabled = true;
        submitBtn.innerHTML = 'Updating...';
    });
}

// Preview multiple files (for campaign images)
function previewMultipleFiles(event, previewId, maxFiles) {
    const input = event.target;
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files.length > maxFiles) {
        alert(`You can only upload up to ${maxFiles} images`);
        input.value = '';
        return;
    }

    Array.from(input.files).forEach((file, index) => {
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                const div = document.createElement('div');
                div.className = 'relative';
                div.innerHTML = `
                    <img src="${e.target.result}" class="h-32 w-full object-cover rounded-lg border-2 border-gray-200">
                    <button type="button" onclick="removeMultipleFile(event, '${input.id}', ${index})" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">×</button>
                `;
                preview.appendChild(div);
            }
            reader.readAsDataURL(file);
        }
    });
}

function removeMultipleFile(event, inputId, index) {
    event.preventDefault();
    const input = document.getElementById(inputId);
    const dt = new DataTransfer();
    Array.from(input.files).forEach((file, i) => {
        if (i !== index) dt.items.add(file);
    });
    input.files = dt.files;
    event.target.closest('div').remove();
}

// Payment Modal Functions
function openPaymentModal() {
    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');
}

function closePaymentModal() {
    const modal = document.getElementById('payment-modal');
    modal.classList.add('hidden');
}

// Close modal on outside click
document.getElementById('payment-modal')?.addEventListener('click', function(e) {
    if (e.target === this) {
        closePaymentModal();
    }
});

// Update Payment Summary dynamically
function updatePaymentSummary() {
    const prizeAmountInput = document.getElementById('prize_amount');
    const prizeAmount = parseFloat(prizeAmountInput.value) || 100;
    const platformFee = 100;
    const total = prizeAmount + platformFee;
    
    // Update payment summary sidebar
    const prizePoolEl = document.getElementById('review-prize-pool');
    const platformFeeEl = document.getElementById('review-platform-fee');
    const totalEl = document.getElementById('review-total');
    
    if (prizePoolEl) prizePoolEl.textContent = '$' + prizeAmount.toFixed(2);
    if (platformFeeEl) platformFeeEl.textContent = '$100';
    if (totalEl) totalEl.textContent = '$' + total.toFixed(2);
    
    // Update modal total
    const modalTotalEl = document.getElementById('modal-total-amount');
    const modalPrizeBudgetEl = document.getElementById('modal-prize-budget');
    if (modalTotalEl) modalTotalEl.textContent = '$' + total.toFixed(2);
    if (modalPrizeBudgetEl) modalPrizeBudgetEl.textContent = '$' + prizeAmount.toFixed(2);
    
    // Update modal form hidden input
    const modalPrizeInput = document.getElementById('modal-prize-amount-hidden');
    if (modalPrizeInput) modalPrizeInput.value = prizeAmount;
    
    // Update modal submit button
    const modalSubmitBtn = document.getElementById('modal-submit-btn');
    if (modalSubmitBtn) modalSubmitBtn.textContent = 'Pay $' + total.toFixed(2) + ' & Update Campaign';
}

// Submit without payment (simple update)
function submitWithoutPayment() {
    const form = document.getElementById('campaign-form');
    const originalAction = form.action;
    
    // Add a flag to indicate no payment
    const noPaymentInput = document.createElement('input');
    noPaymentInput.type = 'hidden';
    noPaymentInput.name = 'no_payment';
    noPaymentInput.value = '1';
    form.appendChild(noPaymentInput);
    
    form.submit();
}

// Open payment modal and update values
function openPaymentModal() {
    // Update modal with current values before opening
    updatePaymentSummary();
    
    const modal = document.getElementById('payment-modal');
    modal.classList.remove('hidden');
}

// Final submission with "payment" details
const paymentForm = document.getElementById('payment-form');
if (paymentForm) {
    paymentForm.addEventListener('submit', function(e) {
        e.preventDefault();
        
        const mainForm = document.getElementById('campaign-form');
        
        // Add a flag for paid status
        const isPaidInput = document.createElement('input');
        isPaidInput.type = 'hidden';
        isPaidInput.name = 'is_paid_update';
        isPaidInput.value = '1';
        mainForm.appendChild(isPaidInput);

        // Copy any necessary fields from payment modal if needed
        // (Currently the controller only needs the prize_amount which is already in mainForm)
        
        mainForm.submit();
    });
}
</script>
@endsection
