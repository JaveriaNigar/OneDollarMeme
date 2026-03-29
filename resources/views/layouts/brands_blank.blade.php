<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'OneDollarMeme') }}</title>
        <!-- Favicon (JS-driven to bypass browser cache) -->
        <script>
            (function(){
                var l=document.createElement('link');
                l.rel='icon'; l.type='image/jpeg';
                l.href='{{ asset("image/my-logo.jpg") }}?v='+Date.now();
                document.head.appendChild(l);
            })();
        </script>
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;700;900&display=swap" rel="stylesheet">
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <style>
            :root {
                --primary-purple: #6f42c1;
                --primary-orange: #fd7e14;
                --secondary-orange: #ff6b35;
                --dark-purple: #5a32a3;
                --light-purple: #8a6de9;
                --base-bg: #f0f2f5;
            }
            body {
                font-family: 'Inter', sans-serif;
                background-color: var(--base-bg);
                margin: 0;
            }
            .text-purple { color: var(--primary-purple); }
            .bg-purple { background-color: var(--primary-purple); }
            .text-orange { color: var(--primary-orange); }
            .bg-orange { background-color: var(--primary-orange); }
            .bg-dark-purple { background-color: var(--dark-purple); }
            .bg-light-purple { background-color: var(--light-purple); }
            .hover\:text-orange:hover { color: var(--primary-orange); }
            .hover\:text-purple:hover { color: var(--primary-purple); }
            .hover\:bg-orange:hover { background-color: var(--primary-orange); }
            .hover\:bg-purple:hover { background-color: var(--primary-purple); }
        </style>
    </head>
    <body class="antialiased text-gray-900 min-h-screen">
        @yield('content')
    </body>
</html>
