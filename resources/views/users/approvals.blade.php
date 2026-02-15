<x-app-layout>
<div x-data="approvalManagement()">
    {{-- Header Removed --}}

    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Success Message -->
            @if(session('success'))
                <div x-data="{ show: true }" x-show="show" x-init="setTimeout(() => show = false, 3000)" 
                     class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4">
                    <strong class="font-bold">{{ session('success') }}</strong>
                </div>
            @endif

            <div class="bg-white shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <h3 class="text-lg font-medium text-gray-900 mb-4">Users Waiting for Approval</h3>
                    
                    @if($pendingUsers->count() > 0)
                        <div>
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registered At</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date Modified</th>
                                        <th class="px-6 py-3 text-center text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($pendingUsers as $user)
                                        <tr id="approval-row-{{ $user->id }}">
                                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                {{ $user->name }}
                                                @if($user->isRemoved())
                                                    <span class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-red-100 text-red-800">
                                                        Account Removed
                                                    </span>
                                                @endif
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->email }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->created_at->diffForHumans() }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $user->updated_at->format('M d, Y h:i A') }}
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap text-center text-sm font-medium">
                                                <div class="flex items-center justify-center gap-3">
                                                    <!-- Approve (Check) -->
                                                    <button @click="processApprove({{ $user->id }})" class="p-1.5 rounded-full bg-green-50 text-green-600 hover:bg-green-100 hover:text-green-700 transition" title="Approve">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"></path>
                                                        </svg>
                                                    </button>
                                                    <!-- Deny (X) -->
                                                    <button @click="processDeny({{ $user->id }})" class="p-1.5 rounded-full bg-red-50 text-red-500 hover:bg-red-100 hover:text-red-700 transition" title="Deny">
                                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M6 18L18 6M6 6l12 12"></path>
                                                        </svg>
                                                    </button>
                                                </div>
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>
                    @else
                        <div class="text-center py-10 text-gray-500">
                            No pending approvals at the moment.
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</x-app-layout>

<div x-data="approvalManagement()" x-cloak>
    <!-- Notification -->
    <div x-show="showNotification" x-transition 
         class="fixed bottom-4 right-4 bg-green-600 text-white px-6 py-3 rounded-lg shadow-xl z-50 flex items-center">
        <span x-text="notificationMessage" class="font-bold"></span>
    </div>

    <!-- Details Modal -->
    <div x-show="showDetailsModal" class="fixed inset-0 z-50 overflow-y-auto" style="display: none;">
        <div class="flex items-center justify-center min-h-screen px-4 pt-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 transition-opacity" aria-hidden="true" @click="showDetailsModal = false">
                <div class="absolute inset-0 bg-gray-500 opacity-75"></div>
            </div>

            <div class="inline-block align-bottom bg-white rounded-lg text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                    <div class="sm:flex sm:items-start">
                        <div class="mx-auto flex-shrink-0 flex items-center justify-center h-12 w-12 rounded-full bg-blue-100 sm:mx-0 sm:h-10 sm:w-10">
                            <svg class="h-6 w-6 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                            </svg>
                        </div>
                        <div class="mt-3 text-center sm:mt-0 sm:ml-4 sm:text-left w-full">
                            <h3 class="text-lg leading-6 font-medium text-gray-900" id="modal-title">
                                User Details
                            </h3>
                            <div class="mt-4 space-y-3">
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-500">Name:</span>
                                    <span class="font-medium" x-text="details.name"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-500">Email:</span>
                                    <span class="font-medium" x-text="details.email"></span>
                                </div>
                                <div class="flex justify-between border-b pb-2">
                                    <span class="text-gray-500">Registered/Login At:</span>
                                    <span class="font-medium" x-text="details.date"></span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                    <button type="button" @click="showDetailsModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                        Close
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Loading Overlay -->
    <div x-show="isLoading" class="fixed inset-0 z-50 flex items-center justify-center bg-gray-900 bg-opacity-50" style="display: none;">
        <svg class="animate-spin h-10 w-10 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
            <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
            <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
        </svg>
    </div>

</div>

<script>
    function approvalManagement() {
        return {
            showDetailsModal: false,
            isLoading: false,
            showNotification: false,
            notificationMessage: '',
            details: { name: '', email: '', date: '' },

            showDetails(name, email, date) {
                this.details = { name, email, date };
                this.showDetailsModal = true;
            },

            processApprove(id) {
                this.isLoading = true;
                fetch(`/admin/approve/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => {
                    if (res.ok) {
                        this.successAction(id, 'Approved successfully!');
                    }
                })
                .catch(e => {
                    this.isLoading = false;
                    console.error(e);
                });
            },

            processDeny(id) {
                this.isLoading = true;
                fetch(`/admin/deny/${id}`, {
                    headers: { 'X-Requested-With': 'XMLHttpRequest' }
                })
                .then(res => {
                    if (res.ok) {
                        this.successAction(id, 'Denied successfully!');
                    }
                })
                .catch(e => {
                    this.isLoading = false;
                    console.error(e);
                });
            },

            successAction(id, msg) {
                this.isLoading = false;
                this.notificationMessage = msg;
                this.showNotification = true;
                
                // Remove row
                const row = document.getElementById(`approval-row-${id}`);
                if(row) row.remove();

                setTimeout(() => {
                    this.showNotification = false;
                    // Check if table empty to show "No pending approvals"
                    const tbody = document.querySelector('tbody');
                    if (tbody && tbody.children.length === 0) {
                        window.location.reload();
                    }
                }, 2000);
            }
        }
    }
</script>
