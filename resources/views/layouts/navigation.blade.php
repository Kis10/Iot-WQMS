<nav x-data="{ open: false }" class="bg-white border-b border-gray-100">
    
    <!-- Primary Navigation Menu -->
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="flex justify-between h-16">
            <div class="flex">
                <!-- Dashboard Text -->
                <div class="shrink-0 flex items-center">
                    <span class="font-semibold text-gray-800">
                        @if(request()->routeIs('dashboard'))
                            Dashboard
                        @elseif(request()->routeIs('history'))
                            History
                        @elseif(request()->routeIs('alerts'))
                            Alerts
                        @elseif(request()->routeIs('analysis.index'))
                            AI Analysis
                        @else
                            Dashboard
                        @endif
                    </span>
                </div>
            <!-- Notification Bell (Admin Only) -->
            @if(Auth::user()->isAdmin())
                <div class="flex items-center ml-4 relative" x-data="{ 
                        notifOpen: false, 
                        count: {{ \App\Models\User::where('is_approved', false)->count() }},
                        check() {
                            fetch('/admin/approval-check')
                                .then(res => res.json())
                                .then(data => {
                                    if (data.count > this.count) {
                                        this.playAlert();
                                    }
                                    this.count = data.count;
                                });
                        },
                        playAlert() {
                            let audio = new Audio('/sounds/alert.mp3');
                            audio.play().catch(e => console.log('Audio error:', e));
                        }
                    }"
                    x-init="setInterval(() => check(), 10000)">
                    
                    <button @click="notifOpen = !notifOpen" class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none relative">
                        <span class="sr-only">View notifications</span>
                        <!-- Bell Icon -->
                        <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                        </svg>
                        
                        <!-- Red Dot -->
                        <div x-show="count > 0" class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-600"></div>
                    </button>

                    <!-- Dropdown -->
                    <div x-show="notifOpen" @click.away="notifOpen = false" class="origin-top-right absolute right-0 mt-2 w-48 rounded-md shadow-lg py-1 bg-white ring-1 ring-black ring-opacity-5 focus:outline-none z-50 transform translate-y-8" style="display: none;">
                        <div class="px-4 py-2 border-b text-sm text-gray-700">
                            Notifications
                        </div>
                        <template x-if="count > 0">
                            <a href="{{ route('admin.users.approvals') }}" class="block px-4 py-2 text-sm text-gray-700 hover:bg-gray-100 flex justify-between items-center">
                                <span><span x-text="count"></span> New User(s)</span>
                                <!-- Expand Icon -->
                                <svg class="h-4 w-4 text-gray-500" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 8V4m0 0h4M4 4l5 5m11-1V4m0 0h-4m4 0l-5 5M4 16v4m0 0h4m-4 0l5-5m11 5l-5-5m5 5v-4m0 4h-4" />
                                </svg>
                            </a>
                        </template>
                        <template x-if="count === 0">
                            <div class="px-4 py-2 text-sm text-gray-500">No new notifications</div>
                        </template>
                    </div>
                </div>
            @endif

            </div><!-- End Flex -->
            
            <!-- Settings Dropdown -->
            <div class="hidden sm:flex sm:items-center sm:ms-6">
                
                <!-- Admin Notification Bell -->
                @if(Auth::user()->isAdmin())
                    <div class="relative mr-4" x-data="{ 
                            notifOpen: false, 
                            count: {{ \App\Models\User::where('is_approved', false)->count() }},
                            check() {
                                fetch('{{ route('admin.approval.check-count') }}')
                                    .then(res => res.json())
                                    .then(data => {
                                        if (data.count > this.count) {
                                            let audio = new Audio('/sounds/alert.mp3');
                                            audio.play().catch(e => console.log('Audio error:', e));
                                        }
                                        this.count = data.count;
                                    });
                            }
                        }"
                        x-init="setInterval(() => check(), 10000)">
                        
                        <button @click="notifOpen = !notifOpen" class="p-1 rounded-full text-gray-400 hover:text-gray-500 focus:outline-none relative">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" />
                            </svg>
                            <div x-show="count > 0" class="absolute top-0 right-0 block h-2 w-2 rounded-full ring-2 ring-white bg-red-600"></div>
                        </button>

                        <div x-show="notifOpen" @click.away="notifOpen = false" class="absolute right-0 mt-2 w-64 bg-white rounded-md shadow-lg py-1 z-50 ring-1 ring-black ring-opacity-5" style="display: none;">
                            <div class="px-4 py-2 border-b text-sm font-semibold text-gray-700">Notifications</div>
                            <template x-if="count > 0">
                                <a href="{{ route('admin.users.approvals') }}" class="block px-4 py-3 text-sm text-gray-700 hover:bg-gray-100 flex items-center justify-between">
                                    <span><span x-text="count" class="font-bold text-red-600"></span> New User(s) waiting</span>
                                    <svg class="h-4 w-4 text-gray-400" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3" /></svg>
                                </a>
                            </template>
                            <template x-if="count === 0">
                                <div class="px-4 py-3 text-sm text-gray-500 text-center">No new notifications</div>
                            </template>
                        </div>
                    </div>
                @endif
                <!-- Refresh Button -->
                @if(request()->routeIs('dashboard'))

                    <button id="refreshDashboard" class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150 me-3">
                        <svg class="w-4 h-4 me-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh
                    </button>
                @endif
                
                <x-dropdown align="right" width="48">
                    <x-slot name="trigger">
                        <button class="inline-flex items-center px-3 py-2 border border-transparent text-sm leading-4 font-medium rounded-md text-gray-500 bg-white hover:text-gray-700 focus:outline-none transition ease-in-out duration-150">
                            <div>{{ Auth::user()->name }}</div>

                            <div class="ms-1">
                                <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                </svg>
                            </div>
                        </button>
                    </x-slot>

                    <x-slot name="content">
                        <x-dropdown-link :href="route('profile.edit')">
                            {{ __('Profile') }}
                        </x-dropdown-link>

                        <!-- Authentication -->
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf

                            <x-dropdown-link :href="route('logout')"
                                    onclick="event.preventDefault();
                                                this.closest('form').submit();">
                                {{ __('Log Out') }}
                            </x-dropdown-link>
                        </form>
                    </x-slot>
                </x-dropdown>
            </div>

            <!-- Hamburger -->
            <div class="-me-2 flex items-center sm:hidden">
                <button @click="open = ! open" class="inline-flex items-center justify-center p-2 rounded-md text-gray-400 hover:text-gray-500 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 focus:text-gray-500 transition duration-150 ease-in-out">
                    <svg class="h-6 w-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                        <path :class="{'hidden': open, 'inline-flex': ! open }" class="inline-flex" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                        <path :class="{'hidden': ! open, 'inline-flex': open }" class="hidden" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                    </svg>
                </button>
            </div>
        </div>
    </div>

    <!-- Responsive Navigation Menu -->
    <div :class="{'block': open, 'hidden': ! open}" class="hidden sm:hidden">
        <div class="pt-2 pb-3 space-y-1">
            <x-responsive-nav-link :href="route('dashboard')" :active="request()->routeIs('dashboard')">
                {{ __('Dashboard') }}
            </x-responsive-nav-link>
        </div>

        <!-- Responsive Settings Options -->
        <div class="pt-4 pb-1 border-t border-gray-200">
            <div class="px-4">
                <div class="font-medium text-base text-gray-800">{{ Auth::user()->name }}</div>
                <div class="font-medium text-sm text-gray-500">{{ Auth::user()->email }}</div>
            </div>

            <div class="mt-3 space-y-1">
                <!-- Refresh Button for Mobile -->
                @if(request()->routeIs('dashboard'))
                    <button id="refreshDashboardMobile" class="flex items-center w-full px-4 py-2 text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:bg-gray-100 transition duration-150 ease-in-out">
                        <svg class="w-4 h-4 me-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Refresh Dashboard
                    </button>
                @endif
                
                <x-responsive-nav-link :href="route('profile.edit')">
                    {{ __('Profile') }}
                </x-responsive-nav-link>

                <!-- Authentication -->
                <form method="POST" action="{{ route('logout') }}">
                    @csrf

                    <x-responsive-nav-link :href="route('logout')"
                            onclick="event.preventDefault();
                                        this.closest('form').submit();">
                        {{ __('Log Out') }}
                    </x-responsive-nav-link>
                </form>
            </div>
        </div>
    </div>
</nav>
