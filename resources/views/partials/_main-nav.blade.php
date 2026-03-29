<style>
    :root {
        --brand-purple: #5B2E91;
        --brand-orange: #f2994a;
        --brand-bg: #f3f4f6;
    }
    
    /* Navbar Styles from Home */
    .custom-navbar { 
        background: white; 
        border-bottom: 1px solid #e5e7eb; 
        padding: 0.8rem 0; 
        position: sticky;
        top: 0;
        z-index: 1020;
    }
    .brand-logo { 
        color: var(--brand-purple) !important; 
        font-weight: 800 !important; 
        font-size: 1.4rem !important; 
        display: flex; 
        align-items: center; 
        gap: 8px; 
        text-decoration: none !important; 
    }
    .nav-menu-link { 
        text-transform: uppercase; 
        font-weight: 700; 
        font-size: 0.8rem; 
        color: #6b7280; 
        letter-spacing: 0.5px; 
        text-decoration: none; 
        padding: 0 10px; 
        transition: color 0.2s; 
    }
    .nav-menu-link:hover { 
        color: var(--brand-purple); 
    }
    .nav-divider { 
        color: #e5e7eb; 
    }

    /* Dropdown overrides for profile dropdown */
    .custom-navbar .dropdown-menu {
        border: none;
        box-shadow: 0 10px 25px rgba(0,0,0,0.1);
        border-radius: 12px;
        padding: 0.5rem;
        margin-top: 10px;
    }
    .custom-navbar .dropdown-item {
        border-radius: 8px;
        padding: 0.6rem 1rem;
        font-weight: 600;
        font-size: 0.9rem;
        color: #4b5563;
        transition: all 0.2s;
    }
    .custom-navbar .dropdown-item:hover {
        background-color: #f3f4f6;
        color: var(--brand-purple);
    }
</style>

<nav class="custom-navbar">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <!-- Logo -->
        <a class="brand-logo" href="{{ route('home') }}">
            <img src="{{ asset('image/my-logo.jpg') }}" width="35" height="35" class="rounded-circle shadow-sm" alt="Logo">
            OneDollarMeme
        </a>
        
        <!-- Center Links -->
        @unless(request()->routeIs('account.settings'))
        <div class="d-none d-lg-flex align-items-center">
            <a href="{{ route('memes.index', ['sort' => 'trending']) }}" class="nav-menu-link">TRENDING</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('brands.public') }}" class="nav-menu-link">BRANDS</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('upload-meme.create') }}" class="nav-menu-link">WEEKLY BATTLE</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('blogs') }}" class="nav-menu-link">BLOG</a>
        </div>
        @endunless
        
        <!-- Right Actions -->
        <div class="d-flex align-items-center gap-3">
             @unless(request()->routeIs('account.settings'))
             <form action="{{ route('memes.search') }}" method="GET" class="input-group input-group-sm d-none d-md-flex" style="width: 180px;">
                <input type="text" name="q" class="form-control" placeholder="SEARCH" style="border-right: none; background: #f9fafb;">
                <button type="submit" class="input-group-text bg-light text-muted" style="border-left: none; background: #f9fafb; cursor: pointer;"><i class="bi bi-search"></i></button>
            </form>
            <a href="{{ route('upload-meme.create') }}" class="btn btn-sm fw-bold text-white px-3" style="background-color: var(--brand-purple);">UPLOAD</a>
            @endunless
            
             <!-- User Profile / Auth -->
            @auth
                 <x-profile-dropdown :user="auth()->user()" />
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-person"></i></a>
            @endauth
        </div>
    </div>
</nav>
