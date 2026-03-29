<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Panel - OneDollarMeme</title>
    <!-- Favicon -->
    <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <style>
        body { background-color: #f8f9fa; font-family: 'Arial', sans-serif; }
        .sidebar { min-height: 100vh; background: #ffffff; color: #212529; border-right: 1px solid #dee2e6; }
        .sidebar .nav-link { color: #495057; padding: 10px 20px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #000; background: #e9ecef; }
        .sidebar .brand { font-size: 1.25rem; font-weight: bold; padding: 20px; display: block; color: #212529; text-decoration: none; }
        .card-stat { border: none; border-radius: 10px; box-shadow: 0 4px 6px rgba(0,0,0,0.1); transition: transform 0.2s; }
        .card-stat:hover { transform: translateY(-5px); }
        .table-responsive { background: white; border-radius: 10px; padding: 15px; box-shadow: 0 4px 6px rgba(0,0,0,0.05); }
    </style>
</head>
<body>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3" style="width: 250px;">
        <a href="{{ route('home') }}" class="brand mb-3 mb-md-0 me-md-auto text-decoration-none">
            <i class="bi bi-shield-lock me-2"></i> ODM Admin
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>
            
            <li class="mt-4 mb-2 ps-3 small text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Moderation
            </li>
            <li>
                <a href="{{ route('admin.moderation') }}" class="nav-link {{ request()->routeIs('admin.moderation') ? 'active' : '' }}">
                    <i class="bi bi-exclamation-diamond me-2"></i> Content Queue
                </a>
            </li>
            <li>
                <a href="{{ route('admin.reports') }}" class="nav-link {{ request()->routeIs('admin.reports') ? 'active' : '' }}">
                    <i class="bi bi-flag me-2"></i> Reports
                </a>
            </li>

            <li class="mt-4 mb-2 ps-3 small text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Competitions
            </li>
            <li>
                <a href="{{ route('admin.challenge') }}" class="nav-link {{ request()->routeIs('admin.challenge') ? 'active' : '' }}">
                    <i class="bi bi-trophy me-2"></i> Weekly Challenge
                </a>
            </li>
            <li>
                <a href="{{ route('admin.payouts') }}" class="nav-link {{ request()->routeIs('admin.payouts') ? 'active' : '' }}">
                    <i class="bi bi-cash-stack me-2"></i> Payouts
                </a>
            </li>

            <li class="mt-4 mb-2 ps-3 small text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Brands
            </li>
            <li>
                <a href="{{ route('admin.brands') }}" class="nav-link {{ request()->routeIs('admin.brands') ? 'active' : '' }}">
                    <i class="bi bi-briefcase me-2"></i> Brand Requests
                </a>
            </li>
            <li>
                <a href="{{ route('admin.approved-brands') }}" class="nav-link {{ request()->routeIs('admin.approved-brands') ? 'active' : '' }}">
                    <i class="bi bi-check-circle me-2"></i> Active Campaigns
                </a>
            </li>
            <li>
                <a href="{{ route('admin.brand-memes') }}" class="nav-link {{ request()->routeIs('admin.brand-memes') ? 'active' : '' }}">
                    <i class="bi bi-images me-2"></i> Brand Memes
                </a>
            </li>

            <li class="mt-4 mb-2 ps-3 small text-muted text-uppercase fw-bold" style="font-size: 0.75rem; letter-spacing: 0.5px;">
                Security
            </li>
            <li>
                <a href="{{ route('admin.ip-tracking') }}" class="nav-link {{ request()->routeIs('admin.ip-tracking') ? 'active' : '' }}">
                    <i class="bi bi-geo-alt me-2"></i> IP Tracking
                </a>
            </li>
            <li>
                <a href="{{ route('admin.engagement-audit') }}" class="nav-link {{ request()->routeIs('admin.engagement-audit') ? 'active' : '' }}">
                    <i class="bi bi-shield-check me-2"></i> Engagement Audit
                </a>
            </li>
        </ul>
        <hr>
        <div class="dropdown">
            <a href="#" class="d-flex align-items-center text-dark text-decoration-none dropdown-toggle" id="dropdownUser1" data-bs-toggle="dropdown" aria-expanded="false">
                <img src="https://ui-avatars.com/api/?name={{ Auth::user()->name }}&background=random" alt="" width="32" height="32" class="rounded-circle me-2">
                <strong>{{ Auth::user()->name }}</strong>
            </a>
            <ul class="dropdown-menu text-small shadow" aria-labelledby="dropdownUser1">
                <li><a class="dropdown-item" href="{{ route('home') }}">Back to Site</a></li>
                <li><hr class="dropdown-divider"></li>
                <li>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="dropdown-item">Sign out</button>
                    </form>
                </li>
            </ul>
        </div>
    </div>

    <!-- Main Content -->
    <div class="flex-grow-1 p-4" style="max-height: 100vh; overflow-y: auto;">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                {{ session('success') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif
        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                {{ session('error') }}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        @endif

        @yield('content')
    </div>
</div>

    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    @include('partials._toast')
</body>
</html>
