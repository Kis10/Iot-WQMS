<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            
            <!-- Users Monitoring Table -->
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Name</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Login Time</th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Logout Time</th>
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
                                                <div class="flex-shrink-0 h-2.5 w-2.5 rounded-full {{ $isOnline ? 'bg-green-500' : 'bg-gray-300' }} mr-3" title="{{ $isOnline ? 'Online' : 'Offline' }}"></div>
                                                <div class="text-sm font-medium text-gray-900">{{ $user->name }}</div>
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

                                        <!-- Column 4: Logout Time -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                            {{ ($lastActivity && $lastActivity->logout_at) ? $lastActivity->logout_at->format('M d, Y h:i A') : ($isOnline ? 'Active Now' : '-') }}
                                        </td>

                                        <!-- Column 5: Duration -->
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
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
                                            {{ $lastActivity ? $lastActivity->ip_address : '-' }}
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
</x-app-layout>
