<aside class="w-64 bg-white border-r flex flex-col h-full">
    <div class="p-6 text-xl font-bold border-b flex items-center gap-3">
        <div class="w-8 h-8 rounded-lg overflow-hidden shadow-sm bg-white p-0.5">
            <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="w-full h-full object-contain" />
        </div>
        <span>AquaSense</span>
    </div>

    <nav class="flex-1 px-4 mt-4 space-y-2">
        <a href="{{ route('dashboard') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('dashboard') ? 'font-bold bg-gray-100' : '' }}">Dashboard</a>
        <a href="{{ route('history') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('history') ? 'font-bold bg-gray-100' : '' }}">History</a>
        <a href="{{ route('alerts') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('alerts') ? 'font-bold bg-gray-100' : '' }}">Alerts</a>
        <a href="{{ route('analysis.index') }}" class="block px-4 py-2 rounded hover:bg-gray-100 {{ request()->routeIs('analysis.index') ? 'font-bold bg-gray-100' : '' }}">AI Analysis</a>
    </nav>

    <div class="p-6 border-t border-gray-200 mt-auto" x-data="{ open: false }">
        <button @click="open = !open" class="w-full flex justify-between items-center text-gray-700 font-semibold px-4 py-2 rounded hover:bg-gray-100">
            {{ Auth::user()->name }}
            <svg class="h-4 w-4 transform" :class="{ 'rotate-180': open }" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" />
            </svg>
        </button>

        <div x-show="open" @click.away="open = false" class="mt-2 bg-white border border-gray-200 rounded shadow text-gray-700">
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button type="submit" class="w-full text-left px-4 py-2 hover:bg-gray-100">Logout</button>
            </form>
        </div>
    </div>
</aside>
