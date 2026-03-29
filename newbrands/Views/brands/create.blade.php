@extends('layouts.blank')

@section('title', 'Create Brand Campaign - OneDollarMeme')

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-6xl mx-auto px-4">
        <!-- Success/Error Messages -->
        @if(session('success'))
            <div class="mb-6 bg-green-50 border border-green-200 rounded-xl p-4 flex items-start gap-3">
                <svg class="w-6 h-6 text-green-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                </svg>
                <p class="text-green-800 font-medium">{{ session('success') }}</p>
            </div>
        @endif

        @if(session('error'))
            <div id="error-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
                <div class="bg-white rounded-2xl p-8 max-w-md mx-4 shadow-2xl transform transition-all relative">
                    <!-- Close Button -->
                    <button onclick="document.getElementById('error-popup').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 transition-colors">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                    
                    <div class="text-center">
                        <div class="w-16 h-16 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <svg class="w-8 h-8 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold text-gray-900 mb-2">Dates Already Selected</h3>
                        <p class="text-gray-600 mb-6">{{ session('error') }}</p>
                        <button onclick="document.getElementById('error-popup').classList.add('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors w-full">
                            Choose Different Dates
                        </button>
                    </div>
                </div>
            </div>
        @endif

        <!-- Real-time Date Validation Popup -->
        <div id="date-validation-popup" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
            <div class="bg-white dark:bg-gray-800 rounded-2xl p-8 max-w-md mx-4 shadow-2xl transform transition-all relative">
                <!-- Close Button -->
                <button onclick="document.getElementById('date-validation-popup').classList.add('hidden')" class="absolute top-4 right-4 text-gray-400 hover:text-gray-600 dark:hover:text-gray-300 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                    </svg>
                </button>

                <div class="text-center">
                    <div class="w-16 h-16 bg-red-100 dark:bg-red-900/20 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-red-600 dark:text-red-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                        </svg>
                    </div>
                    <h3 class="text-xl font-bold text-gray-900 dark:text-gray-100 mb-2">Dates Already Selected</h3>
                    <p class="text-gray-600 dark:text-gray-400 mb-4">The selected dates are already chosen by another campaign.</p>
                    
                    <div id="conflicting-campaign-info" class="bg-gray-50 dark:bg-gray-700 rounded-lg p-4 mb-6 text-left hidden">
                        <p class="text-sm text-gray-600 dark:text-gray-400 mb-2">Conflicting Campaign:</p>
                        <p class="text-sm font-semibold text-gray-900 dark:text-gray-100" id="conflict-company"></p>
                        <p class="text-xs text-gray-500 dark:text-gray-400 mt-1" id="conflict-dates"></p>
                    </div>
                    
                    <button onclick="document.getElementById('date-validation-popup').classList.add('hidden')" class="bg-red-600 hover:bg-red-700 text-white px-6 py-2 rounded-lg font-semibold transition-colors w-full">
                        Choose Different Dates
                    </button>
                </div>
            </div>
        </div>

        @if($errors->any())
            <div class="mb-6 bg-red-50 border border-red-200 rounded-xl p-4">
                <div class="flex items-start gap-3">
                    <svg class="w-6 h-6 text-red-600 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                    </svg>
                    <div>
                        <p class="text-red-800 font-semibold mb-2">Please fix the following errors:</p>
                        <ul class="list-disc list-inside text-red-700 text-sm space-y-1">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>
        @endif

        <!-- Header -->
        <div class="mb-8 text-center relative">
            <!-- Home Button & Dark Mode Toggle -->
            <div class="absolute left-0 top-1/2 -translate-y-1/2 flex items-center gap-4">
                <a href="{{ route('home') }}" class="flex items-center gap-2 text-gray-600 dark:text-gray-400 hover:text-purple dark:hover:text-purple-400 transition-colors">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                    </svg>
                    <span class="font-semibold">Home</span>
                </a>
                
                <!-- Dark Mode Toggle -->
                <!-- <button onclick="toggleDarkMode()" class="p-2 rounded-full hover:bg-gray-200 dark:hover:bg-gray-700 transition-colors">
                    <svg id="dark-mode-icon" class="w-5 h-5 text-gray-600 dark:text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>
                    </svg>
                </button> -->
            </div>
            
            <!-- Brand Logo -->
            <div class="mb-6">
                <img src="{{ asset('WhatsApp Image 2026-02-12 at 12.16.49 PM.jpeg') }}" alt="Brand Logo" class="w-32 h-32 mx-auto object-contain rounded-2xl shadow-lg">
            </div>
            <h1 class="text-3xl font-bold text-gray-800">Create Brand Campaign</h1>
        </div>

        <!-- Progress Steps -->
        <div class="mb-8">
            <div class="flex items-center justify-between max-w-3xl mx-auto">
                <!-- Step 1 -->
                <div class="flex items-center flex-1">
                    <div class="flex flex-col items-center">
                        <div id="step-indicator-1" class="w-12 h-12 rounded-full bg-purple-600 text-white flex items-center justify-center font-bold text-lg shadow-lg transition-all">
                            1
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-700">Brand Account</span>
                    </div>
                    <div id="line-1" class="flex-1 h-1 bg-gray-300 mx-2"></div>
                </div>

                <!-- Step 2 -->
                <div class="flex items-center flex-1">
                    <div class="flex flex-col items-center">
                        <div id="step-indicator-2" class="w-12 h-12 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg transition-all">
                            2
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-600">Campaign Details</span>
                    </div>
                    <div id="line-2" class="flex-1 h-1 bg-gray-300 mx-2"></div>
                </div>

                <!-- Step 3 -->
                <div class="flex items-center flex-1">
                    <div class="flex flex-col items-center">
                        <div id="step-indicator-3" class="w-12 h-12 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg transition-all">
                            3
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-600">Target & Budget</span>
                    </div>
                    <div id="line-3" class="flex-1 h-1 bg-gray-300 mx-2"></div>
                </div>

                <!-- Step 4 -->
                <div class="flex items-center">
                    <div class="flex flex-col items-center">
                        <div id="step-indicator-4" class="w-12 h-12 rounded-full bg-gray-300 text-gray-600 flex items-center justify-center font-bold text-lg transition-all">
                            4
                        </div>
                        <span class="mt-2 text-sm font-semibold text-gray-600">Campaign Creatives</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Form -->
        <form action="{{ route('brands.store') }}" method="POST" enctype="multipart/form-data" id="campaign-form" novalidate>
            @csrf
            @if($draft)
                <input type="hidden" name="draft_id" value="{{ $draft->id }}">
            @endif

            <!-- ========== STEP 1: Brand Account ========== -->
        <!-- ========== STEP 1: Brand Account ========== -->
            <div id="step-1" class="step-content bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Brand Account</h2>
                    <p class="text-gray-600">Tell us about your brand's basic information</p>
                </div>

                <div class="space-y-6">

                    <!-- Row 1: Brand Name + Contact Email -->
                    <div class="grid md:grid-cols-1 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Name <span class="text-red-500">*</span></label>
                            <input
                                type="text"
                                name="company_name"
                                id="company_name"
                                placeholder="Your brand name"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                required data-validate-step="1">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Contact Email <span class="text-red-500">*</span></label>
                            <div class="relative">
                                <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-400">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                                    </svg>
                                </span>
                                <input
                                    type="email"
                                    name="contact_email"
                                    id="contact_email"
                                    placeholder="contact@yourbrand.com"
                                    class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    required data-validate-step="1">
                            </div>
                            <p class="text-xs text-gray-500 mt-1">We'll send campaign updates to this email</p>
                        </div>
                    </div>

                    <!-- Row 2: Website + Tags -->
                    <div class="grid md:grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Website</label>
                            <input
                                type="url"
                                name="website"
                                id="website"
                                placeholder="https://www.yourbrand.com"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-gray-700 mb-2">Tags <span class="text-gray-400 font-normal">(comma separated)</span></label>
                            <input
                                type="text"
                                name="tags"
                                id="tags"
                                placeholder="e.g., Snacks, Funny, Summer"
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                            <p class="text-xs text-gray-500 mt-1">Add relevant tags to help creators find your campaign</p>
                        </div>
                    </div>

                </div>
            </div>

            <!-- ========== STEP 2: Campaign Details ========== -->
            <div id="step-2" class="step-content hidden bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Campaign Details</h2>
                    <p class="text-gray-600">Tell us about your campaign and product</p>
                </div>

                <div class="space-y-6">
                    <!-- Campaign Title -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Campaign Title <span class="text-red-500">*</span></label>
                        <input
                            type="text"
                            name="campaign_title"
                            id="campaign_title"
                            placeholder="e.g., Summer Snack Launch 2024"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                            required data-validate-step="2">
                    </div>

                    <!-- Product Category -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Product Category</label>
                        <select
                            name="product_category"
                            id="product_category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition appearance-none bg-white"
                            onchange="toggleOtherField(this, 'product_category_other')">
                            <option value="">Select Category</option>
                            <option value="Food & Beverage">Food & Beverage</option>
                            <option value="Technology">Technology</option>
                            <option value="Lifestyle">Lifestyle</option>
                            <option value="Fashion">Fashion</option>
                            <option value="Entertainment">Entertainment</option>
                            <option value="Gaming">Gaming</option>
                            <option value="Sports">Sports</option>
                            <option value="Beauty">Beauty</option>
                            <option value="Other">Other</option>
                        </select>
                        <input
                            type="text"
                            name="product_category_other"
                            id="product_category_other"
                            placeholder="Please specify your product category"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition mt-2 hidden">
                    </div>

                    {{-- COMMENTED OUT - Subject Category (duplicate of product category, can re-enable if needed separately)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Subject Category <span class="text-red-500">*</span></label>
                        <select name="subject_category" id="subject_category" ...>
                            <option value="Education">Education</option>
                            <option value="Health & Fitness">Health & Fitness</option>
                            <option value="Travel">Travel</option>
                            <option value="Finance">Finance</option>
                        </select>
                    </div>
                    --}}

                    <!-- Product Description -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Product Description <span class="text-red-500">*</span></label>
                        <textarea
                            name="product_content"
                            id="product_content"
                            rows="4"
                            placeholder="Describe your product and what kind of memes you're looking for..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none"
                            required></textarea>
                    </div>

                    {{-- COMMENTED OUT - Brand Description (separate from product description, can re-enable if needed)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Description <span class="text-red-500">*</span></label>
                        <textarea
                            name="brand_description"
                            id="brand_description"
                            rows="4"
                            placeholder="Describe your brand, what makes it unique..."
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none"
                            required></textarea>
                    </div>
                    --}}

                    <!-- Campaign Duration -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Campaign Duration <span class="text-red-500">*</span></label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <input
                                    type="date"
                                    name="start_date"
                                    id="start_date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    required
                                    min="<?php echo date('Y-m-d', strtotime('+1 day')); ?>"
                                    onchange="validateDates()">
                                <p class="text-xs text-gray-500 mt-1">Start Date (minimum tomorrow)</p>
                            </div>
                            <div>
                                <input
                                    type="date"
                                    name="end_date"
                                    id="end_date"
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    required
                                    min="<?php echo date('Y-m-d', strtotime('+2 days')); ?>"
                                    onchange="validateDates()">
                                <p class="text-xs text-gray-500 mt-1">End Date (must be after start date)</p>
                            </div>
                        </div>
                    </div>

                    <!-- Do's & Don'ts Guidelines -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Do's & Don'ts Guidelines</label>
                        <textarea
                            name="guidelines"
                            id="guidelines"
                            rows="6"
                            placeholder="Example:&#10;✓ DO: Use our logo clearly&#10;✓ DO: Keep content family-friendly&#10;✗ DON'T: Use profanity or offensive content&#10;✗ DON'T: Misrepresent the product"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none font-mono text-sm"></textarea>
                        <p class="text-xs text-gray-500 mt-2">Clear guidelines help creators make content that aligns with your brand</p>
                    </div>

                    {{-- COMMENTED OUT - Product Brand field (can re-enable if needed)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Product Brand</label>
                        <input type="text" name="product_brand" id="product_brand" placeholder="Product brand name" ...>
                    </div>
                    --}}
                </div>
            </div>

            <!-- ========== STEP 3: Target & Budget ========== -->
            <div id="step-3" class="step-content hidden bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                <div class="mb-6">
                    <h2 class="text-2xl font-bold text-gray-800 mb-2">Target & Budget</h2>
                    <p class="text-gray-600">Define your campaign's reach and budget</p>
                </div>

                <div class="space-y-6">
                    <!-- Campaign Goal -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Campaign Goal <span class="text-red-500">*</span></label>
                        <select
                            name="campaign_goal"
                            id="campaign_goal"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition appearance-none bg-white"
                            required
                            onchange="toggleOtherField(this, 'campaign_goal_other')">
                            <option value="">Select Goal</option>
                            <option value="Brand Awareness">Brand Awareness</option>
                            <option value="User Engagement">User Engagement</option>
                            <option value="Product Launch">Product Launch</option>
                            <option value="Drive Sales">Drive Sales</option>
                            <option value="Lead Generation">Lead Generation</option>
                            <option value="Other">Other</option>
                        </select>
                        <input
                            type="text"
                            name="campaign_goal_other"
                            id="campaign_goal_other"
                            placeholder="Please specify your campaign goal"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition mt-2 hidden">
                    </div>

                    <!-- Reward Type -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Reward Type <span class="text-red-500">*</span></label>
                        <select
                            name="prize_type"
                            id="prize_type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition appearance-none bg-white"
                            required
                            onchange="toggleOtherField(this, 'prize_type_other')">
                            <option value="">Select Reward Type</option>
                            <option value="Reward">Reward Only</option>
                            <option value="Prize">Prize Only</option>
                            <option value="Reward + Prize">Reward + Prize</option>
                            <option value="Other">Other</option>
                        </select>
                        <input
                            type="text"
                            name="prize_type_other"
                            id="prize_type_other"
                            placeholder="Please specify your reward type"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition mt-2 hidden">
                    </div>

                    <!-- Region / Audience Location -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Audience Region <span class="text-red-500">*</span></label>
                        <select
                            name="audience_location"
                            id="audience_location"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition appearance-none bg-white"
                            required
                            onchange="toggleOtherField(this, 'audience_location_other')">
                            <option value="">Select Region</option>
                            <option value="Global">Global</option>
                            <option value="United States">United States</option>
                            <option value="Canada">Canada</option>
                            <option value="Europe">Europe</option>
                            <option value="Asia">Asia</option>
                            <option value="Australia">Australia</option>
                            <option value="Latin America">Latin America</option>
                            <option value="Other">Other</option>
                        </select>
                        <input
                            type="text"
                            name="audience_location_other"
                            id="audience_location_other"
                            placeholder="Please specify your target region"
                            class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition mt-2 hidden">
                    </div>

                    <!-- Total Budget -->
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-2">Prize Budget <span class="text-red-500">*</span></label>
                        <div class="relative">
                            <span class="absolute left-4 top-1/2 -translate-y-1/2 text-gray-500 font-semibold">$</span>
                            <input
                                type="number"
                                name="prize_amount"
                                id="prize_amount"
                                placeholder="Enter prize budget (min $100)"
                                min="100"
                                step="1"
                                class="w-full pl-8 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                required
                                oninput="validateBudget(this); updateBudgetBreakdown()">
                        </div>
                        <p class="text-xs text-gray-500 mt-1">Minimum prize budget: $100</p>
                    </div>

                    <!-- Budget Breakdown Card -->
                 <div class="bg-purple-50 dark:bg-gray-700 border-2 border-purple-200 dark:border-gray-600 rounded-xl p-6">
    <h3 class="text-lg font-bold text-gray-800 dark:text-white mb-4">Payment Summary</h3>
    <div class="space-y-3">
        <div class="flex justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-300">Prize Budget</span>
            <span class="font-semibold text-gray-800 dark:text-white" id="budget-prize-pool">$0</span>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-gray-600 dark:text-gray-300">Platform Fee</span>
            <span class="font-semibold text-gray-800 dark:text-white">$100</span>
        </div>
        <div class="h-px bg-purple-200 dark:bg-gray-500"></div>
        <div class="flex justify-between">
            <span class="font-bold text-gray-800 dark:text-white">Total</span>
            <span class="text-xl font-black text-purple-600 dark:text-purple-400" id="budget-total">$0</span>
        </div>
    </div>
