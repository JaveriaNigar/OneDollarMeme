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
        :root {
            --sidebar-width: 250px;
        }
        
        body { 
            background-color: #f8f9fa; 
            font-family: 'Arial', sans-serif;
            overflow-x: hidden;
        }
        
        /* Sidebar Styles */
        .sidebar {
            height: 100vh;
            max-height: 100vh;
            background: #ffffff;
            color: #212529;
            border-right: 1px solid #dee2e6;
            width: var(--sidebar-width);
            transition: transform 0.3s ease-in-out;
            position: fixed;
            top: 0;
            left: 0;
            z-index: 1000;
            overflow-y: auto;
            overflow-x: hidden;
            display: flex;
            flex-direction: column;
        }
        
        .sidebar ul {
            overflow-y: auto;
            flex-grow: 1;
            margin: 0;
            padding: 0;
        }
        
        /* Firefox scrollbar support */
        .sidebar {
            scrollbar-width: thin;
            scrollbar-color: #888 #f1f1f1;
        }
        
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        
        .sidebar::-webkit-scrollbar-track {
            background: #f1f1f1;
        }
        
        .sidebar::-webkit-scrollbar-thumb {
            background: #888;
            border-radius: 3px;
        }
        
        .sidebar::-webkit-scrollbar-thumb:hover {
            background: #555;
        }
        
        .sidebar .nav-link { 
            color: #495057; 
            padding: 12px 20px; 
            border-radius: 8px;
            margin-bottom: 4px;
            transition: all 0.2s;
        }
        
        .sidebar .nav-link:hover, 
        .sidebar .nav-link.active { 
            color: #000; 
            background: #e9ecef; 
        }
        
        .sidebar .nav-link i {
            font-size: 1.1rem;
        }
        
        .sidebar .brand { 
            font-size: 1.25rem; 
            font-weight: bold; 
            padding: 20px; 
            display: flex;
            align-items: center;
            color: #212529; 
            text-decoration: none; 
        }
        
        /* Main Content */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 20px;
            transition: margin-left 0.3s ease-in-out;
            min-height: 100vh;
        }
        
        /* Card Styles */
        .card-stat { 
            border: none; 
            border-radius: 12px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.1); 
            transition: transform 0.2s, box-shadow 0.2s; 
        }
        
        .card-stat:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }
        
        .table-responsive { 
            background: white; 
            border-radius: 12px; 
            padding: 15px; 
            box-shadow: 0 4px 12px rgba(0,0,0,0.05);
        }
        
        /* Mobile Toggle */
        .sidebar-toggle {
            display: none;
            position: fixed;
            top: 15px;
            left: 15px;
            z-index: 1001;
            background: #0d6efd;
            color: white;
            border: none;
            border-radius: 8px;
            padding: 10px 15px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.2);
        }
        
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0,0,0,0.5);
            z-index: 999;
        }
        
        /* Responsive Styles */
        @media (max-width: 991.98px) {
            .sidebar {
                transform: translateX(-100%);
                max-height: 100vh;
                overflow-y: auto;
            }
            
            .sidebar.show {
                transform: translateX(0);
            }
            
            .main-content {
                margin-left: 0;
                padding-top: 70px;
            }
            
            .sidebar-toggle {
                display: block;
            }
            
            .sidebar-overlay.show {
                display: block;
            }
            
            /* Adjust card stats for mobile */
            .card-stat {
                margin-bottom: 20px;
            }
            
            /* Make tables more responsive */
            .table-responsive {
                padding: 10px;
                overflow-x: auto;
            }
            
            .table-responsive table {
                min-width: 600px;
            }
        }
        
        @media (max-width: 575.98px) {
            .sidebar .brand {
                font-size: 1.1rem;
                padding: 15px;
            }
            
            .sidebar .nav-link {
                padding: 10px 15px;
                font-size: 0.95rem;
            }
            
            .main-content {
                padding: 15px 10px;
                padding-top: 70px;
            }
            
            h1, h2, h3 {
                font-size: 1.25rem;
            }
            
            .card-body {
                padding: 15px;
            }
        }
        
        /* Section headers in sidebar */
        .sidebar-section-header {
            font-size: 0.7rem;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            color: #6c757d;
            padding: 15px 20px 8px;
            font-weight: 700;
        }
        
        /* Dropdown user menu */
        .dropdown-menu {
            border-radius: 8px;
            border: none;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }
    </style>
</head>
<body>

<!-- Mobile Sidebar Toggle -->
<button class="sidebar-toggle" type="button" id="sidebarToggle">
    <i class="bi bi-list fs-4"></i>
</button>

<!-- Sidebar Overlay -->
<div class="sidebar-overlay" id="sidebarOverlay"></div>

<div class="d-flex">
    <!-- Sidebar -->
    <div class="sidebar d-flex flex-column flex-shrink-0 p-3" id="sidebar">
        <a href="{{ route('home') }}" class="brand mb-3 mb-md-0 me-md-auto text-decoration-none">
            <i class="bi bi-shield-lock me-2"></i> <span>ODM Admin</span>
        </a>
        <hr>
        <ul class="nav nav-pills flex-column mb-auto">
            <li class="nav-item">
                <a href="{{ route('admin.dashboard') }}" class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}">
                    <i class="bi bi-speedometer2 me-2"></i> Dashboard
                </a>
            </li>

            <li class="sidebar-section-header">
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

            <li class="sidebar-section-header">
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

            <li class="sidebar-section-header">
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

            <li class="sidebar-section-header">
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
                <strong class="d-none d-lg-inline">{{ Auth::user()->name }}</strong>
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
    <div class="main-content" id="mainContent">
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
    <script>
        // Sidebar toggle functionality for mobile
        document.addEventListener('DOMContentLoaded', function() {
            const sidebar = document.getElementById('sidebar');
            const sidebarToggle = document.getElementById('sidebarToggle');
            const sidebarOverlay = document.getElementById('sidebarOverlay');
            
            if (sidebarToggle && sidebar && sidebarOverlay) {
                sidebarToggle.addEventListener('click', function() {
                    sidebar.classList.toggle('show');
                    sidebarOverlay.classList.toggle('show');
                });
                
                sidebarOverlay.addEventListener('click', function() {
                    sidebar.classList.remove('show');
                    sidebarOverlay.classList.remove('show');
                });
            }
        });
    </script>
    @include('partials._toast')
</body>
</html>
