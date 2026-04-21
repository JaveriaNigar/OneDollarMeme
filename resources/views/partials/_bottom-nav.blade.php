<style>
    /* Bottom Navigation for Mobile */
    .bottom-nav {
        position: fixed;
        bottom: 0;
        left: 0;
        right: 0;
        background: white;
        border-top: 1px solid #e5e7eb;
        box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
        z-index: 9999 !important;
        display: none;
        padding: 0.5rem 0;
    }

    .bottom-nav-container {
        display: flex;
        justify-content: space-around;
        align-items: center;
        max-width: 640px;
        margin: 0 auto;
        padding: 0 0.5rem;
    }

    .bottom-nav-link {
        display: flex;
        flex-direction: column;
        align-items: center;
        justify-content: center;
        text-decoration: none;
        color: #6b7280;
        font-size: 0.7rem;
        font-weight: 600;
        padding: 0.25rem 0.5rem;
        transition: color 0.2s;
        min-width: 60px;
    }

    .bottom-nav-link i {
        font-size: 1.4rem;
        margin-bottom: 0.15rem;
    }

    .bottom-nav-link:hover,
    .bottom-nav-link.active {
        color: #5B2E91;
    }

    @media (max-width: 768px) {
        .bottom-nav {
            display: block !important;
        }

        /* Add padding to main content to prevent overlap with bottom nav */
        main {
            padding-bottom: 5rem !important;
        }
    }
</style>

<nav class="bottom-nav">
    <div class="bottom-nav-container">
        <a href="{{ route('home') }}" class="bottom-nav-link">
            <i class="bi bi-house-door"></i>
            <span>Home</span>
        </a>


        <a href="{{ route('meme-agent') }}" class="bottom-nav-link {{ request()->routeIs('meme-agent') ? 'active' : '' }}">
                <i class="bi bi-robot"></i>
                <span>Agent</span>
        </a>
        
        <a href="{{ route('upload-meme.create') }}" class="bottom-nav-link">
            <i class="bi bi-plus-circle "></i>
            <span>Upload</span>

        </a>

                <a href="{{ route('memes.index') }}" class="bottom-nav-link ">
            <i class="bi bi-fire"></i>
            <span>Trending</span>
        </a>

      
        <a href="{{ route('brands.public') }}" class="bottom-nav-link">
            <i class="bi bi-buildings"></i>
            <span>Brands</span>
        </a>
      
        @auth
            <a href="{{ route('account.settings') }}" class="bottom-nav-link">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>
        @else
            <a href="{{ route('login') }}" class="bottom-nav-link">
                <i class="bi bi-box-arrow-in-right"></i>
                <span>Login</span>
            </a>
        @endauth
    </div>
</nav>