</div>

                    {{-- COMMENTED OUT - Campaign Duration fields (can re-enable if date range needed)
                    <div>
                        <label class="block text-sm font-semibold text-gray-700 mb-3">Campaign Duration <span class="text-red-500">*</span></label>
                        <div class="grid md:grid-cols-2 gap-4">
                            <div>
                                <input type="date" name="start_date" id="start_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg..." required>
                                <p class="text-xs text-gray-500 mt-1">Start Date</p>
                            </div>
                            <div>
                                <input type="date" name="end_date" id="end_date" class="w-full px-4 py-3 border border-gray-300 rounded-lg..." required>
                                <p class="text-xs text-gray-500 mt-1">End Date</p>
                            </div>
                        </div>
                    </div>
                    --}}

                    {{-- COMMENTED OUT - Prize & Selection section (can re-enable for prize type / winner selection)
                    <div class="bg-purple-50 border-2 border-purple-200 rounded-xl p-6">
                        <h3 class="text-lg font-bold text-gray-800 mb-4">Prize & Selection</h3>
                        <div class="grid md:grid-cols-2 gap-4 mb-4">
                            <div>
                                <label>Prize Type</label>
                                <select name="prize_type" id="prize_type" ...>
                                    <option value="Cash">Reward</option>
                                    <option value="Product">Product Prize</option>
                                    <option value="Cash + Product">Reward + Product</option>
                                    <option value="Gift Card">Gift Card</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                        </div>
                        <div>
                            <label>Winner Selection Method</label>
                            <select name="winner_selection" id="winner_selection" ...>
                                <option value="Most Likes">Most Likes</option>
                                <option value="Most Shares">Most Shares</option>
                                <option value="Random Draw">Random Draw</option>
                                <option value="Jury Selection">Jury Selection</option>
                                <option value="Most Creative">Most Creative (Jury + Votes)</option>
                            </select>
                        </div>
                    </div>
                    --}}
                </div>
            </div>

            <!-- ========== STEP 4: Campaign Creatives + Review ========== -->
            <div id="step-4" class="step-content hidden">
                <div class="grid lg:grid-cols-3 gap-6">
                    <!-- Creatives + Review Content -->
                    <div class="lg:col-span-2 space-y-6">

                        <!-- Campaign Creatives -->
                        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                            <div class="mb-6">
                                <h2 class="text-2xl font-bold text-gray-800 mb-2">Campaign Creatives</h2>
                                <p class="text-gray-600">Upload images and add your campaign's creative details</p>
                            </div>

                            <div class="space-y-6">
                                <!-- Product Images (up to 5) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Campaign Images <span class="text-gray-400 font-normal">(up to 5 images)</span> <span class="text-red-500">*</span></label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-purple-400 transition cursor-pointer" onclick="document.getElementById('product_images').click()">
                                        <svg class="w-12 h-12 mx-auto mb-4 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                        </svg>
                                        <p class="text-gray-600 font-medium mb-1">Upload Campaign Images</p>
                                        <p class="text-sm text-gray-500">Select up to 5 images (all formats)</p>
                                        <input type="file" name="product_images[]" id="product_images" class="hidden" accept="image/*" multiple required onchange="previewMultipleFiles(event, 'product-images-preview', 5)">
                                    </div>
                                    <div id="product-images-preview" class="mt-4 grid grid-cols-2 md:grid-cols-5 gap-4"></div>
                                </div>

                                <!-- Image Description (Optional) -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Image Description <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <textarea
                                        name="image_description"
                                        id="image_description"
                                        rows="3"
                                        placeholder="Describe what these images represent for your campaign..."
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition resize-none"></textarea>
                                </div>

                                <!-- Brand Slogan -->
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-2">Brand Slogan <span class="text-gray-400 font-normal">(Optional)</span></label>
                                    <input
                                        type="text"
                                        name="slogan"
                                        id="slogan"
                                        placeholder="e.g., Taste the Future!"
                                        class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition">
                                </div>

                                {{-- COMMENTED OUT - Brand Logo upload (moved here from step 1 in original, kept as upload option if re-needed)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Brand Logo <span class="text-red-500">*</span></label>
                                    <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center..." onclick="document.getElementById('brand_logo').click()">
                                        <input type="file" name="brand_logo" id="brand_logo" class="hidden" accept="image/*" onchange="previewSingleFile(event, 'brand_logo', 'logo-preview')">
                                    </div>
                                    <div id="logo-preview" class="mt-4"></div>
                                </div>
                                --}}

                                {{-- COMMENTED OUT - Campaign Creative Image with preview panel (from original step 2)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Campaign Creative Image</label>
                                    <div class="grid md:grid-cols-2 gap-6">
                                        <div>
                                            <div class="border-2 border-dashed..." onclick="document.getElementById('campaign_image').click()">
                                                <div id="campaign-image-container" class="flex items-center justify-center h-64 bg-gray-50 rounded-lg">
                                                    <img id="campaign-image-preview" src="" class="hidden w-full h-full object-cover rounded-lg">
                                                    <div id="campaign-image-placeholder">...</div>
                                                </div>
                                                <input type="file" name="campaign_image" id="campaign_image" class="hidden" accept="image/*" onchange="previewCampaignImage(event)">
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                --}}

                                {{-- COMMENTED OUT - Additional Creative Assets upload (from original step 2)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Additional Creative Assets (Optional)</label>
                                    <div class="border-2 border-dashed..." onclick="document.getElementById('creative_assets').click()">
                                        <input type="file" name="creative_assets[]" id="creative_assets" class="hidden" multiple onchange="previewOtherFiles(event, 'creative-assets-preview')">
                                    </div>
                                    <div id="creative-assets-preview" class="mt-4"></div>
                                </div>
                                --}}

                                {{-- COMMENTED OUT - Other Files upload (from original step 1)
                                <div>
                                    <label class="block text-sm font-semibold text-gray-700 mb-3">Other Files (any format - PDFs, videos, etc.)</label>
                                    <div class="border-2 border-dashed..." onclick="document.getElementById('other_files').click()">
                                        <input type="file" name="other_files[]" id="other_files" class="hidden" multiple onchange="previewOtherFiles(event, 'other-files-preview')">
                                    </div>
                                    <div id="other-files-preview" class="mt-4"></div>
                                </div>
                                --}}
                            </div>
                        </div>

                        <!-- Review Summary -->
                        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6">Review Your Campaign</h2>

                            <div class="space-y-6">
                                <!-- Brand Info -->
                                <div class="border-b border-gray-200 pb-6">
                                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                                        </svg>
                                        Brand Account
                                    </h3>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 mb-1">Brand Name</p>
                                            <p class="font-semibold text-gray-800" id="review-brand-name">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Contact Email</p>
                                            <p class="font-semibold text-gray-800" id="review-contact-email">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Website</p>
                                            <p class="font-semibold text-gray-800" id="review-website">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Tags</p>
                                            <p class="font-semibold text-gray-800" id="review-tags">-</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Campaign Details -->
                                <div class="border-b border-gray-200 pb-6">
                                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Campaign Details
                                    </h3>
                                    <div class="grid md:grid-cols-2 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 mb-1">Campaign Title</p>
                                            <p class="font-semibold text-gray-800" id="review-campaign-title">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Product Category</p>
                                            <p class="font-semibold text-gray-800" id="review-product-category">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Campaign Duration</p>
                                            <p class="font-semibold text-gray-800" id="review-duration">-</p>
                                        </div>
                                    </div>
                                </div>

                                <!-- Target & Budget -->
                                <div>
                                    <h3 class="text-lg font-bold text-gray-800 mb-4 flex items-center gap-2">
                                        <svg class="w-5 h-5 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                        </svg>
                                        Target & Budget
                                    </h3>
                                    <div class="grid md:grid-cols-4 gap-4 text-sm">
                                        <div>
                                            <p class="text-gray-500 mb-1">Campaign Goal</p>
                                            <p class="font-semibold text-gray-800" id="review-goal">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Reward Type</p>
                                            <p class="font-semibold text-gray-800" id="review-prize-type">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Region</p>
                                            <p class="font-semibold text-gray-800" id="review-audience">-</p>
                                        </div>
                                        <div>
                                            <p class="text-gray-500 mb-1">Total Budget</p>
                                            <p class="font-semibold text-gray-800" id="review-prize-amount">-</p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="mt-6 bg-yellow-50 border border-yellow-200 rounded-lg p-4">
                                <p class="text-sm text-yellow-800">
                                    <strong>Note:</strong> Please review all information carefully before submitting. You can go back to edit any section.
                                </p>
                            </div>
                        </div>

                        <!-- Terms Acceptance -->
                        <div class="bg-white rounded-2xl p-8 shadow-sm border border-gray-200">
                            <label class="flex items-start gap-3 cursor-pointer">
                                <input type="checkbox" name="terms_accepted" id="terms_accepted" value="1" class="mt-1 w-5 h-5 rounded border-gray-300 text-purple-600 focus:ring-purple-500" required>
                                <span class="text-sm text-gray-700 leading-relaxed">
                                    I certify that all information provided is accurate and that I have the rights to all uploaded content. I agree to the
                                    <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold underline">Terms of Service</a> and
                                    <a href="#" class="text-purple-600 hover:text-purple-700 font-semibold underline">Content Guidelines</a>.
                                </span>
                            </label>
                        </div>
                    </div>

                    <!-- Payment Summary Sidebar -->
                    <div class="lg:col-span-1">
                        <div class="bg-white rounded-2xl p-6 shadow-lg border border-gray-200 sticky top-6">
                            <h3 class="text-lg font-bold text-gray-800 mb-4">Payment Summary</h3>

                            <div class="space-y-4 mb-6">
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Prize Budget</span>
                                    <span class="font-semibold text-gray-800" id="review-prize-pool">$0</span>
                                </div>
                                <div class="flex justify-between text-sm">
                                    <span class="text-gray-600">Platform Fee</span>
                                    <span class="font-semibold text-gray-800" id="review-platform-fee">$100</span>
                                </div>
                                <div class="h-px bg-gray-200"></div>
                                <div class="flex justify-between items-center">
                                    <span class="font-bold text-gray-800">Total</span>
                                    <span class="text-3xl font-black text-purple-600" id="review-total">$0</span>
                                </div>
                            </div>

                            <button
                                type="button"
                                onclick="openPaymentModal()"
                                id="final-submit-btn"
                                class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg flex items-center justify-center gap-2 mb-3">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"></path>
                                </svg>
                                Pay & Launch Campaign
                            </button>

                            <p class="text-xs text-gray-500 text-center leading-relaxed">
                                Secure payment powered by <span class="font-bold">Stripe</span>
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

            <!-- Stripe-like Payment Modal -->
            <div id="payment-modal" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50 hidden">
                <div class="bg-white dark:bg-gray-800 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
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
                        <!-- Order Summary -->
                        <div class="mb-6 p-4 bg-gray-50 dark:bg-gray-700 rounded-xl">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Prize Pool</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200" id="modal-prize-pool">$0</span>
                            </div>
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-gray-600 dark:text-gray-300">Platform Fee</span>
                                <span class="font-semibold text-gray-800 dark:text-gray-200">$100</span>
                            </div>
                            <div class="h-px bg-gray-300 dark:bg-gray-600 my-2"></div>
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-800 dark:text-gray-200">Total</span>
                                <span class="text-2xl font-black text-purple-600" id="modal-total">$0</span>
                            </div>
                        </div>

                        <!-- Payment Form -->
                        <form id="payment-form" onsubmit="processPayment(event)">
                            <!-- Card Number -->
                            <div class="mb-4">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Card Number</label>
                                <div class="relative">
                                    <input
                                        type="text"
                                        id="card-number"
                                        placeholder="4242 4242 4242 4242"
                                        maxlength="19"
                                        class="w-full pl-12 pr-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition font-mono"
                                        required>
                                    <svg class="w-6 h-6 absolute left-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                                    </svg>
                                    <div class="absolute right-3 top-1/2 -translate-y-1/2 flex gap-1">
                                        <svg class="w-8 h-5" viewBox="0 0 48 32"><rect width="48" height="32" rx="4" fill="#1434CB"/></svg>
                                    </div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">Test card: 4242 4242 4242 4242</p>
                            </div>

                            <!-- Expiry & CVC -->
                            <div class="grid grid-cols-2 gap-4 mb-4">
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Expiry Date</label>
                                    <input
                                        type="text"
                                        id="card-expiry"
                                        placeholder="MM/YY"
                                        maxlength="5"
                                        class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition font-mono"
                                        required>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">CVC</label>
                                    <div class="relative">
                                        <input
                                            type="text"
                                            id="card-cvc"
                                            placeholder="123"
                                            maxlength="4"
                                            class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition font-mono"
                                            required>
                                        <svg class="w-4 h-4 absolute right-3 top-1/2 -translate-y-1/2 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                        </svg>
                                    </div>
                                </div>
                            </div>

                            <!-- Cardholder Name -->
                            <div class="mb-6">
                                <label class="block text-xs font-semibold text-gray-700 dark:text-gray-300 uppercase tracking-wide mb-2">Cardholder Name</label>
                                <input
                                    type="text"
                                    id="card-name"
                                    placeholder="John Doe"
                                    class="w-full px-4 py-3 border border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white rounded-lg focus:ring-2 focus:ring-purple-500 focus:border-transparent outline-none transition"
                                    required>
                            </div>

                            <!-- Pay Button -->
                            <button
                                type="submit"
                                id="pay-button"
                                class="w-full px-6 py-4 bg-gradient-to-r from-purple-600 to-blue-600 text-white rounded-xl font-bold hover:from-purple-700 hover:to-blue-700 transition-all transform hover:scale-105 active:scale-95 shadow-lg flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/>
                                </svg>
                                <span id="pay-button-text">Pay Now</span>
                                <svg id="pay-button-spinner" class="animate-spin h-5 w-5 hidden" viewBox="0 0 24 24">
                                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
                                    <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                                </svg>
                            </button>
                        </form>

                        <!-- Security Badges -->
                        <div class="mt-6 pt-4 border-t border-gray-200 dark:border-gray-700">
                            <div class="flex items-center justify-center gap-4">
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M12 1L3 5v6c0 5.55 3.84 10.74 9 12 5.16-1.26 9-6.45 9-12V5l-9-4zm0 10.99h7c-.53 4.12-3.28 7.79-7 8.94V12H5V6.3l7-3.11v8.8z"/>
                                    </svg>
                                    <span>Secure</span>
                                </div>
                                <div class="flex items-center gap-1 text-xs text-gray-500 dark:text-gray-400">
                                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M18 8h-1V6c0-2.76-2.24-5-5-5S7 3.24 7 6v2H6c-1.1 0-2 .9-2 2v10c0 1.1.9 2 2 2h12c1.1 0 2-.9 2-2V10c0-1.1-.9-2-2-2zm-6 9c-1.1 0-2-.9-2-2s.9-2 2-2 2 .9 2 2-.9 2-2 2zm3.1-9H8.9V6c0-1.71 1.39-3.1 3.1-3.1 1.71 0 3.1 1.39 3.1 3.1v2z"/>
                                    </svg>
                                    <span>Encrypted</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Navigation Buttons -->
            <div class="mt-8 flex items-center justify-between">
                <button
                    type="button"
                    id="prev-btn"
                    class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 hover:border-gray-400 transition flex items-center gap-2 hidden">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Previous
                </button>

                <button
                    type="button"
                    id="save-draft-btn"
                    onclick="saveDraft()"
                    class="px-8 py-3 bg-white border-2 border-gray-300 text-gray-700 rounded-lg font-semibold hover:bg-gray-50 hover:border-gray-400 transition flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path>
                    </svg>
                    Save as Draft
                </button>

                <button
                    type="button"
                    id="next-btn"
                    class="px-12 py-3 bg-purple-600 text-white rounded-lg font-bold hover:bg-purple-700 transition flex items-center gap-2 shadow-lg">
                    Next Step
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </form>
    </div>
