<aside class="w-64 bg-white border-r border-gray-200 flex flex-col h-screen shadow-sm">
    <!-- Brand / Title -->
    <div class="p-6 border-b border-gray-200">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 flex items-center justify-center">
                <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" class="w-full h-full object-contain">
            </div>
            <div class="leading-tight">
                <h1 class="text-xl font-bold text-gray-900 tracking-tight break-words">{{ config('app.name', 'AquaSense') }}</h1>
            </div>
        </div>
    </div>

    <!-- Navigation Links -->
    <nav class="flex-1 px-3 mt-6 space-y-2">
        <!-- Dashboard -->
        <a href="{{ route('dashboard') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition duration-200 {{ request()->routeIs('dashboard') ? 'bg-indigo-100 text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-3m0 0l7-4 7 4M5 9v10a1 1 0 001 1h12a1 1 0 001-1V9m-9 16l-7-4m0 0V5m7 4l7-4" />
            </svg>
            Dashboard
        </a>

        <!-- History -->
        <a href="{{ route('history') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition duration-200 {{ request()->routeIs('history') ? 'bg-indigo-100 text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
            </svg>
            History
        </a>

        <!-- Alerts -->
        <a href="{{ route('alerts') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition duration-200 {{ request()->routeIs('alerts') ? 'bg-indigo-100 text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
            </svg>
            Alerts
        </a>



            <!-- Landing Page CMS -->
            <a href="{{ route('admin.landing.index') }}" 
               class="flex items-center px-4 py-3 rounded-lg transition duration-200 {{ request()->routeIs('admin.landing.index') ? 'bg-indigo-100 text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6" />
                </svg>
                Landing Page
            </a>

            <!-- Networking Dropdown -->
            <div x-data="{ open: {{ (request()->routeIs('admin.firmware.*')) ? 'true' : 'false' }} }" class="space-y-1">
                <button @click="open = !open" 
                        class="flex w-full items-center justify-between px-4 py-3 rounded-lg transition duration-200 text-gray-700 hover:bg-gray-100 focus:outline-none">
                    <div class="flex items-center">
                        <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.111 16.404a5.5 5.5 0 017.778 0M12 20h.01m-7.08-7.071c3.904-3.905 10.236-3.905 14.141 0M1.394 9.393c5.857-5.857 15.355-5.857 21.213 0" />
                        </svg>
                        Networking
                    </div>
                    <svg :class="{'rotate-180': open}" class="w-4 h-4 transition-transform duration-200" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
                    </svg>
                </button>
                
                <div x-show="open" x-transition.origin.top class="pl-11 pr-4 space-y-1">
                    <a href="{{ route('admin.firmware.wifi') }}" 
                       class="block py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('admin.firmware.wifi') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                        Wi-Fi Credentials
                    </a>
                    <a href="{{ route('admin.firmware.api') }}" 
                       class="block py-2 text-sm rounded-md transition duration-200 {{ request()->routeIs('admin.firmware.api') ? 'text-indigo-600 font-semibold' : 'text-gray-600 hover:text-gray-900' }}">
                        API Configuration
                    </a>
                </div>
            </div>
    </nav>
</aside>
