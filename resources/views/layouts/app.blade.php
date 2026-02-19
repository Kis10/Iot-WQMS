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
        <script src="https://cdn.ably.io/lib/ably.min-1.js"></script>
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

        @auth
            @if(!auth()->user()->isAdmin())
            <!-- Blocked User Modal (Real-Time Detection) -->
            <div id="blocked-modal" style="display: none;" class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/60 backdrop-blur-sm">
                <div class="bg-white rounded-2xl shadow-2xl p-8 w-full max-w-md mx-4 text-center">
                    <div class="mx-auto mb-6 w-16 h-16 rounded-full bg-red-100 flex items-center justify-center">
                        <svg class="w-8 h-8 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728A9 9 0 015.636 5.636m12.728 12.728L5.636 5.636"></path>
                        </svg>
                    </div>
                    <h2 class="text-xl font-bold text-gray-900 mb-4">Account Blocked</h2>
                    <p class="text-gray-600 text-sm leading-relaxed mb-8">You have been blocked by the Administrator for some reasons. Please contact Customer Services for your concern. Thank you for your understanding!</p>
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" onclick="this.form.action='{{ route('logout') }}';" class="w-full bg-red-600 hover:bg-red-700 text-white font-bold py-3 px-6 rounded-xl transition shadow-lg">
                            Logout
                        </button>
                    </form>
                </div>
            </div>

            <script>
                (function() {
                    let blocked = false;
                    function checkBlocked() {
                        if (blocked) return;
                        fetch('/block/check')
                            .then(r => r.json())
                            .then(data => {
                                if (data.blocked) {
                                    blocked = true;
                                    document.getElementById('blocked-modal').style.display = 'flex';
                                }
                            })
                            .catch(() => {});
                    }
                    setInterval(checkBlocked, 5000);
                    checkBlocked();
                })();
            </script>
            @endif
        @endauth
        <audio id="globalAiSound" src="{{ asset('sounds/ai.mp3') }}" preload="auto"></audio>
        <script>
            (function() {
                // Global Audio Unlock
                const audioEl = document.getElementById('globalAiSound');
                let audioUnlocked = false;

                function unlockAudio() {
                    if (audioUnlocked || !audioEl) return;
                    const playPromise = audioEl.play();
                    if (playPromise !== undefined) {
                        playPromise.then(() => {
                            audioEl.pause();
                            audioEl.currentTime = 0;
                            audioUnlocked = true;
                            ['click', 'touchstart', 'keydown'].forEach(e => document.removeEventListener(e, unlockAudio));
                            // console.log('Global AI Audio unlocked');
                        }).catch(() => {});
                    }
                }
                ['click', 'touchstart', 'keydown'].forEach(e => document.addEventListener(e, unlockAudio));

                // Global Ably Listener for Sound
                if (window.Ably) {
                    const ably = new Ably.Realtime({
                        authUrl: '{{ route("ably.auth") }}',
                        authMethod: 'POST',
                        authHeaders: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content }
                    });
                    const channel = ably.channels.get('{{ config('services.ably.channel', 'water-readings') }}');
                    
                    channel.subscribe('analysis', (message) => {
                        const analysis = message.data;
                        if (audioEl) {
                            audioEl.currentTime = 0;
                            audioEl.play().catch(e => console.error('Global play failed:', e));
                        }
                        
                        // Dispatch Global Event for Dashboard Popup
                        window.dispatchEvent(new CustomEvent('new-analysis', { detail: analysis }));
                    });
                }
            })();
        </script>
    </body>
</html>
