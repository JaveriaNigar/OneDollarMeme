<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>OneDollarMeme - Memes</title>

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
        .nav-menu-link:hover { color: var(--brand-purple); }
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

        /* Hero styles kept for animation/transition consistency if needed */
        .hero-box { background: #f8f0fc; border-radius: 12px; padding: 1.5rem 2rem; display: flex; flex-direction: column; justify-content: center; min-height: 50vh; height: auto; }
        @media (min-width: 992px) {
            .hero-box { min-height: 50vh; }
            .featured-card { min-height: 50vh; }
        }
        .hero-title { color: var(--brand-purple); font-weight: 900; font-size: 1.8rem; line-height: 1.1; margin-bottom: 0.25rem; }
        .hero-subtitle { font-size: 1rem; color: #4b5563; margin-bottom: 1rem; }
        .btn-hero-orange { background-color: var(--brand-orange); color: white; font-weight: 700; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; text-decoration: none; display: inline-block; transition: opacity 0.2s; }
        .btn-hero-orange:hover { color: white; opacity: 0.9; }
        .btn-hero-purple { background-color: var(--brand-purple); color: white; font-weight: 700; border: none; padding: 0.5rem 1.25rem; border-radius: 6px; text-decoration: none; display: inline-block; transition: opacity 0.2s; }
        .btn-hero-purple:hover { color: white; opacity: 0.9; }
        
        .featured-card { background: #f8f0fc; border-radius: 12px; min-height: 50vh; height: auto; border: 1px solid #e5e7eb; overflow: hidden; }
        .featured-header { padding: 8px 12px; border-bottom: 1px solid #f0f0f0; font-weight: 700; font-size: 0.8rem; text-transform: uppercase; color: #6b7280; }

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

        .reaction-emoji { font-size: 1.2rem; }
        
        /* Dark Mode overrides */
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

    </style>
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
        
        <!-- Center Links -->
        <div class="d-none d-lg-flex align-items-center">
            <a href="{{ route('memes.index', ['sort' => 'trending']) }}" class="nav-menu-link">TRENDING</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('brands.index') }}" class="nav-menu-link">BRANDS</a>
            <span class="nav-divider">|</span>
            <a href="{{ route('upload-meme.create') }}" class="nav-menu-link">WEEKLY BATTLE</a>
            <span class="nav-divider">|</span>

            <a href="{{ route('blogs.index') }}" class="nav-menu-link">BLOG</a>
        </div>
        
        <!-- Right Actions -->
        <div class="d-flex align-items-center gap-3">
             <form action="{{ route('memes.search') }}" method="GET" class="input-group input-group-sm d-none d-md-flex" style="width: 180px;">
                <input type="text" name="q" class="form-control" placeholder="SEARCH" style="border-right: none; background: #f9fafb;">
                <button type="submit" class="input-group-text bg-light text-muted" style="border-left: none; background: #f9fafb; cursor: pointer;"><i class="bi bi-search"></i></button>
            </form>
            <a href="{{ route('upload-meme.create') }}" class="btn btn-sm fw-bold text-white px-3" style="background-color: var(--brand-purple);">UPLOAD</a>
            
             <!-- User Profile / Auth -->
            @auth
                 <x-profile-dropdown :user="auth()->user()" />
            @else
                <a href="{{ route('login') }}" class="btn btn-outline-secondary btn-sm rounded-circle"><i class="bi bi-person"></i></a>
            @endauth

            <!-- Mobile Sidebar Trigger -->
            <button class="btn btn-link link-dark d-lg-none p-0 ms-1 fs-5 text-decoration-none" type="button" data-bs-toggle="offcanvas" data-bs-target="#mobileSidebarOffcanvas" aria-controls="mobileSidebarOffcanvas">
                <i class="bi bi-three-dots-vertical" style="color: var(--brand-purple);"></i>
            </button>
        </div>
    </div>
</nav>

<!-- Main Content Layout -->
<div class="container mt-4 pt-2">
    <div class="row">
        <!-- LEFT SIDEBAR -->
        <div class="col-lg-3 d-none d-lg-block">
             <div class="sticky-top" style="top: 80px; z-index: 10;">
                 <div class="left-sidebar-box mt-2">
                     <a href="{{ route('home') }}" class="ls-link">Home</a>
                     <a href="{{ route('memes.index', ['sort' => 'trending']) }}" class="ls-link {{ request('sort', 'trending') == 'trending' ? 'active' : '' }}">Trending</a>
                     <a href="{{ route('brands.public') }}" class="ls-link">Brands</a>
                     <a href="{{ route('upload-meme.create') }}" class="ls-link">Weekly Battle</a>
                     <a href="{{ route('upload-meme.create') }}" class="ls-link">Upload</a>
                     <a href="{{ route('memes.winners') }}" class="ls-link {{ request()->routeIs('memes.winners') ? 'active' : '' }}">Winners</a>
                     <a href="{{ route('meme-agent') }}" class="ls-link {{ request()->routeIs('meme-agent') ? 'active' : '' }}">Meme Agent</a>
                     <div style="border-top: 1px solid #f3f4f6; margin: 5px 0;"></div>
                     <div class="px-3 py-2 text-muted fw-bold small">TAGS</div>
                     <div class="px-3 pb-3 d-flex flex-column gap-2">
                         <span class="badge bg-light text-dark rounded-pill px-3 py-1" style="font-size: 0.8rem;">#RelatableVibes</span>
                         <span class="badge bg-light text-dark rounded-pill px-3 py-1" style="font-size: 0.8rem;">#DankHumor</span>
                         <span class="badge bg-light text-dark rounded-pill px-3 py-1" style="font-size: 0.8rem;">#Wholesome</span>
                     </div>
                     <div style="border-top: 1px solid #f3f4f6; margin: 5px 0;"></div>
                     <a href="#" class="ls-link" style="color: var(--brand-purple);" data-bs-toggle="modal" data-bs-target="#termsModal">Rules</a>
                      <a href="{{ route('how-it-works') }}" class="ls-link" style="color: var(--brand-purple);">How It Works</a>
                 </div>
             </div>
        </div>
        
        <!-- MAIN FEED -->
        <div class="col-lg-6">
            <div class="text-center fw-bold text-muted small mb-2 d-lg-none">MEME FEED</div>
            
            <!-- Weekly Battle Arena Widget (In Feed) -->
            <div class="arena-box text-center relative">
                 <h2 class="arena-title mb-1 text-start fw-bold" style="font-size: 1.4rem;">WEEKLY BATTLE ARENA</h2>
                 <div class="fw-bold text-start" style="font-size: 1.1rem;">Prize: $100</div>
                 <p class="mb-3 text-muted small text-start" style="font-size: 0.9rem;">Ends in: <span id="weekly-battle-timer" class="js-battle-timer" data-end-time="{{ $currentChallengeEndTime ?? $sidebarEndTime ?? '' }}">Loading...</span></p>
                 
                 <div class="row g-2 justify-content-center mb-3 weekly-battle-leaderboard">
                     @if(isset($leaderboard) && $leaderboard->count() > 0)
                         @foreach($leaderboard as $index => $meme)
                             <div class="col-4">
                                 <div class="post-card position-relative h-100 d-flex flex-column" style="height: 200px;">
                                     @php $rank = $index + 1; @endphp
                                     <div class="winner-badge rank-{{ $rank }}">
                                         #{{ $rank }}
                                     </div>

                                     <div class="flex-grow-1 d-flex align-items-center justify-content-center rounded bg-light" style="min-height: 100px;">
                                         @if($meme->image_path)
                                             <img src="{{ asset('storage/' . $meme->image_path) }}" class="img-fluid rounded w-100" style="height: 100px; object-fit: cover;" alt="Meme">
                                         @else
                                             <div class="p-2 text-center fw-bold text-muted" style="font-size: 0.8rem;">{{ $meme->title }}</div>
                                         @endif
                                     </div>

                                     <div class="mt-auto d-flex justify-content-center">
                                         <a href="{{ route('home', ['highlight' => $meme->id]) }}#meme-{{ $meme->id }}" class="btn btn-sm btn-outline-dark">View</a>
                                     </div>
                                 </div>
                             </div>
                         @endforeach
                         @for($i = $leaderboard->count(); $i < 3; $i++)
                             <div class="col-4">
                                 <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center fw-bold text-purple-500 fs-3" style="color: #a855f7;">#{{ $i + 1 }}</div>
                             </div>
                         @endfor
                     @else
                         <div class="col-4">
                             <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center fw-bold text-purple-500 fs-3" style="color: #a855f7;">#1</div>
                         </div>
                         <div class="col-4">
                             <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center fw-bold text-purple-500 fs-3" style="color: #a855f7;">#2</div>
                         </div>
                         <div class="col-4">
                             <div class="bg-light rounded p-3 d-flex align-items-center justify-content-center fw-bold text-purple-500 fs-3" style="color: #a855f7;">#3</div>
                         </div>
                     @endif
                 </div>
                 
                 <a href="{{ route('upload-meme.create') }}" class="btn-hero-orange d-block w-100 py-2">Enter Battle</a>
                 <div class="mt-2 text-muted small">
                     <a href="{{ route('memes.winners') }}" class="text-decoration-none text-muted">Leaderboard</a> | <a href="{{ route('how-it-works') }}" class="text-decoration-none text-muted">How It Works</a>
                 </div>
            </div>
            
            <!-- Trending Header -->
            <div class="d-flex justify-content-between align-items-center mb-3 px-1">
                 <div class="fw-bold" style="color: var(--brand-purple); font-size: 1.2rem;">MEME FEED</div>
                 <div class="d-flex gap-2 small text-muted">
                     <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="all">All</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="fresh">Fresh</button>
                     <button type="button" class="btn btn-sm btn-outline-secondary" data-filter="battle">Battle Entries</button>
                 </div>
            </div>
        
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
                
                <!-- User Info Profile Section -->
                <div class="d-flex align-items-center mb-3">
                    <img src="{{ $meme->user->profile_photo_url }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $meme->user->name ?? 'Avatar' }}">
                    <div class="flex-grow-1 text-start">
                        <span class="fw-bold small d-block text-dark">{{ $meme->user->name ?? 'Unknown' }}</span>
                    </div>
                </div>

                <div class="row">
                     <div class="col-12 text-center">
                         @if($meme->title && $meme->title !== 'Untitled')
                             <h6 class="meme-title fw-bold mb-2 text-start" style="white-space: pre-line;">{{ $meme->title }}</h6>
                         @endif
                         
                         @if($meme->image_path)
                             <img src="{{ asset('storage/'.$meme->image_path) }}"
                                  class="img-fluid rounded border
                                         @if($meme->template == 'portrait') template-portrait
                                         @elseif($meme->template == 'landscape') template-landscape
                                         @else template-square
                                         @endif" style="width: 100%;">
                         @endif
                     </div>
                </div>

                <!-- Stats / Actions Layout -->
                <div class="mt-3 d-flex align-items-center gap-3 border-top pt-2">
                     <!-- Smile/Reactions -->
                     <button class="react-toggle-btn btn btn-light btn-sm d-flex align-items-center gap-1 rounded-pill" type="button" data-meme-id="{{ $meme->id }}">
                         <span class="reaction-emoji text-warning shadow-sm rounded-circle bg-white" style="font-size: 1.1rem;">{{ $meme->userEmoji ?? '😆' }}</span>
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

                       @if($meme->is_contest)
                            <span class="badge bg-warning text-dark border border-warning rounded-pill ms-2" title="Paid Meme" style="font-size: 0.9rem;">⭐</span>
                       @endif
                      
                       <!-- Pay for Meme Button -->
                       @if(auth()->check() && $meme->user_id == auth()->id() && !$meme->is_contest)
                           <a href="{{ route('memes.pay', $meme->id) }}" class="btn btn-sm text-white fw-bold ms-2" style="background-color: #28a745; font-size: 0.75rem; border-radius: 20px; padding: 5px 12px;">
                               💰 Enter battle
                           </a>
                       @endif
                </div>

                <!-- Hidden sections (Comment/Emoji/Share) -->
                


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

                <!-- Emoji Section -->
                <div id="emoji-section-{{ $meme->id }}" style="display:none;" class="mt-2 text-start">
                    <emoji-picker id="emoji-picker-{{ $meme->id }}"></emoji-picker>
                </div>

                <!-- Comment Section -->
                <div id="comment-section-{{ $meme->id }}" style="display:none;" class="mt-2">
                    <form class="comment-form d-flex mt-2" data-id="{{ $meme->id }}">
                        @csrf
                        <input type="text" name="content" class="form-control form-control-sm me-1" placeholder="Add a comment">
                        <button type="submit" class="btn btn-sm" style="background-color: var(--brand-purple); color: white;">Comment</button>
                    </form>

                    <ul class="comment-list mt-2 list-unstyled">
                        @foreach($meme->comments->whereNull('parent_id') as $comment)
                            @include('partials._comment_item', ['comment' => $comment, 'meme' => $meme])
                        @endforeach
                    </ul>
                </div>

            </div>
            @endforeach
            

        </div>

        <div class="col-lg-3 d-none d-lg-block">
            @include('partials._leaderboard-widget')
        </div>
    </div>
</div>

<!-- Mobile Sidebar Offcanvas -->
<div class="offcanvas offcanvas-end d-lg-none" tabindex="-1" id="mobileSidebarOffcanvas" aria-labelledby="mobileSidebarOffcanvasLabel" style="width: 320px;">
  <div class="offcanvas-header border-bottom">
    <h5 class="offcanvas-title fw-bold text-uppercase" id="mobileSidebarOffcanvasLabel" style="color: var(--brand-purple); font-size: 1rem;">Actions & Leaderboard</h5>
    <button type="button" class="btn-close" data-bs-dismiss="offcanvas" aria-label="Close"></button>
  </div>
  <div class="offcanvas-body" style="padding: 15px; background-color: var(--brand-bg);">
      @include('partials._leaderboard-widget')
  </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const loggedIn = {{ auth()->check() ? 'true' : 'false' }};

    // Toggle Emoji Section
    $(document).on('click', '.react-toggle-btn', function() {
        const memeId = $(this).data('meme-id');
        if (!loggedIn) { window.location = '{{ route("login") }}'; return; }
        const section = $('#emoji-section-' + memeId);
        section.toggle();
    });

    // Toggle Comment Section
    $(document).on('click', '.comment-toggle-btn', function() {
        const memeId = $(this).data('meme-id');
        if (!loggedIn) { window.location = '{{ route("login") }}'; return; }
        const section = $('#comment-section-' + memeId);
        section.toggle();
    });

    // Emoji Picker click
    document.querySelectorAll('emoji-picker').forEach(picker => {
        picker.addEventListener('emoji-click', event => {
            const emoji = event.detail.unicode;
            const postCard = picker.closest('.post-card');
            const memeId = postCard.dataset.id;
            if (!loggedIn) { window.location = '{{ route("login") }}'; return; }

            const reactBtn = postCard.querySelector('.react-toggle-btn');
            const emojiSpan = reactBtn.querySelector('.reaction-emoji');
            emojiSpan.textContent = emoji;

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

    // Comment form submit
    $(document).on('submit', '.comment-form', function(e) {
        e.preventDefault();
        let memeId = $(this).data('id');
        let input = $(this).find('input[name="content"]');
        let form = $(this);
        if(input.val().trim() === '') return;

        $.ajax({
            url: `/api/meme/${memeId}/comments`,
            method: 'POST',
            contentType: 'application/json',
            data: JSON.stringify({ body: input.val() }),
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') },
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

                    // Update comment count
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
                    newReplyHtml += '<div class="comment-content bg-white p-2 border rounded mb-1" tabindex="0">';
                    newReplyHtml += '<strong>' + data.comment.user.name + '</strong>: ' + data.comment.body;
                    newReplyHtml += '<div class="comment-actions mt-1">';
                    newReplyHtml += '<button class="comment-action-btn reply-btn small text-primary border-0 bg-transparent p-0 me-2" data-comment-id="' + data.comment.id + '">Reply</button>';
                    newReplyHtml += '<button class="comment-action-btn copy-btn small text-secondary border-0 bg-transparent p-0" data-comment-body="' + data.comment.body + '" title="Copy reply">📋</button>';

                    // Add delete button if it's the current user's reply
                    if (data.comment.user.id == {{ auth()->id() ?? 'null' }}) {
                        newReplyHtml += '<button class="comment-action-btn delete-btn small text-danger border-0 bg-transparent p-0" data-comment-id="' + data.comment.id + '" title="Delete reply" style="display: none;">🗑️</button>';
                    }

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
                    form.closest('.reply-form-container').hide();

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
    
    // WhatsApp share functionality
    $(document).on('click', '.whatsapp-share-btn', function(e) {
        e.preventDefault();
        const memeId = $(this).data('meme-id');
        const memeUrl = `{{ route("memes.show", ":id") }}`.replace(':id', memeId);
        const text = `Check out this cool meme! ${memeUrl}`;
        const whatsappUrl = `https://wa.me/?text=${encodeURIComponent(text)}`;

        $.ajax({
            url: `/api/meme/${memeId}/share`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        window.open(whatsappUrl, '_blank');
    });

    // Facebook share functionality
    $(document).on('click', '.facebook-share-btn', function(e) {
        e.preventDefault();
        const memeId = $(this).data('meme-id');
        const memeUrl = `{{ route("memes.show", ":id") }}`.replace(':id', memeId);
        const text = `Check out this cool meme!`;
        const facebookUrl = `https://www.facebook.com/sharer/sharer.php?u=${encodeURIComponent(memeUrl)}&quote=${encodeURIComponent(text)}`;

        $.ajax({
            url: `/api/meme/${memeId}/share`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        window.open(facebookUrl, '_blank');
    });

    // Copy link functionality
    $(document).on('click', '.copy-link-btn', function(e) {
        e.preventDefault();
        const memeId = $(this).data('meme-id');
        const memeUrl = `{{ route("memes.show", ":id") }}`.replace(':id', memeId);

        $.ajax({
            url: `/api/meme/${memeId}/share`,
            method: 'POST',
            headers: { 'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content') }
        });

        if (navigator.clipboard && window.isSecureContext) {
            navigator.clipboard.writeText(memeUrl).then(function() {
                const originalText = $(e.target).html();
                $(e.target).html('✓ Copied!');
                setTimeout(() => { $(e.target).html(originalText); }, 2000);
            }).catch(function(err) {
                fallbackCopyTextToClipboard(memeUrl);
            });
        } else {
            fallbackCopyTextToClipboard(memeUrl);
        }
    });

    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            alert('Link copied!');
        } catch (err) {
            alert('Failed to copy link.');
        } finally {
            document.body.removeChild(textArea);
        }
    }
});

$(document).ready(function() {
    $(document).on('click', '.menu-button', function(e) {
        e.preventDefault();
        e.stopPropagation();
        var targetId = $(this).data('target');
        if (!targetId) targetId = $(this).attr('data-bs-target');
        var $target = $(targetId);
        if ($target.length) {
             $target.attr('data-user-toggled', 'true');
             if ($target.hasClass('show')) {
                 $target.collapse('hide');
             } else {
                 $target.collapse('show');
             }
             setTimeout(() => { $target.removeAttr('data-user-toggled'); }, 100);
        }
    });

    $('.collapse').on('hide.bs.collapse', function(e) {
        if (this.id && this.id.startsWith('menu-')) {
            if (!$(this).attr('data-user-toggled')) {
                e.preventDefault();
            }
        }
    });
});

// Filtering functionality
document.addEventListener('DOMContentLoaded', function() {
    const memeCards = document.querySelectorAll('.meme.relative');
    memeCards.forEach(card => {
        card.classList.add('meme-card');
        const isContest = card.dataset.isContest;
        if (isContest === '1') card.classList.add('battle-entry');
        else card.classList.add('regular-entry');

        const createdAt = card.dataset.createdAt;
        if (createdAt) {
            const uploadDate = new Date(createdAt);
            const now = new Date();
            const hoursDiff = (now - uploadDate) / (1000 * 60 * 60);
            if (hoursDiff < 24) card.classList.add('fresh-meme');
        }
    });

    const filterButtons = document.querySelectorAll('[data-filter]');
    filterButtons.forEach(button => {
        button.addEventListener('click', function() {
            const filter = this.dataset.filter;
            filterButtons.forEach(btn => btn.classList.remove('btn-secondary', 'text-white'));
            this.classList.add('btn-secondary', 'text-white');
            memeCards.forEach(card => {
                card.style.display = 'block';
                if (filter === 'fresh' && !card.classList.contains('fresh-meme')) card.style.display = 'none';
                if (filter === 'battle' && !card.classList.contains('battle-entry')) card.style.display = 'none';
            });
        });
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

    // Copy comment functionality
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

    // Fallback function for copying text
    function fallbackCopyTextToClipboard(text) {
        const textArea = document.createElement("textarea");
        textArea.value = text;
        document.body.appendChild(textArea);
        textArea.focus();
        textArea.select();
        try {
            document.execCommand('copy');
            // Show temporary success feedback
            const originalText = $('.copy-btn:visible').html();
            $('.copy-btn:visible').html('✓ Copied!');

            setTimeout(() => {
                $('.copy-btn:visible').html(originalText);
            }, 2000);
        } catch (err) {
            console.error('Fallback copy failed: ', err);
        } finally {
            document.body.removeChild(textArea);
        }
    }
});


</script>

@include('partials._terms-modal')
@include('partials._battle-timer-script')
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
<script src="{{ asset('js/memes-interactions.js?v=' . time()) }}"></script>

@if($highlightMemeId)
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const highlightedMeme = document.getElementById('meme-{{ $highlightMemeId }}');
        if (highlightedMeme) {
            // Scroll to the meme after a short delay
            setTimeout(() => {
                highlightedMeme.scrollIntoView({ 
                    behavior: 'smooth', 
                    block: 'center' 
                });
                
                // Add pulse animation
                highlightedMeme.style.animation = 'memePulse 2s ease-in-out infinite';
                highlightedMeme.style.transition = 'all 0.3s ease';
            }, 500);
        }
    });
</script>

<style>
    @keyframes memePulse {
        0%, 100% {
            box-shadow: 0 4px 20px rgba(0,0,0,0.07);
            transform: translateY(0);
        }
        50% {
            box-shadow: 0 0 0 4px #f59e0b, 0 20px 40px rgba(0,0,0,0.12);
            transform: translateY(-6px);
        }
    }
</style>
@endif
</body>
</html>