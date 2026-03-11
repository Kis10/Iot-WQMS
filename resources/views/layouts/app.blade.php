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
        <link href="https://fonts.bunny.net/css?family=figtree:400,500,600,700&display=swap" rel="stylesheet" />
        <style>
            body { font-family: 'Figtree', sans-serif; }
            h1, h2, h3, h4, h5, h6 { font-family: 'Figtree', sans-serif; }
        </style>

        <!-- Scripts -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
        <script src="https://cdn.ably.io/lib/ably.min-1.js" defer></script>
    </head>
    <body class="font-sans antialiased">
        <div class="flex h-screen bg-gray-100 flex-col" x-data="{ sidebarOpen: false }" @toggle-sidebar.window="sidebarOpen = !sidebarOpen">
            <!-- Main Container -->
            <div class="flex flex-1 overflow-hidden">
                
                <!-- Mobile Sidebar Overlay (backdrop) -->
                <div x-show="sidebarOpen" 
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0"
                     x-transition:enter-end="opacity-100"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100"
                     x-transition:leave-end="opacity-0"
                     @click="sidebarOpen = false"
                     class="fixed inset-0 bg-black/50 z-40 md:hidden"
                     x-cloak></div>

                <!-- Sidebar: hidden on mobile, shown on desktop -->
                <div class="hidden md:flex">
                    <x-sidebar />
                </div>

                <!-- Mobile Sidebar (overlay with fade) -->
                <div x-show="sidebarOpen"
                     x-transition:enter="transition ease-out duration-300"
                     x-transition:enter-start="opacity-0 -translate-x-full"
                     x-transition:enter-end="opacity-100 translate-x-0"
                     x-transition:leave="transition ease-in duration-200"
                     x-transition:leave-start="opacity-100 translate-x-0"
                     x-transition:leave-end="opacity-0 -translate-x-full"
                     class="fixed inset-y-0 left-0 z-50 md:hidden"
                     x-cloak>
                    <x-sidebar />
                </div>

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
            @if(request()->routeIs('dashboard'))
                <x-sound-controls />
            @endif
        @endauth

        <audio id="globalAiSound" src="{{ asset('sounds/ai.mp3') }}" preload="metadata"></audio>
        <audio id="globalAlertSound" src="{{ asset('sounds/error.mp3') }}" preload="metadata"></audio>
        <script>
            (function() {
                // Global Audio Elements
                const audioEl = document.getElementById('globalAiSound');
                const alertEl = document.getElementById('globalAlertSound');
                let audioUnlocked = false;

                // Function to get current sound settings
                function getSoundSettings() {
                    return {
                        muted: localStorage.getItem('sounds_muted') === 'true',
                        aiEnabled: localStorage.getItem('sounds_ai_enabled') !== 'false',
                        alertEnabled: localStorage.getItem('sounds_alert_enabled') !== 'false',
                        volume: (localStorage.getItem('sounds_volume') || 100) / 100
                    };
                }

                function unlockAudio() {
                    if (audioUnlocked) return;
                    // Unlock both audio elements
                    const unlockOne = (el) => {
                        if (!el) return Promise.resolve();
                        const p = el.play();
                        if (p !== undefined) {
                            return p.then(() => { el.pause(); el.currentTime = 0; }).catch(() => {});
                        }
                        return Promise.resolve();
                    };
                    Promise.all([unlockOne(audioEl), unlockOne(alertEl)]).then(() => {
                        audioUnlocked = true;
                        ['click', 'touchstart', 'keydown'].forEach(e => document.removeEventListener(e, unlockAudio));
                    });
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
                        const riskLevel = (analysis.risk_level || '').toLowerCase();
                        const settings = getSoundSettings();

                        if (!settings.muted) {
                            // Play AI notification sound
                            if (audioEl && settings.aiEnabled) {
                                audioEl.currentTime = 0;
                                audioEl.volume = settings.volume;
                                audioEl.play().catch(e => console.error('AI sound failed:', e));
                            }

                            // Play alert sound 0.5s after ai.mp3 for critical/high/medium risk
                            // Increased clarity: alert sound is played if risk is not 'safe'
                            if (alertEl && settings.alertEnabled && (riskLevel === 'critical' || riskLevel === 'high' || riskLevel === 'medium')) {
                                setTimeout(() => {
                                    alertEl.currentTime = 0;
                                    alertEl.volume = settings.volume;
                                    alertEl.play().catch(e => console.error('Alert sound failed:', e));
                                }, 500);
                            }
                        }
                        
                        // Dispatch Global Event for Dashboard Popup
                        window.dispatchEvent(new CustomEvent('new-analysis', { detail: analysis }));
                    });

                    channel.subscribe('reading', (message) => {
                         const reading = message.data;
                         // Dispatch Global Event for Dashboard Chart/Gauges
                         window.dispatchEvent(new CustomEvent('new-reading', { detail: reading }));
                    });
                }
            })();
        </script>
    </body>
</html>
