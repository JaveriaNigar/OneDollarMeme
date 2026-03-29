<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title>{{ config('app.name', 'OneDollarMeme') }}</title>
        <!-- Favicon -->
        <link rel="icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
        <link rel="shortcut icon" type="image/jpeg" href="{{ asset('image/my-logo.jpg') }}">
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
        <!-- Bootstrap CSS -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
        <!-- Font Awesome -->
        <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
        <!-- Bootstrap Icons -->
        <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
        
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        
        <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        @stack('styles')
        <style>
            :root {
                --primary-purple: #6f42c1;
                --brand-purple: var(--primary-purple);
                --primary-orange: #fd7e14;
                --brand-orange: var(--primary-orange);
                --secondary-orange: #ff6b35;
                --dark-purple: #5a32a3;
                --light-purple: #8a6de9;
                --base-bg: #f0f2f5;
            }
            body {
                font-family: 'Inter', sans-serif;
                margin: 0;
                padding-top: 64px;
                background-color: #f0f2f5 !important;
            }
            .text-purple { color: var(--primary-purple); }
            .bg-purple { background-color: var(--primary-purple); }
            .text-orange { color: var(--primary-orange); }
            .bg-orange { background-color: var(--primary-orange); }

            .transition-colors {
                transition-property: background-color, border-color, color, fill, stroke;
                transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
                transition-duration: 300ms;
            }
        </style>
    </head>
    <body class="antialiased text-gray-900 min-h-screen flex flex-col transition-colors duration-300">
        @include('partials._brands-header')

        <!-- Success Message Banner -->
        @if(session('success'))
            <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-2xl mx-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="bg-green-500 text-white py-3 px-6 rounded-xl shadow-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                        </svg>
                        <span class="font-light">{{ session('success') }}</span>
                    </div>
                    <button @click="show = false" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <!-- Error Message Banner -->
        @if(session('error'))
            <div class="fixed top-20 left-1/2 transform -translate-x-1/2 z-50 w-full max-w-2xl mx-4" x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 5000)">
                <div class="bg-red-500 text-white py-3 px-6 rounded-xl shadow-lg flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <i class="bi bi-exclamation-triangle-fill fs-5"></i>
                        <span class="font-light">{{ session('error') }}</span>
                    </div>
                    <button @click="show = false" class="text-white hover:text-gray-200">
                        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>
        @endif

        <main class="flex-grow">
            @yield('content')
        </main>

        @stack('scripts')
    </body>
</html>