</div>

<script>
let currentStep = 1;
const totalSteps = 4;

document.addEventListener('DOMContentLoaded', function() {
    updateStepDisplay();

    document.getElementById('next-btn').addEventListener('click', function() {
        if (validateCurrentStep()) {
            if (currentStep < totalSteps) {
                currentStep++;
                updateStepDisplay();
                window.scrollTo({ top: 0, behavior: 'smooth' });
            }
        }
    });

    document.getElementById('prev-btn').addEventListener('click', function() {
        if (currentStep > 1) {
            currentStep--;
            updateStepDisplay();
            window.scrollTo({ top: 0, behavior: 'smooth' });
        }
    });
});

/**
 * Toggle "Other" text field visibility when "Other" option is selected
 * @param {HTMLElement} selectElement - The select dropdown element
 * @param {string} otherFieldId - The ID of the "Other" text input field
 */
function toggleOtherField(selectElement, otherFieldId) {
    const otherField = document.getElementById(otherFieldId);
    if (selectElement.value === 'Other') {
        otherField.classList.remove('hidden');
        otherField.required = true;
        otherField.focus();
    } else {
        otherField.classList.add('hidden');
        otherField.required = false;
        otherField.value = '';
    }
}

/**
 * Update minimum end date when start date changes
 */
function updateMinEndDate() {
    const startDate = document.getElementById('start_date').value;
    const endDateInput = document.getElementById('end_date');

    if (startDate) {
        // Calculate end date minimum (start date + 1 day)
        const startDateObj = new Date(startDate);
        startDateObj.setDate(startDateObj.getDate() + 1);
        const minEndDate = startDateObj.toISOString().split('T')[0];

        endDateInput.setAttribute('min', minEndDate);

        // If current end date is before new minimum, update it
        if (endDateInput.value && endDateInput.value < minEndDate) {
            endDateInput.value = minEndDate;
        }
    }
}

