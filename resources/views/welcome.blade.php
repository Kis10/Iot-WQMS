<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'AquaSense') }} </title>

        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.googleapis.com">
        <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
        <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
        
        <!-- Favicon -->
        <link rel="icon" type="image/png" href="{{ asset('img/logo/logo-wq.png') }}">

        <!-- Scripts & Styles -->
        @vite(['resources/css/app.css', 'resources/js/app.js'])
        <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>

        <style>
            body {
                font-family: 'Outfit', sans-serif;
            }
            .glass {
                background: rgba(255, 255, 255, 0.7);
                backdrop-filter: blur(10px);
                -webkit-backdrop-filter: blur(10px);
            }
            @keyframes fadeInUpAnim {
                0% { opacity: 0; translate: 0 22px; }
                100% { opacity: 1; translate: 0 0; }
            }
            .fade-in-up {
                opacity: 0;
            }
            .fade-in-up.visible {
                animation: fadeInUpAnim 0.85s cubic-bezier(0.22, 1, 0.36, 1) forwards;
            }
            .fade-stagger > .fade-in-up:nth-child(1) { animation-delay: 70ms; }
            .fade-stagger > .fade-in-up:nth-child(2) { animation-delay: 150ms; }
            .fade-stagger > .fade-in-up:nth-child(3) { animation-delay: 230ms; }
            .fade-stagger > .fade-in-up:nth-child(4) { animation-delay: 310ms; }
            .fade-stagger > .fade-in-up:nth-child(5) { animation-delay: 390ms; }
            .fade-stagger > .fade-in-up:nth-child(6) { animation-delay: 470ms; }
            .smooth-pop-card {
                transition: transform 300ms ease-out, box-shadow 300ms ease-out, border-color 300ms ease-out;
                will-change: transform;
            }
            .smooth-pop-card:hover {
                transform: translate3d(0, -10px, 0);
                box-shadow: 0 20px 30px -12px rgba(15, 23, 42, 0.2), 0 10px 16px -12px rgba(15, 23, 42, 0.2);
            }
            .sensor-card {
                transition: transform 300ms ease-out, box-shadow 300ms ease-out, border-color 300ms ease-out;
                will-change: transform;
            }
            .sensor-card:hover {
                transform: translate3d(0, -10px, 0);
                box-shadow: 0 20px 30px -12px rgba(15, 23, 42, 0.2), 0 10px 16px -12px rgba(15, 23, 42, 0.2);
            }
            .gradient-text {
                background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
            @keyframes heroFloat {
                0%, 100% {
                    transform: translate3d(0, 0, 0) rotate(0deg);
                }
                50% {
                    transform: translate3d(0, -18px, 0) rotate(3deg);
                }
            }
            .hero-float {
                animation: heroFloat 8s ease-in-out infinite;
                will-change: transform;
            }
            .hero-float-delayed {
                animation-delay: 1.8s;
            }
            .hero-float-slow {
                animation-duration: 11s;
            }
            @keyframes heroTextIn {
                0% {
                    opacity: 0;
                    transform: translate3d(0, 24px, 0);
                }
                60% {
                    opacity: 0.88;
                    transform: translate3d(0, 4px, 0);
                }
                100% {
                    opacity: 1;
                    transform: translate3d(0, 0, 0);
                }
            }
            .hero-text-in {
                opacity: 0;
                transform: translate3d(0, 24px, 0);
                animation: heroTextIn 1.45s cubic-bezier(0.22, 1, 0.36, 1) forwards;
                will-change: transform, opacity;
                backface-visibility: hidden;
            }
            .hero-text-in-delay {
                animation-delay: 0.38s;
            }
            .slide-in-right {
                opacity: 0;
                transform: translate3d(100px, 0, 0);
                transition: transform 0.8s cubic-bezier(0.22, 1, 0.36, 1), opacity 0.8s ease-out;
                will-change: transform, opacity;
            }
            .slide-in-right.visible {
                opacity: 1;
                transform: translate3d(0, 0, 0);
            }
            @media (prefers-reduced-motion: reduce) {
                .hero-text-in,
                .hero-text-in-delay,
                .fade-in-up,
                .slide-in-right {
                    animation: none;
                    opacity: 1;
                    transform: none;
                    translate: 0 0;
                    transition: none;
                }
            }
        </style>
    </head>
    <body class="antialiased text-gray-900 bg-gray-50 overflow-x-hidden">
        
        <!-- Animated Navbar -->
        <nav class="fixed top-0 w-full z-50 transition-all duration-300 glass border-b border-gray-100 py-4" x-data="{ atTop: true, mobileOpen: false }" @scroll.window="atTop = (window.pageYOffset > 50 ? false : true)">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center justify-between w-full">
                    <!-- Brand (Far Left) -->
                    <div class="flex items-center gap-3">
                        <a href="/" 
                           ontouchend="event.preventDefault(); var now=Date.now(); if(now - (window._lastLogoTap||0) < 500){ fetch('/logo-access',{method:'POST',headers:{'X-CSRF-TOKEN':document.querySelector('meta[name=csrf-token]').content}}).then(function(){window.location.href='/login';}); } window._lastLogoTap=now;"
                           class="flex items-center gap-3">
                            <div class="w-10 h-10 overflow-hidden p-1">
                                <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="w-full h-full object-contain" />
                            </div>
                            <span class="text-2xl font-bold tracking-tight text-gray-900">{{ config('app.name', 'AquaSense') }}</span>
                        </a>
                    </div>
                    
                    <!-- Desktop Nav Links (Right) -->
                    <div class="hidden md:flex items-center space-x-8 text-sm font-semibold ml-auto">
                        <a href="#home" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Home</a>
                        <a href="#features" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Sensors</a>
                        <a href="#services" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Services</a>
                        <a href="#about" class="text-gray-600 hover:text-blue-600 transition tracking-wide">About</a>
                        <a href="#contact" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Contact</a>
                    </div>

                    <!-- Mobile Hamburger Button (Right) -->
                    <button @click="mobileOpen = !mobileOpen" class="md:hidden ml-auto p-2 rounded-lg hover:bg-gray-100 transition focus:outline-none" aria-label="Toggle Navigation">
                        <!-- Hamburger Icon (show when closed) -->
                        <svg x-show="!mobileOpen" class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                        </svg>
                        <!-- X Icon (show when open) -->
                        <svg x-show="mobileOpen" x-cloak class="w-6 h-6 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                        </svg>
                    </button>
                </div>
            </div>

            <!-- Mobile Nav Menu (Slide Down) -->
            <div x-show="mobileOpen" x-cloak
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 -translate-y-2"
                 x-transition:enter-end="opacity-100 translate-y-0"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-100 translate-y-0"
                 x-transition:leave-end="opacity-0 -translate-y-2"
                 class="md:hidden border-t border-gray-100 mt-4 pt-4 pb-2 px-4">
                <div class="flex flex-col space-y-3">
                    <a href="#home" @click="mobileOpen = false" class="text-gray-700 hover:text-blue-600 font-semibold text-base py-2 px-3 rounded-lg hover:bg-blue-50 transition">Home</a>
                    <a href="#features" @click="mobileOpen = false" class="text-gray-700 hover:text-blue-600 font-semibold text-base py-2 px-3 rounded-lg hover:bg-blue-50 transition">Sensors</a>
                    <a href="#services" @click="mobileOpen = false" class="text-gray-700 hover:text-blue-600 font-semibold text-base py-2 px-3 rounded-lg hover:bg-blue-50 transition">Services</a>
                    <a href="#about" @click="mobileOpen = false" class="text-gray-700 hover:text-blue-600 font-semibold text-base py-2 px-3 rounded-lg hover:bg-blue-50 transition">About</a>
                    <a href="#contact" @click="mobileOpen = false" class="text-gray-700 hover:text-blue-600 font-semibold text-base py-2 px-3 rounded-lg hover:bg-blue-50 transition">Contact</a>
                </div>
            </div>
        </nav>

        <!-- Dynamic Hero Section (Refined Visibility) -->
        <section id="home" class="relative min-h-screen flex items-center justify-center bg-[#0f172a] overflow-hidden pt-24 sm:pt-32 pb-12 sm:pb-20">
            <!-- Network Constellation Canvas -->
            <canvas id="networkBgHero" class="absolute inset-0 z-[1] pointer-events-none" style="opacity: 0.55;"></canvas>

            <!-- Optional Dynamic Background Texture -->
            @php
                $heroEntry = $contents['hero_bg'] ?? null;
                $heroImage = $heroEntry->image ?? null;
                $heroLocal = $heroEntry->value ?? null;
                $heroSrc = $heroImage ? (str_starts_with($heroImage, 'http') ? $heroImage : asset($heroImage)) : null;
                $heroLocalSrc = ($heroLocal && !str_starts_with($heroLocal, 'http')) ? asset($heroLocal) : null;
            @endphp
            @if($heroSrc)
                <div class="absolute inset-0 z-0">
                    <img src="{{ $heroSrc }}" class="w-full h-full object-cover opacity-[0.15] mix-blend-screen" @if($heroLocalSrc) onerror="this.onerror=null; this.src='{{ $heroLocalSrc }}';" @endif>
                </div>
            @endif
            <div class="absolute inset-0 z-0 bg-gradient-to-br from-blue-900/40 via-cyan-900/20 to-slate-900"></div>
            <div class="absolute top-[-10%] left-[-10%] w-96 h-96 bg-blue-600/30 rounded-full blur-[100px] animate-pulse z-0"></div>
            <div class="absolute bottom-[-10%] right-[-10%] w-96 h-96 bg-cyan-500/20 rounded-full blur-[100px] animate-pulse delay-1000 z-0"></div>

        
            <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
                <div class="fade-in-up visible">
                    <br><br>
                    <h1 class="hero-text-in text-3xl sm:text-5xl md:text-7xl lg:text-8xl font-black text-white mb-6 sm:mb-8 leading-tight tracking-tight">
                        {!! $contents['hero_title']->value ?? 'IoT-Based Water Quality <br> <span class="gradient-text">Monitoring System</span>' !!}
                    </h1>
                    <p class="hero-text-in hero-text-in-delay text-base sm:text-xl md:text-2xl text-blue-100 mb-8 sm:mb-12 max-w-3xl mx-auto font-medium leading-relaxed opacity-90 px-2">
                        {{ $contents['hero_subtitle']->value ?? 'Ensuring a sustainable aquaculture environment through high-precision IoT sensors and real-time data analytics.' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Continuous Dotted Pattern Background (Stops Before Footer) -->
        <div class="relative overflow-hidden bg-white">
            <div class="absolute inset-0 z-0 pointer-events-none opacity-[0.3] bg-[radial-gradient(circle_at_1px_1px,rgba(148,163,184,0.45)_1px,transparent_0)] [background-size:28px_28px]"></div>
            <div class="relative z-10">

        <!-- General Caption Section -->
        <section class="py-12 sm:py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto fade-in-up">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-bold mb-6">
                        {{ $contents['mission_badge']->value ?? 'OUR MISSION' }}
                    </div>
                    <h2 class="text-2xl sm:text-4xl font-bold mb-6 sm:mb-8 tracking-tight" style="color: #0D1A63;">{{ $contents['mission_title']->value ?? 'The Future of Aquaculture Management' }}</h2>
                    <p class="text-base sm:text-xl text-gray-600 leading-relaxed font-light">
                        {{ $contents['mission_text']->value ?? 'Our system is designed to provide farmers with a robust, reliable, and user-friendly platform for monitoring vital aquatic conditions. By leveraging the power of IoT, we help eliminate the guesswork, reduce risks, and maximize productivity in aquaculture operations.' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Sensor Features Section -->
        <section id="features" class="py-12 sm:py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-up">
                    <h2 class="text-2xl sm:text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensors_title']->value ?? 'Integrated Sensor Technology' }}</h2>
                    <p class="text-gray-500 text-lg">{{ $contents['sensors_subtitle']->value ?? 'Our system utilizes four high-precision sensors to capture every critical metric.' }}</p>
                </div>

                <div class="grid grid-cols-2 md:grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-12 fade-stagger">
                    <!-- Sensor 1: pH -->
                    <div class="sensor-card bg-white p-4 sm:p-8 rounded-2xl border border-gray-100 transition-all duration-300 ease-out group hover:border-blue-200 fade-in-up mb-4 sm:mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor1_title']->value ?? 'pH Sensor' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor1_desc']->value ?? 'Measures the acidity or alkalinity of the water to ensure a healthy environment for aquatic life.' }}</p>
                    </div>

                    <!-- Sensor 2: Turbidity -->
                    <div class="sensor-card bg-white p-4 sm:p-8 rounded-2xl border border-gray-100 transition-all duration-300 ease-out group hover:border-blue-200 fade-in-up mb-4 sm:mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z"></path>
                                <circle cx="12" cy="14" r="1" fill="currentColor"></circle>
                                <circle cx="15" cy="13" r="0.5" fill="currentColor"></circle>
                                <circle cx="9" cy="13" r="0.5" fill="currentColor"></circle>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor2_title']->value ?? 'Turbidity' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor2_desc']->value ?? 'Detects water clarity by measuring suspended particles, crucial for accurate quality assessment.' }}</p>
                    </div>

                    <!-- Sensor 3: TDS -->
                    <div class="sensor-card bg-white p-4 sm:p-8 rounded-2xl border border-gray-100 transition-all duration-300 ease-out group hover:border-blue-200 fade-in-up mb-4 sm:mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path>
                                <circle cx="12" cy="14" r="1.5" fill="currentColor"></circle>
                                <circle cx="15.5" cy="12.5" r="1" fill="currentColor"></circle>
                                <circle cx="8.5" cy="12.5" r="1" fill="currentColor"></circle>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v6"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor3_title']->value ?? 'TDS Sensor' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor3_desc']->value ?? 'Monitors the concentration of dissolved substances, indicating the overall purity of the water.' }}</p>
                    </div>

                    <!-- Sensor 4: Temperature -->
                    <div class="sensor-card bg-white p-4 sm:p-8 rounded-2xl border border-gray-100 transition-all duration-300 ease-out group hover:border-blue-200 fade-in-up mb-4 sm:mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19c-1.657 0-3-1.343-3-3V6a3 3 0 116 0v10c0 1.657-1.343 3-3 3z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h4m-4 4h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor4_title']->value ?? 'Temperature' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor4_desc']->value ?? 'Tracks water temperature to prevent thermal stress and maintain optimal growth rates for fish.' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-12 sm:py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-up">
                    <h2 class="text-2xl sm:text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['services_title']->value ?? 'Our Services' }}</h2>
                    <p class="text-gray-500 text-lg">{{ $contents['services_subtitle']->value ?? 'We provide end-to-end solutions for aquaculture technology integration.' }}</p>
                </div>

                <div class="max-w-4xl mx-auto space-y-12 fade-stagger">
                    <div class="flex gap-4 sm:gap-8 items-start fade-in-up">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">{{ $contents['service1_num']->value ?? '01' }}</div>
                        <div>
                            <h4 class="text-lg sm:text-2xl font-bold mb-2 sm:mb-3 tracking-tight" style="color: #0D1A63;">{{ $contents['service1_title']->value ?? 'Automated Data Collection' }}</h4>
                            <p class="text-gray-600 leading-relaxed text-sm sm:text-lg">{{ $contents['service1_desc']->value ?? 'Continuous background data harvesting from a pond, simultaneously without manual intervention.' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4 sm:gap-8 items-start fade-in-up">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">{{ $contents['service2_num']->value ?? '02' }}</div>
                        <div>
                            <h4 class="text-lg sm:text-2xl font-bold mb-2 sm:mb-3 tracking-tight" style="color: #0D1A63;">{{ $contents['service2_title']->value ?? 'Smart Alert Notifications' }}</h4>
                            <p class="text-gray-600 leading-relaxed text-sm sm:text-lg">{{ $contents['service2_desc']->value ?? 'Instant Alert notifications when water parameters exceed safe threshold limits for your specific fish species.' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-4 sm:gap-8 items-start fade-in-up">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">{{ $contents['service3_num']->value ?? '03' }}</div>
                        <div>
                            <h4 class="text-lg sm:text-2xl font-bold mb-2 sm:mb-3 tracking-tight" style="color: #0D1A63;">{{ $contents['service3_title']->value ?? 'AI Condition Analysis' }}</h4>
                            <p class="text-gray-600 leading-relaxed text-sm sm:text-lg">{{ $contents['service3_desc']->value ?? 'Advanced algorithms that analyze patterns to predict water quality health and recommend corrective actions.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About / Team Section -->
        <section id="about" class="py-12 sm:py-24 overflow-hidden relative" 
                 x-data="{ openDemo: false, showFloatingDemo: false }"
                 @scroll.window="showFloatingDemo = ($el.getBoundingClientRect().top < window.innerHeight && $el.getBoundingClientRect().bottom > 0)"
                 x-init="showFloatingDemo = ($el.getBoundingClientRect().top < window.innerHeight && $el.getBoundingClientRect().bottom > 0)">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <!-- Project Info & Demo Button -->
                <div class="text-center mb-16 relative w-full max-w-3xl mx-auto fade-in-up">
                    <h2 class="text-3xl sm:text-5xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['project_title']->value ?? 'About the Project' }}</h2>
                    <p class="text-gray-500 text-lg mx-auto">{{ $contents['project_desc']->value ?? 'AquaSense provides a robust and reliable platform for monitoring aquatic conditions by tracking physical and chemical data.' }}</p>
                </div>
                
                @php 
                    $demoRow = $contents['project_video'] ?? null;
                    $demoVid = $demoRow->image ?? $demoRow->value ?? null;
                    $demoUrl = $demoVid ? (str_starts_with($demoVid, 'http') ? $demoVid : asset($demoVid)) : null;
                @endphp

                <!-- Floating Watch Demo Button -->
                    <div class="fixed bottom-8 right-6 sm:right-8 z-[90] transition-all duration-500 ease-in-out"
                         :class="showFloatingDemo ? 'opacity-100 translate-x-0' : 'opacity-0 translate-x-[150%]'"
                         x-cloak>
                        <button @click="openDemo = true" class="group flex items-center gap-3 bg-[#0D1A63] hover:bg-blue-700 text-white px-5 py-3 rounded-full font-bold shadow-[0_10px_40px_-10px_rgba(13,26,99,0.8)] transition transform hover:scale-105 border border-white/10">
                            <span class="flex items-center justify-center w-8 h-8 rounded-full bg-white text-[#0D1A63] group-hover:scale-110 transition shrink-0 shadow-sm">
                                <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 24 24"><path d="M8 5v14l11-7z"/></svg>
                            </span>
                            <span class="tracking-wide">Watch Demo</span>
                        </button>
                    </div>

                    <!-- Video Demo Modal -->
                    <div x-show="openDemo" x-cloak class="fixed inset-0 z-[100] flex items-center justify-center bg-black/90 backdrop-blur-sm"
                         x-transition:enter="transition ease-out duration-300"
                         x-transition:enter-start="opacity-0"
                         x-transition:enter-end="opacity-100"
                         x-transition:leave="transition ease-in duration-200"
                         x-transition:leave-start="opacity-100"
                         x-transition:leave-end="opacity-0">
                         
                        <!-- Modal Content (Matches Carousel Size approx) -->
                        <div x-show="openDemo" @click.outside="openDemo = false; if($refs.demoVideo) $refs.demoVideo.pause()" class="relative w-full max-w-5xl mx-auto h-[300px] sm:h-[500px] rounded-3xl overflow-hidden shadow-2xl bg-black border border-gray-800"
                             x-transition:enter="transition ease-out duration-300 delay-100 transform"
                             x-transition:enter-start="opacity-0 scale-90"
                             x-transition:enter-end="opacity-100 scale-100"
                             x-transition:leave="transition ease-in duration-200 transform"
                             x-transition:leave-start="opacity-100 scale-100"
                             x-transition:leave-end="opacity-0 scale-90">
                             
                             <button @click="openDemo = false; if($refs.demoVideo) $refs.demoVideo.pause()" class="absolute top-4 right-4 z-50 p-2 bg-black/50 hover:bg-red-600 text-white rounded-full backdrop-blur transition flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                             </button>
                             
                             @if($demoUrl)
                             <video x-ref="demoVideo" controls class="w-full h-full object-contain bg-black">
                                 <source src="{{ $demoUrl }}">
                                 Your browser does not support the video tag.
                             </video>
                             @else
                             <div class="w-full h-full flex flex-col items-center justify-center text-gray-500">
                                 <svg class="w-16 h-16 mb-4 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 10l4.553-2.276A1 1 0 0121 8.618v6.764a1 1 0 01-1.447.894L15 14M5 18h8a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v8a2 2 0 002 2z"></path></svg>
                                 <p>No video uploaded yet.</p>
                             </div>
                             @endif
                        </div>
                    </div>
                </div>

                <!-- Step-by-Step Flowchart -->
                <div class="max-w-5xl mx-auto mb-20 fade-in-up">
                    <div class="flex flex-col md:flex-row items-center justify-between gap-6 relative">
                        <!-- Connecting Line (Desktop) -->
                        <div class="hidden md:block absolute top-1/2 left-10 right-10 h-1 bg-gradient-to-r from-blue-200 via-blue-400 to-blue-200 -translate-y-1/2 z-0"></div>
                        
                        <!-- Step 1 -->
                        <div class="relative z-10 w-full md:w-1/3 text-center bg-white p-6 rounded-3xl shadow-[0_10px_40px_-15px_rgba(37,99,235,0.15)] border border-blue-50 hover:-translate-y-2 transition-transform duration-300">
                            <div class="w-16 h-16 mx-auto bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-5 shadow-sm transform rotate-3">
                                <svg class="w-8 h-8 -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path></svg>
                            </div>
                            <h3 class="font-bold text-xl mb-3" style="color: #0D1A63;">{{ $contents['flow_step1_title']->value ?? 'Water Measurement' }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['flow_step1_desc']->value ?? 'Four high-precision sensors deployed in the water continuously gather real-time data.' }}</p>
                        </div>
                        
                        <!-- Arrow (Mobile) -->
                        <div class="md:hidden text-blue-300">
                            <svg class="w-8 h-8 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>
                        
                        <!-- Step 2 -->
                        <div class="relative z-10 w-full md:w-1/3 text-center bg-white p-6 rounded-3xl shadow-[0_10px_40px_-15px_rgba(37,99,235,0.15)] border border-blue-50 hover:-translate-y-2 transition-transform duration-300">
                            <div class="w-16 h-16 mx-auto bg-blue-600 text-white rounded-2xl flex items-center justify-center mb-5 shadow-lg transform -rotate-3">
                                <svg class="w-8 h-8 rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 3v2m6-2v2M9 19v2m6-2v2M5 9H3m2 6H3m18-6h-2m2 6h-2M7 19h10a2 2 0 002-2V7a2 2 0 00-2-2H7a2 2 0 00-2 2v10a2 2 0 002 2zM9 9h6v6H9V9z"></path></svg>
                            </div>
                            <h3 class="font-bold text-xl mb-3" style="color: #0D1A63;">{{ $contents['flow_step2_title']->value ?? 'Data Processing' }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['flow_step2_desc']->value ?? 'An ESP32 microcontroller processes the sensor data and displays local readings on an LCD screen.' }}</p>
                        </div>

                        <!-- Arrow (Mobile) -->
                        <div class="md:hidden text-blue-300">
                            <svg class="w-8 h-8 rotate-90" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                        </div>

                        <!-- Step 3 -->
                        <div class="relative z-10 w-full md:w-1/3 text-center bg-white p-6 rounded-3xl shadow-[0_10px_40px_-15px_rgba(37,99,235,0.15)] border border-blue-50 hover:-translate-y-2 transition-transform duration-300">
                            <div class="w-16 h-16 mx-auto bg-blue-50 text-blue-600 rounded-2xl flex items-center justify-center mb-5 shadow-sm transform rotate-3">
                                <svg class="w-8 h-8 -rotate-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                            </div>
                            <h3 class="font-bold text-xl mb-3" style="color: #0D1A63;">{{ $contents['flow_step3_title']->value ?? 'System Monitoring' }}</h3>
                            <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['flow_step3_desc']->value ?? 'The data is securely transmitted to the cloud, allowing users to monitor water quality from any device.' }}</p>
                        </div>
                    </div>
                </div>

                <!-- Automated Sliding Fade Transition Carousel -->
                @php
                    $sliderImages = [];
                    for($i=1; $i<=5; $i++) {
                        $sl = $contents['slider'.$i.'_img'] ?? null;
                        $sSrc = $sl->image ?? $sl->value ?? null;
                        if ($sSrc) {
                            $sliderImages[] = str_starts_with($sSrc, 'http') ? $sSrc : asset($sSrc);
                        }
                    }
                @endphp
                <div class="mb-16 fade-in-up" x-data="{ currentSlide: 1, slides: {{ json_encode($sliderImages) }} }" x-init="
                    if(slides.length > 0) {
                        setInterval(() => { currentSlide = currentSlide < slides.length ? currentSlide + 1 : 1; }, 4000);
                    }
                ">
                    <template x-if="slides.length > 0">
                        <div class="relative w-full max-w-5xl mx-auto h-[300px] sm:h-[500px] rounded-3xl overflow-hidden shadow-xl border border-gray-100 bg-gray-100">
                            <template x-for="(slide, index) in slides" :key="index">
                                <div x-show="currentSlide === index + 1"
                                     x-transition:enter="transition-opacity ease-in-out duration-1000"
                                     x-transition:enter-start="opacity-0"
                                     x-transition:enter-end="opacity-100"
                                     x-transition:leave="transition-opacity ease-in-out duration-1000"
                                     x-transition:leave-start="opacity-100"
                                     x-transition:leave-end="opacity-0"
                                     class="absolute inset-0 w-full h-full">
                                    <img :src="slide" class="w-full h-full object-cover">
                                </div>
                            </template>
                            <!-- Navigation dots -->
                            <div class="absolute bottom-4 left-0 right-0 flex justify-center space-x-2 z-10">
                                <template x-for="(slide, index) in slides" :key="index">
                                    <button @click="currentSlide = index + 1" :class="{'bg-[#0D1A63] w-6': currentSlide === index + 1, 'bg-white/70 w-2': currentSlide !== index + 1}" class="h-2 rounded-full transition-all duration-300"></button>
                                </template>
                            </div>
                        </div>
                    </template>
                </div>
                
                <div class="text-center mb-16 mt-24 fade-in-up">
                    <h2 class="text-2xl sm:text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['about_title']->value ?? 'Meet the Team' }}</h2>
                    <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ $contents['about_subtitle']->value ?? 'The dedicated minds behind AquaSense, working together to revolutionize aquaculture monitoring.' }}</p>
                </div>

                <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-8 fade-stagger">
                    
                    <!-- Team Member 1 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center smooth-pop-card fade-in-up">
                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @php
                                $team1ImageEntry = $contents['team1_img'] ?? null;
                                $team1Image = $team1ImageEntry->image ?? null;
                                $team1Local = $team1ImageEntry->value ?? null;
                                $team1Src = $team1Image ? (str_starts_with($team1Image, 'http') ? $team1Image : asset($team1Image)) : null;
                                $team1LocalSrc = ($team1Local && !str_starts_with($team1Local, 'http')) ? asset($team1Local) : null;
                            @endphp
                            @if($team1Src)
                                <img src="{{ $team1Src }}" class="w-full h-full object-cover" @if($team1LocalSrc) onerror="this.onerror=null; this.src='{{ $team1LocalSrc }}';" @endif>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @php
                                $team1HoverEntry = $contents['team1_img_hover'] ?? null;
                                $team1HoverImage = $team1HoverEntry->image ?? null;
                                $team1HoverLocal = $team1HoverEntry->value ?? null;
                                $team1HoverSrc = $team1HoverImage ? (str_starts_with($team1HoverImage, 'http') ? $team1HoverImage : asset($team1HoverImage)) : null;
                                $team1HoverLocalSrc = ($team1HoverLocal && !str_starts_with($team1HoverLocal, 'http')) ? asset($team1HoverLocal) : null;
                            @endphp
                            @if($team1HoverSrc)
                                <img src="{{ $team1HoverSrc }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-700 ease-in-out bg-white" @if($team1HoverLocalSrc) onerror="this.onerror=null; this.src='{{ $team1HoverLocalSrc }}';" @endif>
                            @else
                                <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team1_name']->value ?? 'Kirstine A. Sanchez' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team1_role']->value ?? 'Web/Arduino Developer' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team1_desc']->value ?? 'Spearheads the hardware integration and full-stack web development.' }}</p>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center smooth-pop-card fade-in-up">
                         <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @php
                                $team2ImageEntry = $contents['team2_img'] ?? null;
                                $team2Image = $team2ImageEntry->image ?? null;
                                $team2Local = $team2ImageEntry->value ?? null;
                                $team2Src = $team2Image ? (str_starts_with($team2Image, 'http') ? $team2Image : asset($team2Image)) : null;
                                $team2LocalSrc = ($team2Local && !str_starts_with($team2Local, 'http')) ? asset($team2Local) : null;
                            @endphp
                            @if($team2Src)
                                <img src="{{ $team2Src }}" class="w-full h-full object-cover" @if($team2LocalSrc) onerror="this.onerror=null; this.src='{{ $team2LocalSrc }}';" @endif>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @php
                                $team2HoverEntry = $contents['team2_img_hover'] ?? null;
                                $team2HoverImage = $team2HoverEntry->image ?? null;
                                $team2HoverLocal = $team2HoverEntry->value ?? null;
                                $team2HoverSrc = $team2HoverImage ? (str_starts_with($team2HoverImage, 'http') ? $team2HoverImage : asset($team2HoverImage)) : null;
                                $team2HoverLocalSrc = ($team2HoverLocal && !str_starts_with($team2HoverLocal, 'http')) ? asset($team2HoverLocal) : null;
                            @endphp
                            @if($team2HoverSrc)
                                <img src="{{ $team2HoverSrc }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-700 ease-in-out bg-white" @if($team2HoverLocalSrc) onerror="this.onerror=null; this.src='{{ $team2HoverLocalSrc }}';" @endif>
                            @else
                                <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team2_name']->value ?? 'Dannica J. Besinio' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team2_role']->value ?? 'Documenter' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team2_desc']->value ?? 'Ensures comprehensive documentation of system processes and user guides.' }}</p>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center smooth-pop-card fade-in-up">
                         <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @php
                                $team3ImageEntry = $contents['team3_img'] ?? null;
                                $team3Image = $team3ImageEntry->image ?? null;
                                $team3Local = $team3ImageEntry->value ?? null;
                                $team3Src = $team3Image ? (str_starts_with($team3Image, 'http') ? $team3Image : asset($team3Image)) : null;
                                $team3LocalSrc = ($team3Local && !str_starts_with($team3Local, 'http')) ? asset($team3Local) : null;
                            @endphp
                            @if($team3Src)
                                <img src="{{ $team3Src }}" class="w-full h-full object-cover" @if($team3LocalSrc) onerror="this.onerror=null; this.src='{{ $team3LocalSrc }}';" @endif>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @php
                                $team3HoverEntry = $contents['team3_img_hover'] ?? null;
                                $team3HoverImage = $team3HoverEntry->image ?? null;
                                $team3HoverLocal = $team3HoverEntry->value ?? null;
                                $team3HoverSrc = $team3HoverImage ? (str_starts_with($team3HoverImage, 'http') ? $team3HoverImage : asset($team3HoverImage)) : null;
                                $team3HoverLocalSrc = ($team3HoverLocal && !str_starts_with($team3HoverLocal, 'http')) ? asset($team3HoverLocal) : null;
                            @endphp
                            @if($team3HoverSrc)
                                <img src="{{ $team3HoverSrc }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-700 ease-in-out bg-white" @if($team3HoverLocalSrc) onerror="this.onerror=null; this.src='{{ $team3HoverLocalSrc }}';" @endif>
                            @else
                                <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team3_name']->value ?? 'Joy Mae A. Samra' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team3_role']->value ?? 'Documenter' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team3_desc']->value ?? 'Focuses on research, technical writing, and system validation.' }}</p>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center smooth-pop-card fade-in-up">
                         <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @php
                                $team4ImageEntry = $contents['team4_img'] ?? null;
                                $team4Image = $team4ImageEntry->image ?? null;
                                $team4Local = $team4ImageEntry->value ?? null;
                                $team4Src = $team4Image ? (str_starts_with($team4Image, 'http') ? $team4Image : asset($team4Image)) : null;
                                $team4LocalSrc = ($team4Local && !str_starts_with($team4Local, 'http')) ? asset($team4Local) : null;
                            @endphp
                            @if($team4Src)
                                <img src="{{ $team4Src }}" class="w-full h-full object-cover" @if($team4LocalSrc) onerror="this.onerror=null; this.src='{{ $team4LocalSrc }}';" @endif>
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @php
                                $team4HoverEntry = $contents['team4_img_hover'] ?? null;
                                $team4HoverImage = $team4HoverEntry->image ?? null;
                                $team4HoverLocal = $team4HoverEntry->value ?? null;
                                $team4HoverSrc = $team4HoverImage ? (str_starts_with($team4HoverImage, 'http') ? $team4HoverImage : asset($team4HoverImage)) : null;
                                $team4HoverLocalSrc = ($team4HoverLocal && !str_starts_with($team4HoverLocal, 'http')) ? asset($team4HoverLocal) : null;
                            @endphp
                            @if($team4HoverSrc)
                                <img src="{{ $team4HoverSrc }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-700 ease-in-out bg-white" @if($team4HoverLocalSrc) onerror="this.onerror=null; this.src='{{ $team4HoverLocalSrc }}';" @endif>
                            @else
                                <div class="absolute inset-0 bg-blue-600/20 opacity-0 group-hover:opacity-100 transition-opacity duration-300"></div>
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team4_name']->value ?? 'Jonas D. ParraÃƒÆ’Ã‚Â±o' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team4_role']->value ?? 'System Analyst / Capstone Adviser' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team4_desc']->value ?? 'Provides expert guidance on system architecture and project direction.' }}</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="py-12 sm:py-24 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20 fade-in-up">
                    <h2 class="text-2xl sm:text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['contact_title']->value ?? 'Contact Us' }}</h2>
                    <p class="text-gray-500 text-lg">{{ $contents['contact_subtitle']->value ?? 'Have questions? We\'re here to help you optimize your aquaculture operations.' }}</p>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-6 sm:gap-12 text-center fade-stagger">
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm transition-all duration-300 ease-out group hover:border-blue-200 smooth-pop-card fade-in-up">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color: #0D1A63;">{{ $contents['contact_email_label']->value ?? 'Email Address' }}</h4>
                        @php $email = $contents['contact_email']->value ?? 'kirstinesanchez9@gmail.com'; @endphp
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $email }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-[#0D1A63] transition font-medium block">
                            {{ $email }}
                        </a>
                    </div>
                    
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm transition-all duration-300 ease-out group hover:border-blue-200 smooth-pop-card fade-in-up">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color: #0D1A63;">{{ $contents['contact_phone_label']->value ?? 'Mobile Number' }}</h4>
                        @php $phones = explode("\n", $contents['contact_phone']->value ?? "09207327946\n09151003714"); @endphp
                        @foreach($phones as $phone)
                            <a href="tel:{{ trim($phone) }}" class="text-gray-500 hover:text-[#0D1A63] transition font-medium block">{{ trim($phone) }}</a>
                        @endforeach
                    </div>
                    
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm transition-all duration-300 ease-out group hover:border-blue-200 smooth-pop-card fade-in-up">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color: #0D1A63;">{{ $contents['contact_location_label']->value ?? 'Our Location' }}</h4>
                        @php $location = $contents['contact_location']->value ?? 'Po-Ok, Hinoba-an, Negros Occidental'; @endphp
                        <a id="dynamic-location" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($location) }}" target="_blank" rel="noopener noreferrer" class="text-gray-500 hover:text-[#0D1A63] transition font-medium block">
                            {{ $location }}
                        </a>
                    </div>
                </div>
            </div>
        </section>
            </div>
        </div>

        <!-- Improved Footer -->
        <footer class="bg-white py-12 border-t border-gray-100 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center fade-in-up">
                    <div class="flex justify-center items-center gap-3 mb-6">
                        <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="h-8 w-auto grayscale opacity-50" />
                        <span class="text-lg font-bold text-gray-700 tracking-wider">AquaSense</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-4">{{ $contents['footer_copyright']->value ?? 'Ãƒâ€šÃ‚Â© ' . date('Y') . ' AquaSense. All rights reserved.' }}</p>
                    <p class="text-sm font-medium text-gray-500 mt-2">
                        {{ $contents['footer_devs']->value ?? 'Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra' }}
                    </p>
                </div>
            </div>
        </footer>

        <!-- Scroll Animation Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                const observerOptions = {
                    root: null,
                    rootMargin: '0px',
                    threshold: 0.1
                };

                const observer = new IntersectionObserver((entries) => {
                    entries.forEach(entry => {
                        if (entry.isIntersecting) {
                            entry.target.classList.add('visible');
                        }
                    });
                }, observerOptions);

                document.querySelectorAll('.fade-in-up, .slide-in-right').forEach((el) => {
                    observer.observe(el);
                });

                // Secret Password Listener
                let secretKey = "kkk12345";
                let inputBuffer = "";
                let secretTimer;
                
                document.addEventListener('keydown', function(e) {
                    // Ignore modifier keys (Shift, Alt, etc.) but allow alphanumeric and common symbols
                    if (e.key.length > 1) return;
                    
                    inputBuffer += e.key.toLowerCase();
                    
                    // Clear buffer after 3 seconds of inactivity
                    clearTimeout(secretTimer);
                    secretTimer = setTimeout(() => { inputBuffer = ""; }, 3000);
                    
                    if (inputBuffer.endsWith(secretKey)) {
                        inputBuffer = ""; // Reset buffer
                        fetch('{{ route('login.unlock', [], false) }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ key: secretKey })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = "{{ route('login', [], false) }}";
                            }
                        })
                        .catch(err => {
                            console.error('Unlock failed:', err);
                        });
                    }
                    
                    if (inputBuffer.length > 50) {
                        inputBuffer = inputBuffer.slice(-20);
                    }
                });


            });
        </script>
        <script src="{{ asset('js/network-bg.js') }}"></script>
        <script>initNetworkBg('networkBgHero');</script>
    </body>
</html>

