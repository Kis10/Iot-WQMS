<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}" class="scroll-smooth">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>{{ config('app.name', 'AquaSense') }} - IoT Water Quality Monitoring</title>

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
            .fade-in-up {
                opacity: 1;
                transform: translateY(0);
                transition: all 0.8s cubic-bezier(0.4, 0, 0.2, 1);
            }
            .fade-in-up.visible {
                opacity: 1;
                transform: translateY(0);
            }
            .sensor-card:hover {
                transform: translateY(-10px);
                box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
            }
            .gradient-text {
                background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%);
                -webkit-background-clip: text;
                -webkit-text-fill-color: transparent;
            }
        </style>
    </head>
    <body class="antialiased text-gray-900 bg-gray-50 overflow-x-hidden">
        
        <!-- Animated Navbar -->
        <nav class="fixed top-0 w-full z-50 transition-all duration-300 glass border-b border-gray-100 py-4" x-data="{ atTop: true }" @scroll.window="atTop = (window.pageYOffset > 50 ? false : true)">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center w-full">
                    <!-- Brand (Far Left) -->
                    <div class="flex items-center gap-3">
                        <a href="/" class="flex items-center gap-3">
                            <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm bg-white p-1">
                                <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="w-full h-full object-contain" />
                            </div>
                            <span class="text-2xl font-bold tracking-tight text-gray-900">{{ config('app.name', 'AquaSense') }}</span>
                        </a>
                    </div>
                    
                    <!-- Nav Links (Right) -->
                    <div class="hidden md:flex items-center space-x-8 text-sm font-semibold ml-auto">
                        <a href="#home" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Home</a>
                        <a href="#about" class="text-gray-600 hover:text-blue-600 transition tracking-wide">About</a>
                        <a href="#features" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Sensors</a>
                        <a href="#services" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Services</a>
                        <a href="#contact" class="text-gray-600 hover:text-blue-600 transition tracking-wide">Contact</a>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Dynamic Hero Section (Refined Visibility) -->
        <!-- Dynamic Hero Section (Refined Visibility) -->
        <section id="home" class="relative min-h-[85vh] flex items-center justify-center bg-slate-900 overflow-hidden pt-32 pb-20">
            <!-- Dynamic Background Image -->
            @if(isset($contents['hero_bg']) && $contents['hero_bg']->image)
                 <div class="absolute inset-0 z-0">
                    <img src="{{ asset($contents['hero_bg']->image) }}" class="w-full h-full object-cover opacity-40">
                 </div>
            @endif
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 via-slate-900/40 to-slate-950/40 z-0"></div>
            
            <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
                <div class="fade-in-up visible">
                    <br><br>
                    <h1 class="text-5xl md:text-7xl lg:text-8xl font-black text-white mb-8 leading-tight tracking-tight">
                        {!! $contents['hero_title']->value ?? 'IoT-Based Water Quality <br> <span class="gradient-text">Monitoring System</span>' !!}
                    </h1>
                    <p class="text-xl md:text-2xl text-blue-100 mb-12 max-w-3xl mx-auto font-medium leading-relaxed opacity-90">
                        {{ $contents['hero_subtitle']->value ?? 'Ensuring a sustainable aquaculture environment through high-precision IoT sensors and real-time data analytics.' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- General Caption Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto fade-in-up">
                    <div class="inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-bold mb-6">
                        {{ $contents['mission_badge']->value ?? 'OUR MISSION' }}
                    </div>
                    <h2 class="text-4xl font-bold mb-8 tracking-tight" style="color: #0D1A63;">{{ $contents['mission_title']->value ?? 'The Future of Aquaculture Management' }}</h2>
                    <p class="text-xl text-gray-600 leading-relaxed font-light">
                        {{ $contents['mission_text']->value ?? 'Our system is designed to provide farmers with a robust, reliable, and user-friendly platform for monitoring vital aquatic conditions. By leveraging the power of IoT, we help eliminate the guesswork, reduce risks, and maximize productivity in aquaculture operations.' }}
                    </p>
                </div>
            </div>
        </section>

        <!-- Sensor Features Section -->
        <section id="features" class="py-24 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-up">
                    <h2 class="text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensors_title']->value ?? 'Integrated Sensor Technology' }}</h2>
                    <p class="text-gray-500 text-lg">{{ $contents['sensors_subtitle']->value ?? 'Our system utilizes five high-precision sensors to capture every critical metric.' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-12">
                    <!-- Sensor 1: pH -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group fade-in-up mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor1_title']->value ?? 'pH Sensor' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor1_desc']->value ?? 'Measures the acidity or alkalinity of the water to ensure a healthy environment for aquatic life.' }}</p>
                    </div>

                    <!-- Sensor 2: Turbidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group fade-in-up mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
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
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group fade-in-up mb-8">
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
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group fade-in-up mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19c-1.657 0-3-1.343-3-3V6a3 3 0 116 0v10c0 1.657-1.343 3-3 3z"></path>
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h4m-4 4h4"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor4_title']->value ?? 'Temperature' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor4_desc']->value ?? 'Tracks water temperature to prevent thermal stress and maintain optimal growth rates for fish.' }}</p>
                    </div>

                    <!-- Sensor 5: Humidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group fade-in-up mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a7 7 0 007-7c0-3.866-7-11-7-11s-7 7.134-7 11a7 7 0 007 7z"></path>
                            </svg>
                        </div>
                        <h3 class="text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['sensor5_title']->value ?? 'Humidity' }}</h3>
                        <p class="text-gray-600 text-sm leading-relaxed">{{ $contents['sensor5_desc']->value ?? 'Monitors air moisture levels around the pond, affecting evaporation and equipment safety.' }}</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section id="services" class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-up">
                    <h2 class="text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['services_title']->value ?? 'Our Services' }}</h2>
                    <p class="text-gray-500 text-lg">{{ $contents['services_subtitle']->value ?? 'We provide end-to-end solutions for aquaculture technology integration.' }}</p>
                </div>

                <div class="max-w-4xl mx-auto space-y-12 fade-in-up">
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">{{ $contents['service1_num']->value ?? '01' }}</div>
                        <div>
                            <h4 class="text-2xl font-bold mb-3 tracking-tight" style="color: #0D1A63;">{{ $contents['service1_title']->value ?? 'Automated Data Collection' }}</h4>
                            <p class="text-gray-600 leading-relaxed text-lg">{{ $contents['service1_desc']->value ?? 'Continuous background data harvesting from a pond, simultaneously without manual intervention.' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">{{ $contents['service2_num']->value ?? '02' }}</div>
                        <div>
                            <h4 class="text-2xl font-bold mb-3 tracking-tight" style="color: #0D1A63;">{{ $contents['service2_title']->value ?? 'Smart Alert Notifications' }}</h4>
                            <p class="text-gray-600 leading-relaxed text-lg">{{ $contents['service2_desc']->value ?? 'Instant Alert notifications when water parameters exceed safe threshold limits for your specific fish species.' }}</p>
                        </div>
                    </div>
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">{{ $contents['service3_num']->value ?? '03' }}</div>
                        <div>
                            <h4 class="text-2xl font-bold mb-3 tracking-tight" style="color: #0D1A63;">{{ $contents['service3_title']->value ?? 'AI Condition Analysis' }}</h4>
                            <p class="text-gray-600 leading-relaxed text-lg">{{ $contents['service3_desc']->value ?? 'Advanced algorithms that analyze patterns to predict water quality health and recommend corrective actions.' }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About / Team Section -->
        <section id="about" class="py-24 bg-gray-50 overflow-hidden border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16 fade-in-up">
                    <h2 class="text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['about_title']->value ?? 'Meet the Team' }}</h2>
                    <p class="text-gray-500 text-lg max-w-2xl mx-auto">{{ $contents['about_subtitle']->value ?? 'The dedicated minds behind AquaSense, working together to revolutionize aquaculture monitoring.' }}</p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 fade-in-up">
                    
                    <!-- Team Member 1 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-all duration-300">
                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @if(isset($contents['team1_img']) && $contents['team1_img']->image)
                                <img src="{{ asset($contents['team1_img']->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @if(isset($contents['team1_img_hover']) && $contents['team1_img_hover']->image)
                                <img src="{{ asset($contents['team1_img_hover']->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white">
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team1_name']->value ?? 'Kirstine A. Sanchez' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team1_role']->value ?? 'Web/Arduino Developer' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team1_desc']->value ?? 'Spearheads the hardware integration and full-stack web development.' }}</p>
                    </div>

                    <!-- Team Member 2 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-all duration-300">
                         <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @if(isset($contents['team2_img']) && $contents['team2_img']->image)
                                <img src="{{ asset($contents['team2_img']->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @if(isset($contents['team2_img_hover']) && $contents['team2_img_hover']->image)
                                <img src="{{ asset($contents['team2_img_hover']->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white">
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team2_name']->value ?? 'Dannica J. Besinio' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team2_role']->value ?? 'Documenter' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team2_desc']->value ?? 'Ensures comprehensive documentation of system processes and user guides.' }}</p>
                    </div>

                    <!-- Team Member 3 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-all duration-300">
                         <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @if(isset($contents['team3_img']) && $contents['team3_img']->image)
                                <img src="{{ asset($contents['team3_img']->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @if(isset($contents['team3_img_hover']) && $contents['team3_img_hover']->image)
                                <img src="{{ asset($contents['team3_img_hover']->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white">
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team3_name']->value ?? 'Joy Mae A. Samra' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team3_role']->value ?? 'Documenter' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team3_desc']->value ?? 'Focuses on research, technical writing, and system validation.' }}</p>
                    </div>

                    <!-- Team Member 4 -->
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-all duration-300">
                         <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative border-4 bg-white" style="border-color: #0D1A63;">
                            @if(isset($contents['team4_img']) && $contents['team4_img']->image)
                                <img src="{{ asset($contents['team4_img']->image) }}" class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            @endif

                            @if(isset($contents['team4_img_hover']) && $contents['team4_img_hover']->image)
                                <img src="{{ asset($contents['team4_img_hover']->image) }}" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover:opacity-100 transition-opacity duration-300 bg-white">
                            @endif
                        </div>
                        <h3 class="text-lg font-bold mb-1" style="color: #0D1A63;">{{ $contents['team4_name']->value ?? 'Jonas D. Parraño' }}</h3>
                        <p class="text-blue-600 font-medium text-xs uppercase tracking-wide mb-4">{{ $contents['team4_role']->value ?? 'System Analyst / Capstone Adviser' }}</p>
                        <p class="text-gray-500 text-sm leading-relaxed">{{ $contents['team4_desc']->value ?? 'Provides expert guidance on system architecture and project direction.' }}</p>
                    </div>

                </div>
            </div>
        </section>

        <!-- Contact Us Section -->
        <section id="contact" class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20 fade-in-up">
                    <h2 class="text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;">{{ $contents['contact_title']->value ?? 'Contact Us' }}</h2>
                    <p class="text-gray-500 text-lg">{{ $contents['contact_subtitle']->value ?? 'Have questions? We\'re here to help you optimize your aquaculture operations.' }}</p>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center fade-in-up">
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm group hover:-translate-y-2 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color: #0D1A63;">{{ $contents['contact_email_label']->value ?? 'Email Address' }}</h4>
                        @php $email = $contents['contact_email']->value ?? 'kirstinesanchez9@gmail.com'; @endphp
                        <a href="https://mail.google.com/mail/?view=cm&fs=1&to={{ $email }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 transition font-medium block">
                            {{ $email }}
                        </a>
                    </div>
                    
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm group hover:-translate-y-2 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color: #0D1A63;">{{ $contents['contact_phone_label']->value ?? 'Mobile Number' }}</h4>
                        @php $phones = explode("\n", $contents['contact_phone']->value ?? "09207327946\n09151003714"); @endphp
                        @foreach($phones as $phone)
                            <a href="tel:{{ trim($phone) }}" class="text-blue-600 hover:text-blue-800 transition font-medium block">{{ trim($phone) }}</a>
                        @endforeach
                    </div>
                    
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm group hover:-translate-y-2 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold mb-2" style="color: #0D1A63;">{{ $contents['contact_location_label']->value ?? 'Our Location' }}</h4>
                        @php $location = $contents['contact_location']->value ?? 'Po-Ok, Hinoba-an, Negros Occidental'; @endphp
                        <a id="dynamic-location" href="https://www.google.com/maps/search/?api=1&query={{ urlencode($location) }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 transition font-medium block">
                            {{ $location }}
                        </a>
                    </div>
                </div>
            </div>
        </section>

        <!-- Improved Footer -->
        <footer class="bg-white py-12 border-t border-gray-100 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex justify-center items-center gap-3 mb-6">
                        <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="h-8 w-auto grayscale opacity-50" />
                        <span class="text-lg font-bold text-gray-700 tracking-wider">AquaSense</span>
                    </div>
                    <p class="text-gray-500 text-sm mb-4">{{ $contents['footer_copyright']->value ?? '© ' . date('Y') . ' AquaSense. All rights reserved.' }}</p>
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

                document.querySelectorAll('.fade-in-up').forEach((el) => {
                    observer.observe(el);
                });

                // Secret Password Listener
                let secretKey = "kkk12345";
                let inputBuffer = "";
                
                document.addEventListener('keydown', function(e) {
                    inputBuffer += e.key;
                    
                    if (inputBuffer.endsWith('kkk12345')) {
                        // Unlock Login via POST
                        fetch('{{ route('login.unlock') }}', {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                            },
                            body: JSON.stringify({ key: 'kkk12345' })
                        })
                        .then(response => response.json())
                        .then(data => {
                            if (data.success) {
                                window.location.href = "{{ route('login') }}";
                            }
                        });
                    }
                    
                    if (inputBuffer.length > 50) {
                        inputBuffer = inputBuffer.slice(-20);
                    }
                });

                // Dynamic Location Feature
                const locationElement = document.getElementById('dynamic-location');
                if (navigator.geolocation && locationElement) {
                    locationElement.textContent = 'Locating device...';
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            const { latitude, longitude } = position.coords;
                            locationElement.href = `https://www.google.com/maps?q=${latitude},${longitude}`;
                            locationElement.textContent = `Device Location: ${latitude.toFixed(5)}, ${longitude.toFixed(5)}`;
                        },
                        (error) => {
                            console.error('Location detection failed/denied');
                            locationElement.textContent = 'Po-Ok, Hinoba-an, Negros Occidental'; 
                            locationElement.href = "https://www.google.com/maps/search/?api=1&query=Po-Ok%2C+Hinoba-an%2C+Negros+Occidental";
                        }
                    );
                }
            });
        </script>
    </body>
</html>