/**
 * Validate dates in real-time using AJAX
 */
async function validateDates() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    // Only validate if start date is selected
    if (!startDate) {
        return;
    }

    // Check if end date is before start date
    if (endDate && new Date(startDate) >= new Date(endDate)) {
        alert('End date must be after start date');
        document.getElementById('end_date').value = '';
        return;
    }

    try {
        const response = await fetch('/brands/check-dates', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                start_date: startDate,
                end_date: endDate || startDate // If no end date, check just start date
            })
        });

        const data = await response.json();

        if (data.overlapping) {
            // Show conflicting campaign info if available
            const conflictInfo = document.getElementById('conflicting-campaign-info');
            const conflictCompany = document.getElementById('conflict-company');
            const conflictDates = document.getElementById('conflict-dates');
            
            if (data.campaign) {
                conflictCompany.textContent = data.campaign.company_name;
                const startDate = new Date(data.campaign.start_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                const endDate = new Date(data.campaign.end_date).toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric' });
                conflictDates.textContent = `${startDate} - ${endDate}`;
                conflictInfo.classList.remove('hidden');
            } else {
                conflictInfo.classList.add('hidden');
            }
            
            // Show popup by removing 'hidden' class
            document.getElementById('date-validation-popup').classList.remove('hidden');
            // Clear the dates
            document.getElementById('start_date').value = '';
            document.getElementById('end_date').value = '';
        }
    } catch (error) {
        console.error('Date validation error:', error);
    }
}

