<!-- Header -->
<header class="bg-white border-b border-light fixed top-0 left-0 right-0 z-50 transition-colors duration-300">
    <div class="max-w-[1440px] mx-auto px-4 h-16 flex items-center justify-between relative">
        <!-- Logo -->
        <a href="{{ route('home') }}" class="flex items-center gap-2">
            <div class="w-10 h-10 bg-purple rounded-full flex items-center justify-center text-white font-light text-xl italic">
                <img src="{{ asset('image/my-logo.jpg') }}" class="w-8 h-8 object-contain rounded-full">
            </div>
            <span style="color: #5B2E91; font-weight: 800; font-size: 1.4rem; text-decoration: none;">OneDollarMeme</span>
        </a>
        
        <!-- Search -->
        <div class="hidden lg:flex absolute left-1/2 top-1/2 transform -translate-x-1/2 -translate-y-1/2 w-full max-w-md justify-center">
            <div class="relative w-full">
                <input type="text" placeholder="Search memes, battles..." class="w-full bg-gray-100 border-none rounded-full py-2 pl-10 focus:ring-2 focus:ring-purple/20">
                <svg class="w-5 h-5 absolute left-3 top-2.5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path></svg>
            </div>
        </div>

        <!-- Actions -->
        <div class="flex items-center gap-6">
        </div>
    </div>
</header>
