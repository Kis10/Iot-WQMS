<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'IoT-Based Water Quality Monitoring System') }}</title>

        <!-- Favicon -->
        <link rel="icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad1' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%2387CEEB;stop-opacity:1' /><stop offset='100%' style='stop-color:%231E90FF;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='48' fill='url(%23grad1)' stroke='%23FFF' stroke-width='2'/><path d='M 50 15 Q 45 30 45 40 Q 45 55 50 65 Q 55 55 55 40 Q 55 30 50 15' fill='%23FFF'/><path d='M 30 50 Q 35 45 45 48 Q 50 50 45 58 Q 35 60 30 55' fill='%23FFF'/><path d='M 70 55 Q 65 52 55 54 Q 50 56 55 62 Q 65 64 70 60' fill='%23FFF'/><path d='M 35 70 Q 40 68 50 70 Q 60 72 65 70' fill='%23FFF' opacity='0.8'/></svg>" />
        <link rel="shortcut icon" type="image/svg+xml" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 100 100'><defs><linearGradient id='grad1' x1='0%' y1='0%' x2='100%' y2='100%'><stop offset='0%' style='stop-color:%2387CEEB;stop-opacity:1' /><stop offset='100%' style='stop-color:%231E90FF;stop-opacity:1' /></linearGradient></defs><circle cx='50' cy='50' r='48' fill='url(%23grad1)' stroke='%23FFF' stroke-width='2'/><path d='M 50 15 Q 45 30 45 40 Q 45 55 50 65 Q 55 55 55 40 Q 55 30 50 15' fill='%23FFF'/><path d='M 30 50 Q 35 45 45 48 Q 50 50 45 58 Q 35 60 30 55' fill='%23FFF'/><path d='M 70 55 Q 65 52 55 54 Q 50 56 55 62 Q 65 64 70 60' fill='%23FFF'/><path d='M 35 70 Q 40 68 50 70 Q 60 72 65 70' fill='%23FFF' opacity='0.8'/></svg>" />
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />
        <link href="https://fonts.googleapis.com/css2?family=Coco+Gothic&display=swap" rel="stylesheet" />
        <style>
            :root {
                --font-coco: 'Coco Gothic', sans-serif;
            }
            body {
                font-family: 'Coco Gothic', sans-serif;
            }
            h1, h2, h3, h4, h5, h6 {
                font-family: 'Coco Gothic', sans-serif;
            }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen bg-gray-100 flex-col">
            <!-- Main Container -->
            <div class="flex flex-1 overflow-hidden">
                <!-- Sidebar -->
                <x-sidebar />

                <!-- Main Content -->
                <div class="flex-1 flex flex-col overflow-hidden">
                    @include('layouts.navigation')

                    <!-- Page Heading -->
                    @isset($header)
                        <header class="bg-white shadow">
                            <div class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
                                {{ $header }}
                            </div>
                        </header>
                    @endisset

                    <!-- Page Content -->
                    <main class="flex-1 overflow-y-auto">
                        {{ $slot }}
                    </main>
                </div>
            </div>

            <!-- Fixed Footer -->
            <footer class="bg-indigo-50 text-indigo-700 text-center py-4 border-t border-indigo-200">
                <p class="text-sm">&copy; 2026 IoT-Based Water Quality Monitoring System. All rights reserved.</p>
            </footer>
        </div>
    </body>
</html>
