<style>
    :root {
        --brand-purple: #5B2E91;
        --brand-orange: #f2994a;
        --brand-bg: #f3f4f6;
    }
    
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
        flex-shrink: 0; /* Logo ko dabne se rokta hai */
    }
    .nav-menu-link { 
        text-transform: uppercase; 
        font-weight: 700; 
        font-size: 0.8rem; 
        color: #6b7280; 
        letter-spacing: 0.5px; 
        text-decoration: none; 
        padding: 5px 12px; /* Gap badhane ke liye padding */
        transition: color 0.2s; 
        white-space: nowrap; /* Text wrap nahi hoga */
    }
    .nav-menu-link:hover { 
        color: var(--brand-purple); 
    }
    .nav-divider { 
        color: #e5e7eb; 
        user-select: none;
    }

    /* Actions area (Search + Buttons + Profile) */
    .nav-actions {
        display: flex;
        align-items: center;
        gap: 1rem;
        margin-left: auto; /* Sab kuch right side push karne ke liye */
    }

    @media (max-width: 991px) {
        .brand-logo { font-size: 1.1rem !important; }
    }
</style>

<nav class="custom-navbar">
    <div class="container d-flex align-items-center">
        <a class="brand-logo" href="{{ route('home') }}">
            <img src="{{ asset('image/my-logo.jpg') }}" width="35" height="35" class="rounded-circle shadow-sm" alt="Logo">
          <span class="ms-2">OneDollarMeme</span>
        </a>
        
        @unless(request()->routeIs('account.settings'))
        <div class="d-none d-lg-flex align-items-center ms-4">
            <a href="{{ route('memes.index', ['sort' => 'trending']) }}" class="nav-menu-link">TRENDING</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('brands.public') }}" class="nav-menu-link">BRANDS</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('upload-meme.create') }}" class="nav-menu-link">WEEKLY BATTLE</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('blogs.index') }}" class="nav-menu-link">BLOG</a>

            @if(auth()->check() && auth()->user()->isAdmin())
                <span class="nav-divider">|</span>
                <a href="{{ route('blogs.dashboard') }}" class="nav-menu-link text-warning">BLOG DASHBOARD</a>
            @endif
        </div>
        @endunless
        
        <div class="nav-actions">
             @unless(request()->routeIs('account.settings'))
                @if(auth()->check() && auth()->user()->isMemeUser())
                    <form action="{{ route('memes.search') }}" method="GET" class="input-group input-group-sm d-none d-xl-flex" style="width: 160px;">
                        <input type="text" name="q" class="form-control" placeholder="SEARCH" style="border-right: none; background: #f9fafb;">
                        <button type="submit" class="input-group-text bg-light text-muted" style="border-left: none; background: #f9fafb;"><i class="bi bi-search"></i></button>
                    </form>
                @endif

                @if(auth()->check() && auth()->user()->isAdmin())
                    <div class="d-none d-md-flex gap-2">
                        <a href="{{ route('upload-meme.create') }}" class="btn btn-sm fw-bold text-white px-3" style="background-color: var(--brand-purple);">UPLOAD</a>
                        <a href="{{ route('blogs.create') }}" class="btn btn-sm fw-bold text-white px-3" style="background-color: var(--brand-orange);">CREATE BLOG</a>
                    </div>
                @endif
             @endunless

             @auth
                 <x-profile-dropdown :user="auth()->user()" />
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-person"></i></a>
            @endauth

            @if(request()->routeIs('home') || request()->routeIs('brands.public'))
            <button class="btn btn-link link-dark d-lg-none p-0 fs-4" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebarOffcanvas">
                <i class="bi bi-list" style="color: var(--brand-purple);"></i>
            </button>
            @endif
        </div>
    </div>
</nav>