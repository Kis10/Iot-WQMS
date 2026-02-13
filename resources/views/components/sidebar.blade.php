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

        <!-- Users -->
        <a href="{{ route('users.index') }}" 
           class="flex items-center px-4 py-3 rounded-lg transition duration-200 {{ (request()->routeIs('users.index') || request()->routeIs('users.activities')) ? 'bg-indigo-100 text-indigo-600 font-semibold' : 'text-gray-700 hover:bg-gray-100' }}">
            <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z" />
            </svg>
            Users
        </a>
    </nav>
</aside>