/**
 * Validate that end date is after start date
 */
function validateEndDate() {
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;

    if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
        alert('End date must be after start date');
        document.getElementById('end_date').value = '';
    }
}

/**
 * Validate budget input - must be at least $100
 */
function validateBudget(input) {
    const value = parseFloat(input.value);
    if (value && value < 100) {
        input.value = 100;
        alert('Minimum budget is $100');
    }
}

function updateStepDisplay() {
    document.querySelectorAll('.step-content').forEach(step => step.classList.add('hidden'));
    document.getElementById(`step-${currentStep}`).classList.remove('hidden');

    for (let i = 1; i <= totalSteps; i++) {
        const indicator = document.getElementById(`step-indicator-${i}`);
        const line = document.getElementById(`line-${i}`);

        if (i < currentStep) {
            indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-purple-600');
            indicator.classList.add('bg-green-500', 'text-white');
            indicator.innerHTML = '✓';
            if (line) line.classList.add('bg-green-500');
        } else if (i === currentStep) {
            indicator.classList.remove('bg-gray-300', 'text-gray-600', 'bg-green-500');
            indicator.classList.add('bg-purple-600', 'text-white');
            indicator.textContent = i;
            if (line) line.classList.remove('bg-green-500');
        } else {
            indicator.classList.remove('bg-purple-600', 'bg-green-500', 'text-white');
            indicator.classList.add('bg-gray-300', 'text-gray-600');
            indicator.textContent = i;
            if (line) line.classList.remove('bg-green-500');
        }
    }

    const prevBtn = document.getElementById('prev-btn');
    const nextBtn = document.getElementById('next-btn');

    if (currentStep === 1) {
        prevBtn.classList.add('hidden');
    } else {
        prevBtn.classList.remove('hidden');
    }

    if (currentStep === totalSteps) {
        nextBtn.classList.add('hidden');
        updateReviewStep();
    } else {
        nextBtn.classList.remove('hidden');
    }

    for (let i = 1; i <= totalSteps; i++) {
        const stepText = document.querySelector(`#step-indicator-${i}`).nextElementSibling;
        if (i <= currentStep) {
            stepText.classList.remove('text-gray-600');
            stepText.classList.add('text-gray-700', 'font-semibold');
        } else {
            stepText.classList.remove('text-gray-700', 'font-semibold');
            stepText.classList.add('text-gray-600');
        }
    }
}

