<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'AquaSense') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('img/logo/logo-wq.png') }}">
        <link rel="shortcut icon" type="image/png" href="{{ asset('img/logo/logo-wq.png') }}">

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
    </head>
    <body class="font-sans text-gray-900 antialiased">
        <div class="min-h-screen flex flex-col sm:justify-center items-center pt-6 sm:pt-0 bg-[#0f172a] relative overflow-hidden">
            <!-- Background Effects -->
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/40 via-cyan-900/20 to-slate-900 z-0"></div>
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600/30 rounded-full blur-[100px] animate-pulse"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-cyan-500/20 rounded-full blur-[100px] animate-pulse delay-1000"></div>
            
            <!-- Network Constellation Canvas -->
            <canvas id="networkBgLogin" class="absolute inset-0 z-[1] pointer-events-none" style="opacity: 0.55;"></canvas>

            <!-- Logo/Title Area (Optional, for context) -->
            <div class="z-10 mb-6 text-center">
                <a href="/" class="flex flex-col items-center gap-2">
                    <img src="{{ asset('img/logo/logo-wq.png') }}" class="w-20 h-20 drop-shadow-lg" style="width: 5rem; height: 5rem;" alt="AquaSense Logo" />
                    <span class="text-3xl font-bold text-white tracking-tight drop-shadow-md">AquaSense</span>
                </a>
            </div>

            <div class="w-full sm:max-w-md mt-6 px-6 py-4 bg-white/95 backdrop-blur-sm shadow-2xl overflow-hidden sm:rounded-2xl z-10 border border-white/20">
                {{ $slot }}
            </div>
        </div>
        <script src="{{ asset('js/network-bg.js') }}"></script>
        <script>initNetworkBg('networkBgLogin');</script>
    </body>
</html>
