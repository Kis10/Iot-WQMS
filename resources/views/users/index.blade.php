<x-app-layout>
<div x-data="userManagement()">
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Users Monitoring Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="{{ Auth::user()->isAdmin() ? 'overflow-x-auto' : '' }}">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logout Time</th>
                                    {{-- No Extra Status Column --}}
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Duration</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                @foreach($users as $user)
                                    @php
                                        // Get Latest Activity
                                        $lastActivity = $user->loginActivities->last();
                                        $isOnline = $user->isOnline();
                                    @endphp
                                    <tr 
                                        id="user-row-{{ $user->id }}"
                                        @if(Auth::user()->isAdmin()) 
                                            onclick="window.location='{{ route('users.activities', $user) }}'" 
                                            class="cursor-pointer hover:bg-gray-50 transition duration-150 ease-in-out"
                                        @else
                                            class="hover:bg-gray-50 transition duration-150 ease-in-out"
                                        @endif
                                    >
                                        <!-- Column 1: Status Dot + Name -->
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="flex items-center">
                                                <div class="flex-shrink-0 h-2.5 w-2.5 rounded-full mr-3" 
                                                     :class="(blockedUsers.includes({{ $user->id }}) || {{ $user->isBlocked() ? 'true' : 'false' }}) ? 'bg-gray-300' : ({{ $isOnline ? 'true' : 'false' }} ? 'bg-green-500' : 'bg-gray-300')" 
                                                     title="{{ $isOnline ? 'Online' : 'Offline' }}"></div>
                                                <div>
                                                    <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
                                                    {{-- Label Removed --}}
                                                </div>
                                            </div>
                                        </td>

                                        <!-- Column 2: Email -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $user->email }}
                                        </td>
                                        
                                        <!-- Column 3: Login Time -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ $lastActivity ? $lastActivity->login_at->format('M d, Y h:i A') : 'Never' }}
                                        </td>

                                        <!-- Dynamic Blocked Status / Normal Columns -->
                                        
                                        <!-- Merged Cells for Blocked State -->
                                        <td colspan="2" 
                                            class="px-6 py-4 whitespace-nowrap text-center text-xs font-medium text-gray-500 uppercase tracking-wider"
                                            x-show="blockedUsers.includes({{ $user->id }}) || {{ $user->isBlocked() ? 'true' : 'false' }}">
                                            ACCOUNT HAS BEEN BLOCKED
                                        </td>

                                        <!-- Normal Column: Logout Time -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            x-show="!(blockedUsers.includes({{ $user->id }}) || {{ $user->isBlocked() ? 'true' : 'false' }})">
                                            {{ ($lastActivity && $lastActivity->logout_at) ? $lastActivity->logout_at->format('M d, Y h:i A') : ($isOnline ? 'Active Now' : '-') }}
                                        </td>

                                        <!-- Normal Column: Duration -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500"
                                            x-show="!(blockedUsers.includes({{ $user->id }}) || {{ $user->isBlocked() ? 'true' : 'false' }})">
                                            @if($lastActivity && $lastActivity->duration_minutes)
                                                {{ $lastActivity->duration_minutes }} mins
                                            @elseif($isOnline)
                                                <span class="text-green-600 font-semibold">Monitoring...</span>
                                            @else
                                                -
                                            @endif
                                        </td>

                                        <!-- Column 6: IP Address -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            <div class="flex items-center justify-between">
                                                <span>{{ $lastActivity ? $lastActivity->ip_address : '-' }}</span>
                                                @if(Auth::user()->isAdmin())
                                                    <div class="relative" x-data="{ open: false }" @click.stop @click.outside="open = false">
                                                        <button @click="open = !open" class="text-gray-400 hover:text-gray-600 focus:outline-none p-1 bg-gray-100 rounded-full">
                                                            <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20">
                                                                <path d="M10 6a2 2 0 110-4 2 2 0 010 4zM10 12a2 2 0 110-4 2 2 0 010 4zM10 18a2 2 0 110-4 2 2 0 010 4z" />
                                                            </svg>
                                                        </button>
                                                        <div x-show="open" 
                                                             x-transition:enter="transition ease-out duration-100"
                                                             x-transition:enter-start="transform opacity-0 scale-95"
                                                             x-transition:enter-end="transform opacity-100 scale-100"
                                                             x-transition:leave="transition ease-in duration-75"
                                                             x-transition:leave-start="transform opacity-100 scale-100"
                                                             x-transition:leave-end="transform opacity-0 scale-95"
                                                             class="absolute right-0 mt-2 w-48 bg-white rounded-md shadow-lg z-50 py-1 border border-gray-100" 
                                                             style="display: none;">
                                                             <a href="#" @click.prevent.stop="open = false; confirmBlock({{ $user->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-red-50 hover:text-red-700">Block</a>
                                                             <a href="#" @click.prevent.stop="open = false; confirmRemove({{ $user->id }})" class="block px-4 py-2 text-sm text-gray-700 hover:bg-yellow-50 hover:text-yellow-700">Remove Account</a>
                                                        </div>
                                                    </div>
                                                @endif
                                            </div>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Notification -->
    <div x-show="showNotification" x-transition 
         class="fixed bottom-4 right-4 bg-gray-800 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center">
        <span x-text="notificationMessage"></span>
    </div>

    <!-- Block Modal -->
    <div x-show="showBlockModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="cancelBlock">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-red-100 sm:mx-0 sm:h-10 sm:w-10">
                            <!-- Warning Icon -->
                            <svg class="h-6 w-6 text-red-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Block Account</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to block this account? This action cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                <!-- Loading Overlay -->
                <div x-show="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="processBlock" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-red-600 text-base font-medium text-white hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Yes
                    </button>
                    <button type="button" @click="cancelBlock" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        No
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Remove Modal -->
    <div x-show="showRemoveModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="cancelRemove">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-yellow-100 sm:mx-0 sm:h-10 sm:w-10">
                             <svg class="h-6 w-6 text-yellow-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left">
                            <h3 class="text-lg leading-6 font-medium text-gray-900">Remove Account</h3>
                            <div class="mt-2">
                                <p class="text-sm text-gray-500">
                                    Are you sure you want to remove this account? This cannot be undone.
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
                 <div x-show="isLoading" class="absolute inset-0 bg-white bg-opacity-75 flex items-center justify-center z-10">
                    <svg class="animate-spin h-8 w-8 text-indigo-600" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="processRemove" 
                            class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-600 text-base font-medium text-white hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-yellow-500 sm:ml-3 sm:w-auto sm:text-sm">
                        Yes
                    </button>
                    <button type="button" @click="cancelRemove" 
                            class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        No
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    function userManagement() {
        return {
            showBlockModal: false,
            showRemoveModal: false,
            selectedUserId: null,
            isLoading: false,
            showNotification: false,
            notificationMessage: '',
            blockedUsers: [],

            confirmBlock(id) {
                this.selectedUserId = id;
                this.showBlockModal = true;
            },
            cancelBlock() {
                this.showBlockModal = false;
                this.showMessage('Action declined.');
            },
            processBlock() {
                this.isLoading = true;
                setTimeout(() => {
                    fetch(`/admin/block/${this.selectedUserId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isLoading = false;
                        this.showBlockModal = false;
                        this.showMessage('Account has been blocked'); // "Action granted!" replaced per visual requirement context, but user said "Account has been blocked" label on row
                        // Actually user said show message "Action granted!". The label is on the row.
                        this.showMessage('Action granted!');
                        if (!this.blockedUsers.includes(this.selectedUserId)) {
                            this.blockedUsers.push(this.selectedUserId);
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false;
                        this.showBlockModal = false; // Close on error?
                    });
                }, 2000); // 2s loading
            },

            confirmRemove(id) {
                this.selectedUserId = id;
                this.showRemoveModal = true;
            },
            cancelRemove() {
                this.showRemoveModal = false;
                this.showMessage('Action declined.');
            },
            processRemove() {
                this.isLoading = true;
                setTimeout(() => {
                    fetch(`/admin/remove/${this.selectedUserId}`, {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': '{{ csrf_token() }}',
                            'Content-Type': 'application/json'
                        }
                    })
                    .then(res => res.json())
                    .then(data => {
                        this.isLoading = false;
                        this.showRemoveModal = false;
                        this.showMessage('Action granted!');
                        const row = document.getElementById(`user-row-${this.selectedUserId}`);
                        if (row) row.remove();
                    })
                    .catch(err => {
                        console.error(err);
                        this.isLoading = false;
                        this.showRemoveModal = false;
                    });
                }, 2000);
            },
            
            showMessage(msg) {
                this.notificationMessage = msg;
                this.showNotification = true;
                setTimeout(() => {
                    this.showNotification = false;
                }, 3000);
            }
        }
    }
</script>
</div>
</x-app-layout>
