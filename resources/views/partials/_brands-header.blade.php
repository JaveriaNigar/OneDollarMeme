<header class="bg-white border-b border-gray-200 fixed top-0 left-0 right-0 z-50 transition-colors duration-300">
    <div class="max-w-[1440px] mx-auto px-4 h-16 flex items-center justify-between relative">
        
        <a href="{{ route('home') }}" class="flex items-center gap-2 flex-shrink-0">
            <div class="w-10 h-10 bg-purple-700 rounded-full flex items-center justify-center overflow-hidden shadow-sm">
                <img src="{{ asset('image/my-logo.jpg') }}" class="w-full h-full object-cover">
            </div>
            <span class="whitespace-nowrap" style="color: #5B2E91; font-weight: 800; font-size: 1.4rem;">OneDollarMeme</span>
        </a>
        
        <div class="hidden md:flex absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-xs lg:max-w-md">
            <div class="relative w-full">
                <input type="text" placeholder="Search memes..." class="w-full bg-gray-100 border-none rounded-full py-2 pl-10 focus:ring-2 focus:ring-purple-500/20 text-sm">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                </svg>
            </div>
        </div>

        <div class="flex items-center gap-3 lg:gap-4">
            <a href="{{ route('brands.index') }}" 
               class="hidden sm:block px-4 py-1.5 bg-[#5B2E91] text-white text-[11px] font-bold rounded shadow-sm hover:bg-purple-800 transition-all uppercase tracking-wider">
                For Brands
            </a>

            <div class="flex items-center gap-1 sm:gap-2">
                @auth
                    <div class="flex items-center">
                         <x-profile-dropdown :user="auth()->user()" />
                    </div>
                @else
                    <a href="{{ route('login') }}" class="w-9 h-9 flex items-center justify-center rounded-full border border-gray-200 text-gray-600 hover:bg-gray-50">
                        <i class="bi bi-person text-lg"></i>
                    </a>
                @endauth

                <button class="flex md:hidden items-center justify-center w-8 h-8 text-[#5B2E91] hover:bg-gray-100 rounded-full transition-colors focus:outline-none" 
                        type="button" 
                        data-bs-toggle="offcanvas" 
                        data-bs-target="#mobileSidebarOffcanvas">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 8c1.1 0 2-.9 2-2s-.9-2-2-2-2 .9-2 2 .9 2 2 2zm0 2c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2zm0 6c-1.1 0-2 .9-2 2s.9 2 2 2 2-.9 2-2-.9-2-2-2z"/>
                    </svg>
                </button>
            </div>
        </div>
        
    </div>
</header>