function validateCurrentStep() {
    let isValid = true;
    let errorMessage = '';

    if (currentStep === 1) {
        // Step 1: Brand Account - Brand Name + Email required
        const requiredFields = [
            { id: 'company_name', name: 'Brand Name' },
            { id: 'contact_email', name: 'Contact Email' },
        ];

        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) {
                isValid = false;
                errorMessage += `${field.name} is required\n`;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // Validate email format
        const emailInput = document.getElementById('contact_email');
        if (emailInput.value && !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(emailInput.value)) {
            isValid = false;
            errorMessage += 'Please enter a valid email address\n';
            emailInput.classList.add('border-red-500');
        }

    } else if (currentStep === 2) {
        // Step 2: Campaign Details - Campaign Title + Product Description + Dates required
        const requiredFields = [
            { id: 'campaign_title', name: 'Campaign Title' },
            { id: 'product_content', name: 'Product Description' },
            { id: 'start_date', name: 'Start Date' },
            { id: 'end_date', name: 'End Date' },
        ];

        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) {
                isValid = false;
                errorMessage += `${field.name} is required\n`;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // Validate end date is after start date
        const startDate = document.getElementById('start_date').value;
        const endDate = document.getElementById('end_date').value;
        if (startDate && endDate && new Date(startDate) >= new Date(endDate)) {
            isValid = false;
            errorMessage += 'End date must be after start date\n';
        }

    } else if (currentStep === 3) {
        // Step 3: Target & Budget - Goal, Reward Type, Region, Budget required
        const requiredFields = [
            { id: 'campaign_goal', name: 'Campaign Goal' },
            { id: 'prize_type', name: 'Reward Type' },
            { id: 'audience_location', name: 'Audience Region' },
            { id: 'prize_amount', name: 'Total Budget' },
        ];

        requiredFields.forEach(field => {
            const input = document.getElementById(field.id);
            if (!input.value.trim()) {
                isValid = false;
                errorMessage += `${field.name} is required\n`;
                input.classList.add('border-red-500');
            } else {
                input.classList.remove('border-red-500');
            }
        });

        // Validate "Other" fields when "Other" option is selected
        const goalOtherInput = document.getElementById('campaign_goal_other');
        const prizeTypeOtherInput = document.getElementById('prize_type_other');
        const audienceOtherInput = document.getElementById('audience_location_other');

        if (document.getElementById('campaign_goal').value === 'Other' && !goalOtherInput.value.trim()) {
            isValid = false;
            errorMessage += 'Please specify your campaign goal\n';
            goalOtherInput.classList.add('border-red-500');
        }

        if (document.getElementById('prize_type').value === 'Other' && !prizeTypeOtherInput.value.trim()) {
            isValid = false;
            errorMessage += 'Please specify your reward type\n';
            prizeTypeOtherInput.classList.add('border-red-500');
        }

        if (document.getElementById('audience_location').value === 'Other' && !audienceOtherInput.value.trim()) {
            isValid = false;
            errorMessage += 'Please specify your target region\n';
            audienceOtherInput.classList.add('border-red-500');
        }

        // Validate minimum budget
        const budget = parseFloat(document.getElementById('prize_amount').value);
        if (budget && budget < 100) {
            isValid = false;
            errorMessage += 'Minimum budget is $100\n';
            document.getElementById('prize_amount').classList.add('border-red-500');
        }
    }

    if (!isValid) alert(errorMessage);
    return isValid;
}

