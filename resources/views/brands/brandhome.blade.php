<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>OneDollarMeme</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    <link rel="shortcut icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">

    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

    <!-- Emoji Picker -->
    <script type="module" src="https://cdn.jsdelivr.net/npm/emoji-picker-element@^1/index.js"></script>

    <style>
        :root {
            --brand-purple: #5B2E91;
            --brand-orange: #f2994a;
            --brand-bg: #f3f4f6;
        }
        body { background-color: var(--brand-bg); color: #333; font-family: 'Figtree', sans-serif; }
        
        /* Navbar */
        .custom-navbar { background: white; border-bottom: 1px solid #e5e7eb; padding: 0.8rem 0; }
        .brand-logo { color: var(--brand-purple) !important; font-weight: 800; font-size: 1.4rem; display: flex; align-items: center; gap: 8px; text-decoration: none; }
        .nav-menu-link { text-transform: uppercase; font-weight: 700; font-size: 0.8rem; color: #6b7280; letter-spacing: 0.5px; text-decoration: none; padding: 0 10px; transition: color 0.2s; }
        .nav-menu-link:hover, .nav-menu-link.active { color: var(--brand-purple); }
        .nav-divider { color: #e5e7eb; }

        /* Dropdown */
        .dropdown { position: relative; }
        .dropdown-menu { max-height: 300px; overflow-y: auto; }
        .dropdown-menu .dropdown-item {
            transition: none !important;
        }
        .dropdown-menu .dropdown-item:hover,
        .dropdown-menu .dropdown-item:focus {
            background-color: inherit !important;
            color: inherit !important;
            outline: none !important;
        }
        .dropdown-menu .dropdown-item:active {
            background-color: inherit !important;
            color: inherit !important;
        }

        /* Hero */
        .hero-box { background: #f8f0fc; border-radius: 12px; padding: 1.5rem 2rem; display: flex; flex-direction: column; justify-content: center; min-height: 50vh; height: auto; }

        @media (min-width: 992px) {
            .hero-box { min-height: 50vh; }
            .featured-card { min-height: 50vh; }
        }
        .hero-title { color: var(--brand-purple); font-weight: 900; font-size: 1.8rem; line-height: 1.1; margin-bottom: 0.25rem; }
        .hero-subtitle { font-size: 1rem; color: #203552ff; margin-bottom: 1rem; }
        .btn-hero-orange { background-color: var(--brand-orange); color: white; font-weight: 700; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; text-decoration: none; display: inline-block; transition: opacity 0.2s; }
        .btn-hero-orange:hover { color: white; opacity: 0.9; }
        .btn-hero-purple { background-color: var(--brand-purple); color: white; font-weight: 700; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; text-decoration: none; display: inline-block; transition: opacity 0.2s; }
        .btn-hero-purple:hover { color: white; opacity: 0.9; }
        
        .featured-card { background: #f8f0fc; border-radius: 12px; min-height: 50vh; height: auto; border: 1px solid #e5e7eb; overflow: hidden; }
        .featured-header { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: #6b7280; }
        .featured-card .flex-grow-1 img { width: 100% !important; object-fit: cover !important; height: 100% !important; display: block !important; }
        .featured-card .position-absolute { display: none !important; }

        /* Layout & Cards */
        .left-sidebar-box { background: white; border-radius: 12px; overflow: hidden; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .ls-link { display: block; padding: 10px 20px; color: #4b5563; font-weight: 600; font-size: 0.9rem; text-decoration: none; border-left: 3px solid transparent; }
        .ls-link:hover, .ls-link.active { background-color: #f9fafb; color: var(--brand-purple); border-left-color: var(--brand-purple); }
        .ls-header { padding: 15px 20px 5px; font-weight: 800; font-size: 0.8rem; color: #9ca3af; text-transform: uppercase; }
        
        .arena-box { background: white; border-radius: 12px; border: 1px solid #e9d5ff; padding: 1.5rem; margin-bottom: 2rem; }
        .arena-title { color: var(--brand-purple); font-weight: 800; text-transform: uppercase; font-size: 1rem; }

        .post-card { background: white; border-radius: 1rem; padding: 1rem; margin-bottom: 1.5rem; box-shadow: 0 4px 12px rgba(0,0,0,0.1); border: none; }
        .post-card .img-fluid { border-radius: 8px; }

        .winner-badge {
            position: absolute;
            top: -10px;
            left: -10px;
            z-index: 10;
            padding: 8px 15px;
            font-weight: bold;
            border-radius: 20px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.2);
        }
        .rank-1 { background: linear-gradient(45deg, #ffd700, #ffae00); color: black; }
        .rank-2 { background: linear-gradient(45deg, #c0c0c0, #a0a0a0); color: black; }
        .rank-3 { background: linear-gradient(45deg, #cd7f32, #b87333); color: white; }

        /* Buttons from existing styles preserved but tweaked */
        .reaction-emoji { font-size: 1.2rem; }
        
        /* Dark Mode overrides (preserving minimal functionality) */
        body.dark-mode { background-color: #121212; color: #e5e5e5; }
        body.dark-mode .custom-navbar, body.dark-mode .left-sidebar-box, body.dark-mode .post-card, body.dark-mode .featured-card, body.dark-mode .hero-box { background-color: #1e1e1e; border-color: #333; }
        body.dark-mode .brand-logo, body.dark-mode .hero-title, body.dark-mode .arena-title { color: #bb86fc !important; }
        body.dark-mode .ls-link { color: #ccc; } 
        body.dark-mode .ls-link:hover { background-color: #333; }

        .comment-list {
            max-height: 450px;
            overflow-y: auto;
            overflow-x: hidden;
            padding-right: 5px;
        }

        .comment-list::-webkit-scrollbar {
            width: 5px;
        }

        .comment-list::-webkit-scrollbar-track {
            background: transparent;
        }

        .comment-list::-webkit-scrollbar-thumb {
            background: #e0e0e0;
            border-radius: 10px;
        }

        .comment-list::-webkit-scrollbar-thumb:hover {
            background: #d0d0d0;
        }

        /* Mobile Bottom Navigation */
        @media (max-width: 991px) {
            .mobile-bottom-nav {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                background: white;
                border-top: 1px solid #e5e7eb;
                z-index: 1000;
                padding: 8px 0;
                padding-bottom: max(8px, env(safe-area-inset-bottom));
                box-shadow: 0 -2px 10px rgba(0,0,0,0.1);
            }

            .mobile-bottom-nav .nav-item {
                display: flex;
                flex-direction: column;
                align-items: center;
                justify-content: center;
                text-decoration: none;
                color: #6b7280;
                font-size: 0.65rem;
                font-weight: 600;
                padding: 4px 6px;
                transition: color 0.2s;
            }

            .mobile-bottom-nav .nav-item i {
                font-size: 1.2rem;
                margin-bottom: 2px;
            }

            .mobile-bottom-nav .nav-item:hover,
            .mobile-bottom-nav .nav-item.active {
                color: var(--brand-purple);
            }

            .mobile-bottom-nav .nav-item.active i {
                transform: scale(1.1);
            }

            /* Main content padding for mobile */
            .mobile-main-content {
                padding-bottom: 70px;
            }
            
            /* Mobile sidebar content */
            .mobile-sidebar-content {
                padding-bottom: 80px !important;
            }

            /* Center memes on mobile */
            .mobile-feed-container {
                max-width: 100%;
                margin: 0;
                padding: 0 10px;
            }

            /* Hide desktop sidebar on mobile */
            .desktop-sidebar {
                display: none !important;
            }

            /* Hero section mobile adjustments */
            .hero-box {
                padding: 1rem;
                min-height: auto;
            }

            .hero-title {
                font-size: 1.3rem;
            }

            .hero-subtitle {
                font-size: 0.85rem;
            }

            .btn-hero-orange,
            .btn-hero-purple {
                padding: 0.4rem 0.9rem;
                font-size: 0.8rem;
            }

            /* Brand header mobile */
            .brand-header {
                padding: 1.5rem 1rem !important;
                min-height: auto !important;
                border-radius: 12px !important;
            }
            
            .brand-header h1 {
                font-size: 1.3rem !important;
            }
            
            .brand-header p {
                font-size: 0.85rem !important;
            }

            .right-sidebar {
                display: none !important;
            }
            
            /* Mobile sidebar content styling */
            .mobile-sidebar-content .card {
                margin-bottom: 1rem;
            }
        }
        
        /* Small phones (max-width: 375px) */
        @media (max-width: 375px) {
            .mobile-bottom-nav .nav-item {
                font-size: 0.6rem;
                padding: 4px 4px;
            }
            
            .mobile-bottom-nav .nav-item i {
                font-size: 1.1rem;
            }
            
            .mobile-bottom-nav .nav-item span {
                font-size: 0.55rem;
            }
            
            .hero-title {
                font-size: 1.2rem;
            }
            
            .btn-hero-orange,
            .btn-hero-purple {
                padding: 0.35rem 0.7rem;
                font-size: 0.75rem;
            }
            
            .mobile-feed-container {
                padding: 0 5px;
            }
            
            .post-card {
                padding: 0.5rem;
            }
            
            .brand-header h1 {
                font-size: 1.1rem !important;
            }
        }
        
        /* Tablets (768px - 991px) */
        @media (min-width: 768px) and (max-width: 991px) {
            .mobile-feed-container {
                max-width: 720px;
                margin: 0 auto;
            }
            
            .hero-title {
                font-size: 1.5rem;
            }
            
            .btn-hero-orange,
            .btn-hero-purple {
                padding: 0.45rem 1rem;
                font-size: 0.85rem;
            }
        }

        /* Desktop - hide mobile nav and keep original layout */
        @media (min-width: 992px) {
            .mobile-bottom-nav {
                display: none !important;
            }
            
            .mobile-main-content {
                padding-bottom: 0 !important;
            }
            
            .mobile-feed-container {
                max-width: none !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            
            .mobile-sidebar-content {
                display: none !important;
            }
        }
        
        /* Large Desktop (min-width: 1400px) */
        @media (min-width: 1400px) {
            .container {
                max-width: 1320px;
            }
            
            .post-card {
                padding: 1.25rem;
            }
            
            .hero-title {
                font-size: 2rem;
            }
        }
    </style>

    <meta name="csrf-token" content="{{ csrf_token() }}">
</head>
<body>
    @include('partials._toast')

<!-- Navbar -->
<nav class="custom-navbar sticky-top">
    <div class="container d-flex flex-wrap justify-content-between align-items-center">
        <!-- Logo -->
        <a class="brand-logo" href="{{ route('home') }}">
            <img src="{{ asset('image/my-logo.jpg') }}" width="35" height="35" class="rounded-circle shadow-sm" alt="Logo">
            OneDollarMeme
        </a>
        
        <!-- Center Links Removed -->
        
        <!-- Right Actions -->
        <div class="d-flex align-items-center gap-3">
            <a href="{{ route('brands.index') }}" class="btn btn-sm fw-bold text-white px-3" style="background-color: var(--brand-purple);">FOR BRANDS</a>
            
             <!-- User Profile / Auth -->
            @auth
                 <x-profile-dropdown :user="auth()->user()" />
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-person"></i></a>
            @endauth
        </div>
    </div>
</nav>


<!-- Hero Section -->
<div class="container my-4">
@if(isset($featuredBrand))
    <!-- Show Dynamic Brand Header if exists -->
    <div class="brand-header p-5 rounded-[40px] text-white mb-2 position-relative overflow-hidden" 
         style="background: linear-gradient(135deg, {{ $featuredBrand->theme_color ?? '#6f42c1' }} 0%, #000 100%); min-height: 250px; border-radius: 20px;">
        
        <!-- Background Pattern -->
        <div class="position-absolute top-0 end-0 opacity-10 w-100 h-100" 
             style="background-image: radial-gradient(#fff 1px, transparent 1px); background-size: 20px 20px;"></div>
        
        <div class="position-relative z-1 d-flex flex-column align-items-center text-center">
            @if($featuredBrand->logo)
                <img src="{{ asset('storage/' . $featuredBrand->logo) }}" alt="{{ $featuredBrand->company_name }}"
                     class="rounded-circle shadow-lg mb-3 border border-4 border-white" style="width: 100px; height: 100px; object-fit: cover;">
            @else
                <div class="rounded-circle shadow-lg mb-3 bg-white d-flex align-items-center justify-content-center text-dark fw-bold"
                     style="width: 80px; height: 80px; font-size: 1.5rem;">
                    {{ substr($featuredBrand->company_name, 0, 1) }}
                </div>
            @endif

            <h1 class="h2 fw-black uppercase italic tracking-tight mb-1" style="font-weight: 900; text-transform: uppercase; font-style: italic; letter-spacing: -0.025em;">{{ $featuredBrand->company_name }}</h1>
            <p class="lead opacity-75 small mb-3" style="max-width: 600px;">{{ $featuredBrand->brand_description ?? 'Participate in our brand campaign and win amazing prizes!' }}</p>
            
            <div class="d-flex gap-2">
                <a href="{{ route('brands.show', $featuredBrand->id) }}" 
                   class="btn rounded-pill px-4 fw-bold" 
                   style="background-color: white; color: {{ $featuredBrand->theme_color ?? '#6f42c1' }}; border: none; transition: none;">JOIN CAMPAIGN</a>
                @if($featuredBrand->website)
                    <a href="{{ $featuredBrand->website }}" target="_blank" 
                       class="btn rounded-pill px-4 fw-bold" 
                       style="background-color: white; color: {{ $featuredBrand->theme_color ?? '#6f42c1' }}; border: none; transition: none;">Brand Site</a>
                @endif
            </div>
        </div>
    </div>
@else
    <div class="row g-4 align-items-stretch">
        <!-- Hero Left -->
        <div class="col-lg-8">
            <div class="hero-box">
                <h1 class="hero-title">Post Memes. Win Prizes.</h1>
                <p class="hero-subtitle">Enter the Weekly Battle for $1</p>
                <div class="d-flex gap-3">
                    <a href="{{ route('upload-meme.create') }}" class="btn-hero-orange">join Battle</a>
                    <a href="{{ route('upload-meme.create') }}" class="btn-hero-purple">Upload Meme</a>
                </div>
            </div>
        </div>
        <!-- Hero Right: Featured Meme -->
        <div class="col-lg-4">
            <div class="featured-card d-flex flex-column">

                <div class="flex-grow-1 d-flex align-items-center justify-content-center position-relative overflow-hidden">
                    <img src="{{ asset('image/my-cat.png') }}" class="img-fluid w-100 h-100" style="object-fit: cover;" alt="Featured">
                     <div class="position-absolute d-flex align-items-center justify-content-center w-100 h-100">
                         <i class="bi bi-image fs-1 text-white shadow-sm"></i>
                     </div>
                </div>
            </div>
        </div>
    </div>
@endif
    <div class="row mt-2">
        <div class="col-12 text-center">
        </div>
    </div>
</div>

<!-- Main Content -->
<!-- Main Content Layout -->
<div class="container mt-0 pt-0 mobile-main-content">
    <div class="row">
        <!-- LEFT SIDEBAR - Desktop Only -->
        <div class="col-lg-3 d-none d-lg-block desktop-sidebar">
             <div class="sticky-top" style="top: 80px; z-index: 10;">
                 <div class="left-sidebar-box mt-2">
                     <a href="{{ route('home') }}" class="ls-link">Home</a>
                     <a href="{{ route('brands.index') }}" class="ls-link">For Brands</a>

                     <div style="border-top: 1px solid #f3f4f6; margin: 10px 0;"></div>
                     <div class="px-3 py-1 text-muted fw-bold small uppercase italic">Brand Campaigns</div>
                     @if(isset($brands) && count($brands) > 0)
                        @foreach($brands->take(5) as $brand)
                            <a href="{{ route('brands.show', $brand->id) }}" class="ls-link d-flex align-items-center gap-2">
                                @if($brand->logo)
                                    <img src="{{ asset('storage/' . $brand->logo) }}" alt="" style="width: 20px; height: 20px; object-fit: cover;" class="rounded-circle">
                                @else
                                    <div class="rounded-circle bg-purple text-white d-flex align-items-center justify-content-center" style="width: 20px; height: 20px; font-size: 0.6rem;">
                                        {{ strtoupper(substr($brand->company_name, 0, 1)) }}
                                    </div>
                                @endif
                                {{ $brand->company_name }}
                            </a>
                        @endforeach
                     @endif

                     <div style="border-top: 1px solid #f3f4f6; margin: 10px 0;"></div>
                     <a href="{{ route('brands.work') }}" class="ls-link" style="color: var(--brand-purple);">How it works</a>

                     <div style="border-top: 1px solid #f3f4f6; margin: 10px 0;"></div>
                     <div class="px-3 py-1 text-muted fw-bold small uppercase italic">Trending Tags</div>
                     <a href="#" class="ls-link">#RelatableVibes</a>
                     <a href="#" class="ls-link">#DankHumor</a>
                     <a href="#" class="ls-link">#CatLife</a>

                 </div>
             </div>
        </div>

        <!-- MAIN FEED -->
        <div class="col-lg-6">
            <!-- Mobile-only container for centered feed -->
            <div class="mobile-feed-container d-lg-block">
            <!-- Hero Section removed from here -->
            
            <div class="text-center fw-bold text-muted small mb-2 d-lg-none">MAIN FEED</div>
            
            <!-- Arena box removed from brands page -->
            
            <!-- Trending Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                 <div class="fw-bold" style="color: var(--brand-purple); font-size: 1.2rem;">TRENDING MEMES</div>
                 <div class="d-flex gap-2 small text-muted">
                     <button type="button" class="btn btn-sm btn-outline-secondary active" data-brand-filter="all">All</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary" data-brand-filter="fresh">Fresh</button>
                 </div>
            </div>

            <script>
            document.addEventListener('DOMContentLoaded', function() {
                const filterBtns = document.querySelectorAll('[data-brand-filter]');
                filterBtns.forEach(btn => {
                    btn.addEventListener('click', function() {
                        filterBtns.forEach(b => b.classList.remove('active'));
                        this.classList.add('active');
                        const filter = this.dataset.brandFilter;
                        const now = new Date();
                        const weekStart = new Date(now);
                        weekStart.setDate(now.getDate() - now.getDay());
                        weekStart.setHours(0, 0, 0, 0);

                        document.querySelectorAll('.post-card.meme').forEach(card => {
                            if (filter === 'all') {
                                card.style.display = '';
                            } else if (filter === 'fresh') {
                                const createdAt = new Date(card.dataset.createdAt);
                                card.style.display = createdAt >= weekStart ? '' : 'none';
                            }
                        });
                    });
                });
            });
            </script>
        
            @php $userLoggedIn = auth()->check(); @endphp

            @foreach($memes as $meme)
            <div class="post-card meme relative" id="meme-{{ $meme->id }}" data-id="{{ $meme->id }}" data-created-at="{{ $meme->created_at }}" data-is-contest="{{ $meme->is_contest ? 1 : 0 }}" data-score="{{ $meme->score ?? 0 }}" style="scroll-margin-top: 100px;">

                <!-- 3-dot menu for post owner -->
                @if(auth()->check() && $meme->user_id == auth()->id())
                <div class="position-relative">
                    <button class="menu-button btn btn-sm position-absolute" type="button" style="top: 0; right: 0; z-index: 10;" data-target="#menu-{{ $meme->id }}">
                        ⋮
                    </button>
                    <div class="collapse position-absolute" style="top: 30px; right: 0; z-index: 5;" id="menu-{{ $meme->id }}">
                        <div class="card card-body p-2" style="width: 150px; border: 1px solid #ddd; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);">
                            <a class="dropdown-item p-2" href="{{ route('memes.edit', $meme) }}">Edit Post</a>
                            <form method="POST" action="{{ route('memes.destroy', $meme) }}" style="display: inline;">
                                @csrf
                                @method('DELETE')
                                <button type="button" class="dropdown-item text-danger p-2 w-100 text-start delete-meme-btn">Delete Post</button>
                            </form>
                        </div>
                    </div>
                </div>
                @endif
                
                <!-- Meme Content Structure Matching Design -->
                <div class="d-flex gap-3">
                     <!-- Thumbnail (if we wanted to do list view, but sticking to large for now per plan, 
                     actually let's just make the user info cleaner) -->
                </div>

                <!-- User Info Profile Section -->
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $meme->user->profile_photo_url }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $meme->user->name ?? 'Avatar' }}">
                    <div class="flex-grow-1 text-start">
                        <div class="d-flex align-items-center gap-1">
                            <span class="fw-bold small d-block text-dark">{{ $meme->user->name ?? 'Unknown' }}</span>

                        </div>
                        @if($meme->brand)
                            <div class="small text-muted" style="font-size: 0.75rem;">
                                Campaign for: <span class="fw-bold text-dark">{{ $meme->brand->company_name }}</span>
                            </div>
                        @endif
                    </div>

                    @if(auth()->check() && (auth()->id() == $meme->user_id || auth()->user()->is_admin))
                    <div class="dropdown ms-auto">
                        <button class="btn btn-link link-dark p-0 text-decoration-none" type="button" data-bs-toggle="dropdown" aria-expanded="false">
                            <i class="bi bi-three-dots-vertical"></i>
                        </button>
                        <ul class="dropdown-menu dropdown-menu-end shadow border-0" style="border-radius: 12px; font-size: 0.85rem;">
                            <li><a class="dropdown-item py-2" href="{{ route('memes.edit', $meme->id) }}"><i class="bi bi-pencil me-2"></i> Edit Meme</a></li>
                            <li><hr class="dropdown-divider"></li>
                            <li>
                                <form action="{{ route('memes.destroy', $meme->id) }}" method="POST" onsubmit="return confirm('Are you sure you want to delete this meme?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="dropdown-item py-2 text-danger"><i class="bi bi-trash me-2"></i> Delete Meme</button>
                                </form>
                            </li>
                        </ul>
                    </div>
                    @endif
                </div>

                <div class="row">
                     <div class="col-12">

                         @if($meme->title && $meme->title !== 'Untitled')
                             <h6 class="meme-title fw-bold mb-2" style="white-space: pre-line;">{{ $meme->title }}</h6>
                         @endif

                         @if($meme->image_path)
                             <div class="position-relative">
                                 <img src="{{ asset('storage/'.$meme->image_path) }}"
                                      class="img-fluid rounded border
                                             @if($meme->template == 'portrait') template-portrait
                                             @elseif($meme->template == 'landscape') template-landscape
                                             @else template-square
                                             @endif" style="width: 100%;">
                             </div>
                         @endif
                     </div>
                </div>

                <!-- Stats / Actions Layout -->
                <div class="mt-3 d-flex align-items-center gap-3 border-top pt-2">
                      <!-- Reactions (Standard) -->
                      <button class="react-toggle-btn btn btn-light btn-sm d-flex align-items-center gap-1 rounded-pill" type="button" data-meme-id="{{ $meme->id }}">
                          <span class="reaction-emoji">{{ $meme->userEmoji ?? '😀' }}</span>
                          <span class="reaction-count fw-bold text-dark">{{ $meme->reactions->count() }}</span>
                      </button>

                      <!-- Comments -->
                      <button class="comment-toggle-btn btn btn-light btn-sm d-flex align-items-center gap-1 rounded-pill" type="button" data-meme-id="{{ $meme->id }}">
                          <i class="bi bi-chat-fill text-primary"></i>
                          <span class="comment-count fw-bold text-dark">{{ $meme->comments->count() }}</span>
                      </button>
                      
                       <!-- Share -->
                       <button class="meme-share-btn bg-gray-100 text-black text-sm md:text-base font-medium py-1 px-4 rounded border hover:bg-gray-200 transition ms-auto" type="button" onclick="showMemeShareModal(event, {{ $meme->id }})">
                           <i class="bi bi-share-fill text-muted"></i>
                      </button>
                </div>

                <!-- Hidden sections (Comment/Emoji/Share) kept for functionality -->
                


                <!-- User-specific reactions line -->
                <div class="user-reactions-line mt-2" style="display: flex; flex-wrap: wrap; gap: 5px; align-items: center;">
                    @php
                        $userReactions = [];
                        $allReactions = $meme->reactions->groupBy('emoji');

                        foreach($allReactions as $emoji => $reactions) {
                            $count = count($reactions);
                            $userReacted = $reactions->contains(function($reaction) {
                                return $reaction->user_id == auth()->id();
                            });

                            $userReactions[] = [
                                'emoji' => $emoji,
                                'count' => $count,
                                'user_reacted' => $userReacted
                            ];
                        }

                        usort($userReactions, function($a, $b) {
                            if ($a['user_reacted'] && !$b['user_reacted']) return -1;
                            if (!$a['user_reacted'] && $b['user_reacted']) return 1;
                            return $b['count'] - $a['count'];
                        });
                    @endphp

                    @foreach($userReactions as $reactionData)
                        <span class="user-reaction-item"
                              style="display: flex; align-items: center; padding: 2px 6px; border-radius: 12px; font-size: 0.8rem;
                                     {{ $reactionData['user_reacted'] ? 'background-color: #d1ecf1; border: 1px solid #bee5eb;' : 'background-color: #f8f9fa; border: 1px solid #eee;' }}">
                            <span class="reaction-emoji">{{ $reactionData['emoji'] }}</span>
                            <span class="reaction-count ms-1">{{ $reactionData['count'] }}</span>
                        </span>
                    @endforeach
                </div>

                <!-- Emoji Section (Standard) -->
                <div id="emoji-section-{{ $meme->id }}" style="display:none;" class="mt-2 text-start">
                    <emoji-picker id="emoji-picker-{{ $meme->id }}"></emoji-picker>
                </div>

                <!-- Comment Section (Standard) -->
                <div id="comment-section-{{ $meme->id }}" style="display:none;" class="mt-2">
                    <!-- Comment Form -->
                    <form class="comment-form d-flex mt-2" data-id="{{ $meme->id }}">
                        @csrf
                        <input type="text" name="content" class="form-control form-control-sm me-1" placeholder="Add a comment">
                        <button type="submit" class="btn btn-sm" style="background-color: var(--brand-purple); color: white;">Comment</button>
                    </form>

                    <!-- Comment List -->
                    <ul class="comment-list mt-2 list-unstyled">
                        @foreach($meme->comments->whereNull('parent_id') as $comment)
                            @include('partials._comment_item', ['comment' => $comment, 'meme' => $meme])
                        @endforeach
                    </ul>
                </div>

            </div>
            @endforeach
            </div> <!-- Close mobile-feed-container -->
        </div>

        <div class="col-lg-3 right-sidebar">
            <!-- Sidebar cards -->
            <div class="sticky-top" style="top: 80px; z-index: 10;">
                @include('partials._leaderboard-widget', ['hideTopCreators' => true, 'hideWinnerSpotlight' => true])
            </div>
        </div>
    </div>
</div>

<!-- Mobile Bottom Navigation -->
<nav class="mobile-bottom-nav d-lg-none">
    <div class="container-fluid">
        <div class="d-flex justify-content-around align-items-center">
            <a href="{{ route('home') }}" class="nav-item {{ request()->routeIs('home') ? 'active' : '' }}">
                <i class="bi bi-house-door-fill"></i>
                <span>Home</span>
            </a>
            <a href="{{ route('memes.index', ['sort' => 'trending']) }}" class="nav-item {{ request()->routeIs('memes.index') ? 'active' : '' }}">
                <i class="bi bi-fire"></i>
                <span>Trending</span>
            </a>
            <a href="{{ route('upload-meme.create') }}" class="nav-item {{ request()->routeIs('upload-meme.create') ? 'active' : '' }}">
                <i class="bi bi-plus-circle-fill" style="font-size: 1.8rem; color: var(--brand-purple);"></i>
                <span>Upload</span>
            </a>
            <a href="{{ route('brands.public') }}" class="nav-item {{ request()->routeIs('brands.public') ? 'active' : '' }}">
                <i class="bi bi-buildings"></i>
                <span>Brands</span>
            </a>
            @auth
            <a href="{{ route('profile.edit') }}" class="nav-item">
                <i class="bi bi-person-circle"></i>
                <span>Profile</span>
            </a>
            @else
            <a href="{{ route('login') }}" class="nav-item">
                <i class="bi bi-person"></i>
                <span>Login</span>
            </a>
            @endauth
        </div>
    </div>
</nav>

<!-- Mobile Sidebar Content (Above Bottom Nav) -->
<div class="d-lg-none mobile-sidebar-content" style="padding: 10px; max-width: 600px; margin: 0 auto;">
    @include('partials._leaderboard-widget', ['hideTopCreators' => true, 'hideWinnerSpotlight' => true])
</div>

<div id="toast-container" style="position: fixed; top: 20px; right: 20px; z-index: 9999;"></div>
<style>
    .toast-card { background: white; padding: 12px 20px; margin-bottom: 10px; border-radius: 8px; box-shadow: 0 4px 12px rgba(0,0,0,0.15); display: flex; align-items: center; gap: 10px; animation: toast-in 0.3s ease-out; border-left: 5px solid #28a745; }
    .toast-card.error { border-left-color: #dc3545; }
    .toast-card.info { border-left-color: #17a2b8; }
    @keyframes toast-in { from { transform: translateX(100%); opacity: 0; } to { transform: translateX(0); opacity: 1; } }
    @keyframes toast-out { from { transform: translateX(0); opacity: 1; } to { transform: translateX(100%); opacity: 0; } }
</style>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        window.showToast = function(message, type = 'success') {
            const container = document.getElementById('toast-container');
            if (!container) return;
            const toast = document.createElement('div');
            toast.className = `toast-card ${type}`;
            let icon = 'bi-check-circle-fill';
            if (type === 'error') icon = 'bi-exclamation-triangle-fill';
            if (type === 'info') icon = 'bi-info-circle-fill';
            toast.innerHTML = `<i class="bi ${icon}"></i> <span>${message}</span>`;
            container.appendChild(toast);
            setTimeout(() => {
                toast.style.animation = 'toast-out 0.3s ease-in forwards';
                setTimeout(() => toast.remove(), 300);
            }, 3000);
        };

        const toggle = document.getElementById("darkModeToggle");
    const icon = toggle.querySelector("i");
    toggle.addEventListener("click", () => {
        document.body.classList.toggle("dark-mode");
        icon.classList.toggle("bi-moon");
        icon.classList.toggle("bi-sun");
    });
    
    // Auto-hide elements
    $(document).ready(function() {
         $('.collapse').collapse('hide');
    });
</script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loggedIn = {{ auth()->check() ? 'true' : 'false' }};
    const userId = {{ auth()->id() ?? 'null' }};
    window.user = { id: userId };

    // Toggle Emoji Section
    document.querySelectorAll('.react-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const memeId = btn.dataset.memeId;
            if (!loggedIn) { window.location = '{{ route("login") }}'; return; }
            const section = document.getElementById('emoji-section-' + memeId);
            section.style.display = (section.style.display === 'none' || section.style.display === '') ? 'block' : 'none';
        });
    });

    // Toggle Comment Section
    document.querySelectorAll('.comment-toggle-btn').forEach(btn => {
        btn.addEventListener('click', () => {
            const memeId = btn.dataset.memeId;
            if (!loggedIn) { window.location = '{{ route("login") }}'; return; }
            const section = document.getElementById('comment-section-' + memeId);
            section.style.display = (section.style.display === 'none' || section.style.display === '') ? 'block' : 'none';
        });
    });

    // Emoji Picker click → update only button emoji
    document.querySelectorAll('emoji-picker').forEach(picker => {
        picker.addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            const postCard = picker.closest('.post-card');
            const memeId = postCard.dataset.id;
            if (!loggedIn) { window.location = '{{ route("login") }}'; return; }

            const reactBtn = postCard.querySelector('.react-toggle-btn');
            reactBtn.textContent = emoji;

            $.ajax({
                url: '/memes/' + memeId + '/reaction',
                method: 'POST',
                contentType: 'application/json',
                data: JSON.stringify({ emoji }),
                headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
                success: function(res) {
                    if(res.success) {
                        // Update the reaction count
                        $(postCard).find('.reaction-count').text(res.total_count);

                        // Update the reactions line HTML
                        $(postCard).find('.user-reactions-line').html(res.reactions_html);
                    }
                },
                error: function(xhr) {
                    console.error('Error:', xhr);
                    alert('An error occurred while adding the reaction');
                }
            });
        });
    });

    // Comment form submit via AJAX
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        let memeId = $(this).data('id');
        let input = $(this).find('input[name="content"]');
        let form = $(this);
        if(input.val().trim() === '') return;

        $.ajax({
            url: '/meme/' + memeId + '/comment',
            method: 'POST',
            data: {
                _token: $('meta[name="csrf-token"]').attr('content'),
                content: input.val()
            },
            success: function(res) {
                if(res.success) {
                    let commentList = form.closest('.post-card').find('.comment-list');

                    // Create new comment element with reply/delete functionality
                    let newCommentHtml = '<li class="comment-item" data-comment-id="' + res.comment.id + '">';
                    newCommentHtml += '<div class="comment-content" tabindex="0">';
                    newCommentHtml += '<strong>' + res.comment.user.name + '</strong>: ' + res.comment.body;
                    newCommentHtml += '<div class="comment-actions">';
                    newCommentHtml += '<button class="comment-action-btn reply-btn" data-comment-id="' + res.comment.id + '">Reply</button>';
                    newCommentHtml += '<button class="comment-action-btn copy-btn" data-comment-body="' + res.comment.body + '" title="Copy comment">📋</button>';

                    // Add delete button if it's the current user's comment
                    @if(auth()->check() && auth()->id())
                        if(res.comment.user.id == {{ auth()->id() }}) {
                            newCommentHtml += '<button class="comment-action-btn delete-btn" data-comment-id="' + res.comment.id + '" title="Delete comment" style="display: none;">🗑️</button>';
                        }
                    @endif

                    newCommentHtml += '</div></div>';
                    newCommentHtml += '<div class="reply-form-container" style="display: none;">';
                    newCommentHtml += '<form class="reply-form mt-2" data-parent-id="' + res.comment.id + '" data-meme-id="' + memeId + '">';
                    newCommentHtml += '@csrf';
                    newCommentHtml += '<input type="hidden" name="parent_id" value="' + res.comment.id + '">';
                    newCommentHtml += '<input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>';
                    newCommentHtml += '<button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>';
                    newCommentHtml += '<button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>';
                    newCommentHtml += '</form></div>';
                    newCommentHtml += '<ul class="replies-container mt-2" style="margin-left: 20px; border-left: 1px solid #eee; padding-left: 10px;"></ul>';
                    newCommentHtml += '</li>';

                    commentList.append(newCommentHtml);
                    commentList.scrollTop(commentList[0].scrollHeight);
                    input.val('');

                    // Update comment count like home page
                    form.closest('.post-card').find('.comment-count').text(res.comments_count);
                } else {
                    alert('Failed to submit comment: ' + (res.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                let errorMessage = 'An error occurred while submitting the comment';
                if (xhr.status === 422 && xhr.responseJSON && xhr.responseJSON.errors) {
                    errorMessage = Object.values(xhr.responseJSON.errors).flat().join('\n');
                } else if (xhr.responseJSON && xhr.responseJSON.message) {
                    errorMessage = xhr.responseJSON.message;
                }
                alert('Failed to submit comment: ' + errorMessage);
            }
        });
    });

    // Handle reply button clicks
    $(document).on('click', '.reply-btn', function(e) {
        e.preventDefault();
        const commentId = $(this).data('comment-id');
        const replyFormContainer = $(this).closest('.comment-content').next('.reply-form-container');

        // Hide all other reply forms
        $('.reply-form-container').hide();

        // Show the reply form for this comment
        replyFormContainer.show();
        replyFormContainer.find('input[name="body"]').focus();
    });

    // Handle cancel reply button clicks
    $(document).on('click', '.cancel-reply', function(e) {
        e.preventDefault();
        const replyFormContainer = $(this).closest('.reply-form-container');
        replyFormContainer.hide();
    });

    // Handle reply form submissions
    $(document).on('submit', '.reply-form', function(e) {
        e.preventDefault();

        const form = $(this);
        const parentId = form.data('parent-id');
        const memeId = form.data('meme-id');
        const bodyInput = form.find('input[name="body"]');
        const body = bodyInput.val().trim();

        if (!body) {
            alert('Please enter a reply');
            return;
        }

        $.ajax({
            url: `/api/meme/${memeId}/comments`,
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            data: JSON.stringify({
                body: body,
                parent_id: parseInt(parentId)
            }),
            success: function(data) {
                if (data.success) {
                    // Create new reply element with reply button and sub-container for nested replies
                    let newReplyHtml = '<li class="reply-item" data-comment-id="' + data.comment.id + '">';
                    newReplyHtml += '<div class="comment-content" tabindex="0">';
                    newReplyHtml += '<strong>' + data.comment.user.name + '</strong>: ' + data.comment.body;
                    newReplyHtml += '<div class="comment-actions mt-1">';
                    newReplyHtml += '<button class="comment-action-btn reply-btn small text-primary border-0 bg-transparent p-0 me-2" data-comment-id="' + data.comment.id + '">Reply</button>';
                    newReplyHtml += '<button class="comment-action-btn copy-btn small text-secondary border-0 bg-transparent p-0" data-comment-body="' + data.comment.body + '" title="Copy reply">📋</button>';

                    // Add delete button if it's the current user's reply
                    @if(auth()->check() && auth()->id())
                        if(data.comment.user.id == {{ auth()->id() }}) {
                            newReplyHtml += '<button class="comment-action-btn delete-btn small text-danger border-0 bg-transparent p-0" data-comment-id="' + data.comment.id + '" title="Delete reply" style="display: none;">🗑️</button>';
                        }
                    @endif

                    newReplyHtml += '</div></div>';
                    newReplyHtml += '<div class="reply-form-container" style="display: none;">';
                    newReplyHtml += '<form class="reply-form mt-2" data-parent-id="' + data.comment.id + '" data-meme-id="' + memeId + '">';
                    newReplyHtml += '@csrf';
                    newReplyHtml += '<input type="hidden" name="parent_id" value="' + data.comment.id + '">';
                    newReplyHtml += '<input type="text" name="body" class="form-control form-control-sm" placeholder="Write a reply..." required>';
                    newReplyHtml += '<button type="submit" class="btn btn-sm btn-primary mt-1">Reply</button>';
                    newReplyHtml += '<button type="button" class="btn btn-sm btn-secondary mt-1 cancel-reply">Cancel</button>';
                    newReplyHtml += '</form></div>';
                    newReplyHtml += '<ul class="replies-container mt-2 list-unstyled" style="margin-left: 20px; border-left: 2px solid #eee; padding-left: 10px;"></ul>';
                    newReplyHtml += '</li>';

                    // Find the replies container for this parent comment
                    const parentCommentElement = $(`[data-comment-id="${parentId}"]`);
                    const repliesContainer = parentCommentElement.find('.replies-container').first();

                    // Add the new reply to the replies container
                    repliesContainer.append(newReplyHtml);

                    // Clear and hide the reply form
                    bodyInput.val('');
                    form.hide();

                    // Update comment count
                    const countElement = $(`#meme-${memeId} .comment-count`);
                    if (countElement.length) {
                        let currentCount = parseInt(countElement.text()) || 0;
                        countElement.text(currentCount + 1);
                    }
                } else {
                    alert('Failed to submit reply: ' + (data.message || 'Unknown error'));
                }
            },
            error: function(xhr) {
                console.error('Error:', xhr);
                alert('An error occurred while submitting the reply');
            }
        });
    });



    // Handle copy button clicks
    $(document).on('click', '.copy-btn', function(e) {
        e.preventDefault();
        const commentBody = $(this).data('comment-body');

        // Modern clipboard API approach
        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(commentBody).then(function() {
                // Show temporary success feedback
                const originalText = $(e.target).html();
                $(e.target).html('✓ Copied!');

                setTimeout(() => {
                    $(e.target).html(originalText);
                }, 2000);
            }).catch(function(err) {
                console.error('Clipboard API failed: ', err);
                fallbackCopyTextToClipboard(commentBody);
            });
        } else {
            // Fallback for older browsers
            fallbackCopyTextToClipboard(commentBody);
        }
    });

    // Handle comment content focus/blur for delete and copy button visibility
    $(document).on('focus', '.comment-content', function() {
        // Show delete button for owner comments only
        const commentItem = $(this).closest('.comment-item, .reply-item');
        const deleteBtn = commentItem.find('.delete-btn');
        if (deleteBtn.length) {
            deleteBtn.show();
        }

        // Show copy button for all comments
        const copyBtn = commentItem.find('.copy-btn');
        if (copyBtn.length) {
            copyBtn.show();
        }
    });

    $(document).on('blur', '.comment-content', function() {
        // Hide delete button after a delay to allow for clicking
        const commentItem = $(this).closest('.comment-item, .reply-item');
        const deleteBtn = commentItem.find('.delete-btn');
        if (deleteBtn.length) {
            setTimeout(() => {
                deleteBtn.hide();
            }, 300);
        }

        // Hide copy button after a delay to allow for clicking
        const copyBtn = commentItem.find('.copy-btn');
        if (copyBtn.length) {
            setTimeout(() => {
                copyBtn.hide();
            }, 300);
        }
    });

    // Double-click to show delete and copy buttons
    $(document).on('dblclick', '.comment-content', function() {
        const commentItem = $(this).closest('.comment-item, .reply-item');
        const deleteBtn = commentItem.find('.delete-btn');
        if (deleteBtn.length) {
            deleteBtn.show();

            // Hide after delay if not clicked
            setTimeout(() => {
                deleteBtn.hide();
            }, 3000);
        }

        // Show copy button on double-click
        const copyBtn = commentItem.find('.copy-btn');
        if (copyBtn.length) {
            copyBtn.show();

            // Hide after delay if not clicked
            setTimeout(() => {
                copyBtn.hide();
            }, 3000);
        }
    });

});


</script>

<script>
$(document).ready(function() {
    // Bulletproof Handler: Uses 'data-user-toggled' attribute to strictly authorize state changes
    $(document).on('click', '.menu-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        
        var targetId = $(this).data('target');
        // Handle both data-target and data-bs-target if present (bootstrap uses data-bs-target)
        if (!targetId) targetId = $(this).attr('data-bs-target');
        
        var $target = $(targetId);
        
        if ($target.length) {
             // Mark this action as USER-INITIATED
             $target.attr('data-user-toggled', 'true');
             
             if ($target.hasClass('show')) {
                 $target.collapse('hide');
             } else {
                 $target.collapse('show');
             }
             
             setTimeout(() => {
                 $target.removeAttr('data-user-toggled');
             }, 100);
        }
    });

    // Strict Interceptor
    $('.collapse').on('hide.bs.collapse', function(e) {
        // Only guard our specific menus (assuming they have id starting with menu-)
        if (this.id && this.id.startsWith('menu-')) {
            if (!$(this).attr('data-user-toggled')) {
                e.preventDefault();
            }
        }
    });
});
</script>

@include('partials._terms-modal')
@include('partials._battle-timer-script')
<script>
    document.addEventListener('DOMContentLoaded', function () {
        // Profile Dropdown Manual Initialization - Find any element with ID starting with 'profileDropdown-'
        var profileDropdownEl = document.querySelector('[id^="profileDropdown-"]');
        if (profileDropdownEl) {
            // Ensure the dropdown is properly initialized
            var dropdown = new bootstrap.Dropdown(profileDropdownEl, {
                boundary: 'clippingParents',
                popperConfig: null
            });
        }
    });

    // Live leaderboard updates
    function updateLeaderboard() {
        fetch('/api/leaderboard')
            .then(response => response.json())
            .then(data => {
                if (data.top_contestants && data.top_contestants.length > 0) {
                    // Update the top 3 leaderboard items
                    const leaderboardItems = data.top_contestants;

                    // Clear existing content and populate with new data
                    const containers = document.querySelectorAll('.weekly-battle-leaderboard .col-4');

                    // Populate top 3 or however many are available
                    for (let i = 0; i < 3; i++) {
                        if (containers[i]) {
                            let html = '';

                            if (i < leaderboardItems.length) {
                                const item = leaderboardItems[i];

                                // Create image HTML - check if we have image data in the API response
                                let imageHtml = '';
                                if (item.image_path) {
                                    imageHtml = `<img src="${item.image_path}" class="img-fluid rounded w-100" style="height: 100px; object-fit: cover;" alt="Meme">`;
                                } else {
                                    imageHtml = `<div class="p-2 text-center fw-bold text-muted" style="font-size: 0.8rem;">${item.title || 'Untitled'}</div>`;
                                }

                                html = `
                                    <div class="post-card position-relative h-100 d-flex flex-column" style="height: 200px;">
                                        <div class="winner-badge rank-${i + 1}">
                                            #${i + 1}
                                        </div>

                                        <div class="flex-grow-1 d-flex align-items-center justify-content-center rounded bg-light" style="min-height: 100px;">
                                            ${imageHtml}
                                        </div>

                                        <div class="mt-auto d-flex justify-content-center">
                                            <a href="/meme/${item.id}" class="btn btn-sm btn-outline-dark">View</a>
                                        </div>
                                    </div>
                                `;
                            } else {
                                html = `
                                    <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center fw-bold text-purple-500 fs-3" style="color: #a855f7;">#${i + 1}</div>
                                `;
                            }

                            containers[i].innerHTML = html;
                        }
                    }
                }
            })
            .catch(error => {
                console.error('Error updating leaderboard:', error);
            });
    }

    // Update leaderboard every 5 seconds for instant updates
    if (document.querySelector('.weekly-battle-leaderboard')) {
        updateLeaderboard(); // Initial update after page load
        setInterval(updateLeaderboard, 5000); // Update every 5 seconds
    }

    // Filtering functionality for trending memes
    document.addEventListener('DOMContentLoaded', function() {
        // Add classes to meme cards for filtering
        const memeCards = document.querySelectorAll('.meme.relative');
        memeCards.forEach(card => {
            // Add classes for filtering
            card.classList.add('meme-card');

            // Check if it's a battle entry
            const isContest = card.dataset.isContest;
            if (isContest === '1') {
                card.classList.add('battle-entry');
            } else {
                card.classList.add('regular-entry');
            }

            // Check if it's a fresh meme (uploaded in last 24 hours)
            const createdAt = card.dataset.createdAt;
            if (createdAt) {
                const uploadDate = new Date(createdAt);
                const now = new Date();
                const timeDiff = now - uploadDate;
                const hoursDiff = timeDiff / (1000 * 60 * 60);

                if (hoursDiff < 24) { // Less than 24 hours old
                    card.classList.add('fresh-meme');
                }
            }
        });

        // Filter buttons functionality
        const filterButtons = document.querySelectorAll('[data-filter]');
        filterButtons.forEach(button => {
            button.addEventListener('click', function() {
                const filter = this.dataset.filter;

                // Update active button state
                filterButtons.forEach(btn => btn.classList.remove('btn-secondary', 'text-white'));
                this.classList.add('btn-secondary', 'text-white');

                // Apply filter
                memeCards.forEach(card => {
                    card.style.display = 'block'; // Reset display

                    switch(filter) {
                        case 'all':
                            // Show all memes
                            break;
                        case 'fresh':
                            if (!card.classList.contains('fresh-meme')) {
                                card.style.display = 'none';
                            }
                            break;
                        case 'battle':
                            if (!card.classList.contains('battle-entry')) {
                                card.style.display = 'none';
                            }
                            break;
                        default:
                            // For any other filter, show all
                            break;
                    }
                });
            });
        });

    });

</script>
<script src="{{ asset('js/memes-interactions.js') }}"></script>
</body>
</html>
