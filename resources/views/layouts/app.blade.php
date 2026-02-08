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