function updateBudgetBreakdown() {
    const prizeBudget = parseFloat(document.getElementById('prize_amount').value) || 0;
    const platformFee = 100;
    const total = prizeBudget + platformFee;

    document.getElementById('budget-prize-pool').textContent = `$${prizeBudget}`;
    document.getElementById('budget-total').textContent = `$${total}`;
}

function updateReviewStep() {
    // Brand Account
    document.getElementById('review-brand-name').textContent = document.getElementById('company_name').value || '-';
    document.getElementById('review-contact-email').textContent = document.getElementById('contact_email').value || '-';
    document.getElementById('review-website').textContent = document.getElementById('website').value || '-';
    document.getElementById('review-tags').textContent = document.getElementById('tags').value || '-';

    // Campaign Details
    document.getElementById('review-campaign-title').textContent = document.getElementById('campaign_title').value || '-';
    const productCategory = document.getElementById('product_category').value;
    const productCategoryOther = document.getElementById('product_category_other').value;
    document.getElementById('review-product-category').textContent = (productCategory === 'Other' && productCategoryOther.trim()) ? productCategoryOther : (productCategory || '-');
    const startDate = document.getElementById('start_date').value;
    const endDate = document.getElementById('end_date').value;
    document.getElementById('review-duration').textContent = (startDate && endDate) ? `${startDate} to ${endDate}` : '-';

    // Target & Budget - Use "Other" value if "Other" option is selected
    const campaignGoal = document.getElementById('campaign_goal').value;
    const campaignGoalOther = document.getElementById('campaign_goal_other').value;
    document.getElementById('review-goal').textContent = (campaignGoal === 'Other' && campaignGoalOther.trim()) ? campaignGoalOther : (campaignGoal || '-');

    const prizeType = document.getElementById('prize_type').value;
    const prizeTypeOther = document.getElementById('prize_type_other').value;
    document.getElementById('review-prize-type').textContent = (prizeType === 'Other' && prizeTypeOther.trim()) ? prizeTypeOther : (prizeType || '-');

    const audienceLocation = document.getElementById('audience_location').value;
    const audienceLocationOther = document.getElementById('audience_location_other').value;
    document.getElementById('review-audience').textContent = (audienceLocation === 'Other' && audienceLocationOther.trim()) ? audienceLocationOther : (audienceLocation || '-');

    const prizeBudget = parseFloat(document.getElementById('prize_amount').value) || 0;
    const platformFee = 100;
    const total = prizeBudget + platformFee;

    document.getElementById('review-prize-amount').textContent = `$${prizeBudget}`;
    document.getElementById('review-prize-pool').textContent = `$${prizeBudget}`;
    document.getElementById('review-platform-fee').textContent = `$100`;
    document.getElementById('review-total').textContent = `$${total}`;

    // COMMENTED OUT - prize type and winner selection review fields (can re-enable if those fields are restored)
    // document.getElementById('review-prize-type').textContent = document.getElementById('prize_type').value || '-';
    // document.getElementById('review-winner-selection').textContent = document.getElementById('winner_selection').value || '-';
    // document.getElementById('review-phone').textContent = document.getElementById('phone').value || '-';
    // document.getElementById('review-duration').textContent = startDate && endDate ? `${startDate} to ${endDate}` : '-';
}

function previewSingleFile(event, inputId, previewId) {
    const input = event.target;
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    if (input.files && input.files[0]) {
        const file = input.files[0];
        if (file.type.startsWith('image/')) {
            const reader = new FileReader();
            reader.onload = function(e) {
                preview.innerHTML = `
                    <div class="relative inline-block">
                        <img src="${e.target.result}" class="h-24 w-24 object-cover rounded-lg border-2 border-purple-200">
                        <button type="button" onclick="removeFile('${inputId}')" class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full w-6 h-6 flex items-center justify-center hover:bg-red-600">×</button>
                    </div>
                `;
            }
            reader.readAsDataURL(file);
        }
    }
}

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

function previewOtherFiles(event, previewId) {
    const input = event.target;
    const preview = document.getElementById(previewId);
    preview.innerHTML = '';

    Array.from(input.files).forEach((file, index) => {
        const div = document.createElement('div');
        div.className = 'flex items-center gap-3 bg-gray-50 p-3 rounded-lg border border-gray-200';
        div.innerHTML = `
            <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
            </svg>
            <div class="flex-1">
                <p class="font-medium text-gray-800 text-sm">${file.name}</p>
                <p class="text-xs text-gray-500">${(file.size / 1024).toFixed(2)} KB</p>
            </div>
            <button type="button" onclick="removeMultipleFile(event, '${input.id}', ${index})" class="text-red-500 hover:text-red-600">
                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        `;
        preview.appendChild(div);
    });
}

function previewCampaignImage(event) {
    const input = event.target;
    const preview = document.getElementById('campaign-image-preview');
    const placeholder = document.getElementById('campaign-image-placeholder');

    if (input.files && input.files[0]) {
        const reader = new FileReader();
        reader.onload = function(e) {
            preview.src = e.target.result;
            preview.classList.remove('hidden');
            placeholder.classList.add('hidden');
        }
        reader.readAsDataURL(input.files[0]);
    }
}

