<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>Winners - {{ config('app.name', 'OneDollarMeme') }}</title>

    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    
    <!-- Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

    <style>
        :root {
            --brand-purple: #3e1e86;
            --brand-yellow: #fbbf24;
            --brand-orange: #f97316;
            --bg-body: #f3f4f6;
        }

        body {
            font-family: 'Inter', sans-serif;
            background-color: var(--bg-body);
            color: #333;
            min-height: 100vh;
        }



        /* Weekly Challenge Banner */
        .challenge-banner {
            background: linear-gradient(90deg, #3b1e84 0%, #6e46c7 100%);
            border-radius: 16px;
            padding: 1rem 2rem; /* Reduced padding further */
            color: white;
            position: relative;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
            margin-bottom: 2rem;
            display: flex;
            align-items: center; /* Center content vertically if height is fixed, or just compact it */
        }
        .challenge-banner::before {
             content: '';
             position: absolute;
             bottom: 0;
             left: 0;
             right: 0;
             height: 50%;
             background: url('https://upload.wikimedia.org/wikipedia/commons/thumb/3/3e/Dubai_Skyline_from_the_Sea.jpg/640px-Dubai_Skyline_from_the_Sea.jpg') bottom center no-repeat;
             background-size: cover;
             opacity: 0.2;
             filter: grayscale(100%);
             z-index: 0;
        }
        .banner-content {
            position: relative;
            z-index: 10;
            width: 100%;
        }
        .badge-live {
            background-color: #f97316;
            color: white;
            font-weight: 700;
            font-size: 0.75rem;
            padding: 4px 10px;
            border-radius: 4px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            display: inline-block;
            margin-bottom: 1rem; /* Increased spacing */
        }
        .challenge-title {
            font-size: 2rem; 
            font-weight: 700;
            margin-bottom: 1rem; /* Increased spacing */
            line-height: 1.1;
        }
        .challenge-desc {
            font-size: 1rem; 
            color: rgba(255,255,255,0.9);
            margin-bottom: 1.5rem; /* Increased spacing */
        }
        .stat-item {
            display: inline-flex;
            align-items: center;
            margin-right: 1.5rem;
            font-size: 0.9rem;
            font-weight: 600;
        }
        .stat-item i {
            margin-right: 6px;
            color: #fbbf24;
            font-size: 1.1rem;
        }

        .btn-enter-challenge {
            background-color: #4f46e5; /* Indigo */
            color: white;
            font-weight: 600;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            transition: background 0.2s;
            font-size: 0.9rem;
        }
        .btn-enter-challenge:hover {
            background-color: #4338ca;
            color: white;
        }
        .btn-upload-free {
            background-color: white;
            color: #333;
            font-weight: 600;
            border: none;
            padding: 8px 16px;
            border-radius: 8px;
            text-decoration: none;
            margin-left: 10px;
             transition: background 0.2s;
             font-size: 0.9rem;
        }
         .btn-upload-free:hover {
            background-color: #f3f4f6;
            color: #333;
        }

        /* Filter Tabs */
        .filter-tabs {
            background: white;
            border-radius: 8px;
            padding: 5px;
            display: inline-flex;
            box-shadow: 0 1px 2px rgba(0,0,0,0.05);
            margin-right: 15px;
        }
        .filter-tab {
            padding: 8px 16px;
            border-radius: 6px;
            font-weight: 600;
            font-size: 0.9rem;
            color: #6b7280;
            text-decoration: none;
            transition: all 0.2s;
        }
        .filter-tab.active {
            background-color: #4f46e5;
            color: white;
        }

        /* Contestant Card */
        .contestant-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
            transition: transform 0.2s;
            margin-bottom: 1rem;
        }
        .contestant-card:hover {
            transform: translateY(-5px);
        }
        .card-header-custom {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .user-info {
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
        }
        .username {
            font-weight: 700;
            font-size: 0.9rem;
            color: #111827;
        }
        .verified-badge {
            color: #22c55e; /* Green check */
            font-size: 0.9rem;
        }
        .entered-badge {
            background: #f3f4f6;
            color: #6b7280;
            font-size: 0.75rem;
            padding: 2px 8px;
            border-radius: 12px;
            font-weight: 500;
        }
        .entered-badge i {
             color: #22c55e;
             font-size: 0.7rem;
             margin-left: 2px;
        }
        
        .card-image-container {
            width: 100%;
            height: 250px;
            background-color: #f9fafb;
            display: flex;
            align-items: center;
            justify-content: center;
            overflow: hidden;
        }
        .card-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .card-stats {
            padding: 12px 16px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            color: #6b7280;
            font-size: 0.85rem;
            font-weight: 500;
            border-top: 1px solid #f3f4f6;
        }
        .stat-group {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        .stat-icon {
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .score-val {
            color: #f97316;
            font-weight: 700;
        }
        .score-val i {
             color: #f97316;
        }

        /* Right Sidebar Layout */
        .sidebar-right {
             display: flex;
             flex-direction: column;
             gap: 1.5rem;
        }
        
        /* Winner Box */
        .winner-box, .winner-box-large {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 15px -3px rgba(0, 0, 0, 0.1);
        }
        /* Specific overrides for the large winner box if needed, currently mainly targets children */
        .winner-box-large .winner-image-container {
             height: 300px !important; /* Forces it taller than the challenge banner likely is */
        }
        .winner-box-large .winner-header {
             padding: 15px !important;
        }
        .winner-box-large .winner-info-overlay {
             padding: 12px !important;
        }
        .winner-box-large .winner-details {
             padding: 20px !important;
        }
        /* End specific overrides */
        .winner-header {
            background: #1f2937; /* Dark header */
            color: white;
            padding: 6px 10px; /* Further reduced padding */
            text-align: center;
            font-weight: 700;
            font-size: 1rem;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .winner-header i {
            color: #fbbf24;
        }
        .winner-image-container {
            position: relative;
            height: 140px; /* Significantly reduced height from 180px */
        }
        .winner-image {
            width: 100%;
            height: 100%;
            object-fit: cover;
        }
        .winner-info-overlay {
            background: #1f2937;
            color: white;
            padding: 4px 8px; /* Further reduced padding */
            text-align: center;
            font-weight: 700;
            font-size: 0.95rem;
        }
        .winner-details {
            padding: 10px; /* Reduced padding */
        }
        .winner-stats {
            display: flex;
            justify-content: space-between;
            align-items: center;
            font-size: 0.85rem;
            color: #4b5563;
            margin-bottom: 8px; /* Reduced margin */
        }
        .prize-value {
             font-weight: 700;
             color: #dc2626; /* Red for prize text in ref */
        }
        .winner-actions .btn {
            width: 48%;
            font-weight: 600;
            font-size: 0.85rem;
            padding: 6px 12px;
        }
        .btn-view-winner {
            background-color: #5b46e8;
            color: white;
            border: none;
        }
        .btn-view-winner:hover {
             background-color: #4338ca;
             color: white;
        }
        .btn-share {
            background-color: white;
            border: 1px solid #e5e7eb;
            color: #374151;
        }
         .btn-share:hover {
            background-color: #f9fafb;
        }

        /* Past Winners List */
        .past-winners-box {
            background: white;
            border-radius: 16px;
            padding: 20px;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1);
        }
        .past-winners-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 15px;
            font-weight: 700;
            font-size: 1rem;
        }
        .dropdown-toggle-custom {
            border: none;
            background: none;
            font-weight: 600;
            color: #6b7280;
            font-size: 0.9rem;
        }
        .past-winner-item {
            display: flex;
            align-items: center;
            padding: 12px 0;
            border-bottom: 1px solid #f3f4f6;
        }
        .past-winner-item:last-child {
            border-bottom: none;
        }
        .past-avatar {
            width: 40px;
            height: 40px;
            border-radius: 8px; /* Slightly square in ref list? Ref shows square-ish/rounded */
            border-radius: 50%; /* Let's stick to circle for consistency unless ref is strict square */
            object-fit: cover;
            margin-right: 12px;
        }
        .past-info {
            flex-grow: 1;
        }
        .past-week {
            font-weight: 700;
            font-size: 0.9rem;
            color: #111827;
        }
        .past-prize {
            font-size: 0.8rem;
            color: #6b7280;
        }
        .past-stats {
            text-align: right;
            font-size: 0.85rem;
            font-weight: 600;
            color: #374151;
        }
        .past-stats i {
            color: #ef4444; /* Heart red */
            margin-right: 2px;
        }
        .view-all-link {
            display: block;
            text-align: center;
            margin-top: 15px;
            color: #5b46e8;
            font-weight: 600;
            text-decoration: none;
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    @include('partials._toast')

@include('partials._main-nav')

    <!-- Main Content -->
    <div class="container-fluid px-4 mt-4" style="max-width: 1400px;">
        
        <div class="row g-4">
            <!-- Left Column: Weekly Challenge + Top 3 Memes -->
            <div class="col-lg-8">
                <!-- Challenge Banner -->
                <div class="challenge-banner mb-4"> 
                     <div class="banner-content">
                         <div class="badge-live">LIVE • WEEK #{{ $currentChallengeId }}</div>
                         <h1 class="challenge-title">Weekly Challenge</h1>
                         <p class="challenge-desc">Submit a meme for $1 and compete for real rewards.</p>
                         
                         <div class="d-flex align-items-center flex-wrap gap-3">
                             
                             <div class="ms-auto">
                                 <a href="{{ route('upload-meme.create') }}" class="btn-enter-challenge">Enter Battle </a>
                                 <button class="btn-upload-free">Upload Free Meme</button>
                             </div>
                         </div>
                     </div>
                </div>

                <!-- Meme Grid - Limited to 3 items -->
                <div class="row g-4">
                    @forelse($leaderboard->take(3) as $meme)
                    <div class="col-md-4">
                        <div class="contestant-card">
                            <div class="card-header-custom">
                                <div class="d-flex align-items-center">
                                    <img src="{{ $meme->user->profile_photo_url }}" class="rounded-circle me-3" style="width: 32px; height: 32px; object-fit: cover;" alt="{{ $meme->user->name ?? 'Avatar' }}">
                                    <div class="flex-grow-1 text-start">
                                        <span class="fw-bold small d-block text-dark">{{ $meme->user->name ?? 'Unknown' }}</span>
                                    </div>
                                    <i class="bi bi-check-circle-fill verified-badge ms-2"></i>
                                </div>
                            </div>
                            
                            <div class="card-image-container">
                                @if($meme->image_path)
                                    <img src="{{ asset('storage/' . $meme->image_path) }}" class="card-image" alt="Meme">
                                @else
                                    <div class="p-3 text-center fw-bold text-muted">{{ $meme->title }}</div>
                                @endif
                            </div>@if($meme->image_path && trim($meme->title ?? '') !== '' && strtolower($meme->title) !== 'untitled')<div class="px-3 py-2 fw-bold text-dark border-top small" style="background: #fafafa; border-bottom: 1px solid #f3f4f6; white-space: pre-line;">{{ $meme->title }}</div>@endif<div class="card-stats">
                                <a href="{{ route('home', ['highlight' => $meme->id]) }}#meme-{{ $meme->id }}" class="btn btn-view-winner" style="width: 50%; font-size: 0.85rem; padding: 6px 12px;">View</a>
                                <div class="score-val">{{ $meme->score }} Score</div>
                            </div>
                        </div>
                    </div>
                    @empty
                        <div class="col-12 py-5 text-center text-muted">No active entries yet. Be the first!</div>
                    @endforelse
                </div>
            </div>

            <!-- Right Column: Winner Box + Past Winners -->
            <div class="col-lg-4 sidebar-right">
                <!-- Winner Box (Large) -->
                @if($pastWinners->isNotEmpty())
                    @php $lastWinner = $pastWinners->first(); @endphp
                    <div class="winner-box-large mb-4"> <!-- Removed h-100 to allow natural height spacing -->
                        <div class="winner-header">
                            <i class="bi bi-trophy-fill"></i> Winner – Week #{{ $lastWinner->challenge_id }}
                        </div>
                        <div class="winner-image-container">
                             @if($lastWinner->winnerMeme && $lastWinner->winnerMeme->image_path)
                                <img src="{{ asset('storage/' . $lastWinner->winnerMeme->image_path) }}" class="winner-image" alt="Winner">
                             @else
                                <div class="w-100 h-100 d-flex align-items-center justify-content-center text-dark px-4 text-center fw-bold" style="background: #f8fafc; font-size: 1.1rem; line-height: 1.4;">
                                    {{ $lastWinner->winnerMeme->title ?? 'Untitled Meme' }}
                                </div>
                             @endif
                        </div>
                        <div class="winner-info-overlay">
                             Just Won an iPhone 13 Pro!
                        </div>
                        
                        <div class="winner-details">
                            
                            <div class="d-flex justify-content-between winner-actions">
                                <a href="{{ route('home', ['highlight' => $lastWinner->winner_meme_id]) }}#meme-{{ $lastWinner->winner_meme_id }}" class="btn btn-view-winner">View Winner</a>
                                <button class="btn btn-share" onclick="showMemeShareModal(event, {{ $lastWinner->winner_meme_id }})">Share</button>
                            </div>
                        </div>
                    </div>
                @else
                     <div class="card border-0 shadow-sm d-flex align-items-center justify-content-center p-5 mb-4">
                        <div class="text-center text-muted">
                            <i class="bi bi-trophy fs-1 mb-3 d-block"></i>
                            Waiting for the first winner!
                        </div>
                    </div>
                @endif

                <!-- Past Winners List (Below Winner Box) -->
                <div class="past-winners-box">
                    <div class="past-winners-header">
                        <span>Past Winners</span>
                    </div>

                    <div class="past-winners-list">
                        @foreach($pastWinners->take(4) as $past)
                            @if($past->winnerMeme)
                            <div class="past-winner-item">
                                <img src="{{ $past->winnerMeme->user->profile_photo_url }}" class="past-avatar" alt="Avatar">
                                <div class="past-info">
                                    <div class="past-week">Week #{{ $past->challenge_id }} • {{ '@' . $past->winnerMeme->user->name }}</div>
                                </div>
                            </div>
                            @endif
                        @endforeach
                    </div>
                    
                    <a href="#" class="view-all-link">View all winners →</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Scripts -->
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
    </script>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials._battle-timer-script')
    @include('partials._terms-modal')
    <script src="{{ asset('js/memes-interactions.js?v=' . time()) }}"></script>
</body>
</html>