function removeFile(inputId) {
    document.getElementById(inputId).value = '';
    event.target.closest('div').remove();
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

function saveDraft() {
    const formData = new FormData(document.getElementById('campaign-form'));

    // Show loading state on button
    const saveDraftBtn = document.getElementById('save-draft-btn');
    const originalText = saveDraftBtn.innerHTML;
    saveDraftBtn.disabled = true;
    saveDraftBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Saving...
    `;

    fetch('{{ route("drafts.save") }}', {
        method: 'POST',
        body: formData,
        headers: {
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show success toast and redirect to home
            showDraftSavedToast();
            setTimeout(() => {
                window.location.href = '{{ route("home") }}';
            }, 1500);
        } else {
            alert('Failed to save draft: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(error => {
        console.error('Error saving draft:', error);
        alert('Failed to save draft. Please try again.');
    })
    .finally(() => {
        saveDraftBtn.disabled = false;
        saveDraftBtn.innerHTML = originalText;
    });
}

function showDraftSavedToast() {
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-green-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 z-50 animate-bounce';
    toast.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-medium">Draft saved successfully!</span>
    `;
    document.body.appendChild(toast);
    
    setTimeout(() => {
        toast.remove();
    }, 3000);
}

document.getElementById('campaign-form').addEventListener('submit', function(e) {
    e.preventDefault();

    if (!document.getElementById('terms_accepted').checked) {
        alert('Please accept the terms and conditions to continue');
        return;
    }

    const submitBtn = document.getElementById('final-submit-btn');
    submitBtn.disabled = true;
    submitBtn.innerHTML = `
        <svg class="animate-spin h-5 w-5 mr-2" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4" fill="none"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
        Processing Payment...
    `;
    this.submit();
});

// Payment Modal Functions
function openPaymentModal() {
    // Validate form first
    if (!validateCurrentStep()) {
        alert('Please fill in all required fields');
        return;
    }
    
    // Update modal with total amounts
    const prizeBudget = parseFloat(document.getElementById('prize_amount').value) || 0;
    const platformFee = 100;
    const total = prizeBudget + platformFee;
    
    document.getElementById('modal-prize-pool').textContent = `$${prizeBudget}`;
    document.getElementById('modal-total').textContent = `$${total}`;
    
    // Show modal
    document.getElementById('payment-modal').classList.remove('hidden');
}

function closePaymentModal() {
    document.getElementById('payment-modal').classList.add('hidden');
    // Reset payment form
    document.getElementById('payment-form').reset();
    document.getElementById('pay-button-text').textContent = 'Pay Now';
    document.getElementById('pay-button-spinner').classList.add('hidden');
    document.getElementById('pay-button').disabled = false;
}

function processPayment(event) {
    event.preventDefault();
    
    const payButton = document.getElementById('pay-button');
    const payButtonText = document.getElementById('pay-button-text');
    const payButtonSpinner = document.getElementById('pay-button-spinner');
    
    // Show loading state
    payButton.disabled = true;
    payButtonText.textContent = 'Processing...';
    payButtonSpinner.classList.remove('hidden');
    
    // Simulate payment processing (2 seconds)
    setTimeout(() => {
        // Payment successful
        payButtonText.textContent = 'Payment Successful!';
        payButtonSpinner.classList.add('hidden');
        
        // Show success message
        alert('Payment successful! Your campaign is being launched.');
        
        // Close modal and submit form
        closePaymentModal();
        document.getElementById('campaign-form').submit();
    }, 2000);
}

// Format card number with spaces
document.getElementById('card-number')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\s/g, '');
    let formattedValue = '';
    for (let i = 0; i < value.length && i < 16; i++) {
        if (i > 0 && i % 4 === 0) formattedValue += ' ';
        formattedValue += value[i];
    }
    e.target.value = formattedValue;
});

// Format expiry date
document.getElementById('card-expiry')?.addEventListener('input', function(e) {
    let value = e.target.value.replace(/\D/g, '');
    if (value.length >= 2) {
        value = value.substring(0, 2) + '/' + value.substring(2, 4);
    }
    e.target.value = value;
});

// Only allow numbers for CVC
document.getElementById('card-cvc')?.addEventListener('input', function(e) {
    e.target.value = e.target.value.replace(/\D/g, '');
});

// Dark mode toggle function
function toggleDarkMode() {
    const html = document.documentElement;
    const isDark = !html.classList.contains('dark');
    
    if (isDark) {
        html.classList.add('dark');
    } else {
        html.classList.remove('dark');
    }
    
    localStorage.setItem('darkMode', isDark);
    updateDarkModeIcon(isDark);
}

function updateDarkModeIcon(isDark) {
    const icon = document.getElementById('dark-mode-icon');
    if (!icon) return;

    if (isDark) {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v1m0 16v1m9-9h-1M4 12H3m15.364 6.364l-.707-.707M6.343 6.343l-.707-.707m12.728 0l-.707.707M6.343 17.657l-.707.707M16 12a4 4 0 11-8 0 4 4 0 018 0z"></path>';
    } else {
        icon.innerHTML = '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20.354 15.354A9 9 0 018.646 3.646 9.003 9.003 0 0012 21a9.003 9.003 0 008.354-5.646z"></path>';
    }
}

// Load draft data if available
@if($draft)
document.addEventListener('DOMContentLoaded', function() {
    const draft = @json($draft);
    
    // Step 1: Brand Account
    document.getElementById('company_name').value = draft.company_name || '';
    document.getElementById('contact_email').value = draft.contact_email || '';
    document.getElementById('website').value = draft.website || '';
    document.getElementById('tags').value = draft.tags || '';
    
    // Step 2: Campaign Details
    document.getElementById('campaign_title').value = draft.campaign_title || '';
    document.getElementById('product_category').value = draft.product_category || '';
    if (draft.product_category_other) {
        document.getElementById('product_category_other').value = draft.product_category_other;
        document.getElementById('product_category_other').classList.remove('hidden');
    }
    document.getElementById('product_content').value = draft.product_content || '';
    if (draft.start_date) {
        document.getElementById('start_date').value = draft.start_date.split('T')[0];
    }
    if (draft.end_date) {
        document.getElementById('end_date').value = draft.end_date.split('T')[0];
    }
    document.getElementById('guidelines').value = draft.guidelines || '';
    
    // Step 3: Target & Budget
    document.getElementById('campaign_goal').value = draft.campaign_goal || '';
    if (draft.campaign_goal_other) {
        document.getElementById('campaign_goal_other').value = draft.campaign_goal_other;
        document.getElementById('campaign_goal_other').classList.remove('hidden');
    }
    document.getElementById('prize_type').value = draft.prize_type || '';
    if (draft.prize_type_other) {
        document.getElementById('prize_type_other').value = draft.prize_type_other;
        document.getElementById('prize_type_other').classList.remove('hidden');
    }
    document.getElementById('prize_amount').value = draft.prize_amount || '';
    document.getElementById('audience_location').value = draft.audience_location || '';
    if (draft.audience_location_other) {
        document.getElementById('audience_location_other').value = draft.audience_location_other;
        document.getElementById('audience_location_other').classList.remove('hidden');
    }
    document.getElementById('audience_size').value = draft.audience_size || '1000-5000';
    document.getElementById('estimated_participants').value = draft.estimated_participants || '';
    
    // Step 4: Campaign Creatives
    document.getElementById('theme_color').value = draft.theme_color || '#6f42c1';
    
    // Show success message
    const toast = document.createElement('div');
    toast.className = 'fixed bottom-4 right-4 bg-purple-500 text-white px-6 py-3 rounded-lg shadow-lg flex items-center gap-3 z-50';
    toast.innerHTML = `
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
        </svg>
        <span class="font-medium">Draft loaded successfully!</span>
    `;
    document.body.appendChild(toast);
    setTimeout(() => toast.remove(), 3000);
});
@endif
</script>

<style>
.text-purple-600 { color: #8b5cf6; }
.bg-purple-600 { background-color: #8b5cf6; }
.hover\:bg-purple-700:hover { background-color: #7c3aed; }
.focus\:ring-purple-500:focus { --tw-ring-color: #8b5cf6; }
.from-purple-600 { --tw-gradient-from: #8b5cf6; }
.to-blue-600 { --tw-gradient-to: #2563eb; }

@keyframes spin { to { transform: rotate(360deg); } }
.animate-spin { animation: spin 1s linear infinite; }

.step-content { animation: fadeIn 0.3s ease-in; }
@keyframes fadeIn {
    from { opacity: 0; transform: translateY(10px); }
    to { opacity: 1; transform: translateY(0); }
}
</style>