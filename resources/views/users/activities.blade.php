<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 bg-white border-b border-gray-200">
                    
                    <div class="flex justify-end mb-4">
                        <a href="{{ route('users.index') }}" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">
                            &larr; Back to Users
                        </a>
                    </div>

                    @if($activities->count() > 0)
                        <div class="overflow-x-auto">
                            <table class="min-w-full divide-y divide-gray-200">
                                <thead class="bg-gray-50">
                                    <tr>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Time</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Action</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">URL</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Method</th>
                                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">IP Address</th>
                                    </tr>
                                </thead>
                                <tbody class="bg-white divide-y divide-gray-200">
                                    @foreach($activities as $activity)
                                        <tr class="hover:bg-gray-50">
                                            <!-- Time -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $activity->created_at->format('M d, Y h:i:s A') }}
                                                <div class="text-xs text-gray-400">{{ $activity->created_at->diffForHumans() }}</div>
                                            </td>

                                            <!-- Activity Description -->
                                            <td class="px-6 py-4 text-sm text-gray-900 font-medium">
                                                {{ $activity->activity }}
                                            </td>

                                            <!-- URL -->
                                            <td class="px-6 py-4 text-sm text-gray-500 max-w-xs truncate" title="{{ $activity->url }}">
                                                {{ $activity->url }}
                                            </td>

                                            <!-- Method (GET/POST) -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm">
                                                @if($activity->method === 'POST')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                        POST
                                                    </span>
                                                @elseif($activity->method === 'DELETE')
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">
                                                        DELETE
                                                    </span>
                                                @else
                                                    <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                        GET
                                                    </span>
                                                @endif
                                            </td>

                                            <!-- IP Address -->
                                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                {{ $activity->ip_address }}
                                            </td>
                                        </tr>
                                    @endforeach
                                </tbody>
                            </table>
                        </div>

                        <!-- Pagination -->
                        <div class="mt-4 flex items-center justify-center space-x-4">
                            <!-- Previous -->
                            @if ($activities->onFirstPage())
                                <span class="text-gray-400 font-bold">&lt;</span>
                            @else
                                <a href="{{ $activities->previousPageUrl() }}" class="text-gray-700 hover:text-black font-bold decoration-0">&lt;</a>
                            @endif

                            <!-- Page Info -->
                            <span class="text-sm text-gray-700 font-medium whitespace-nowrap">
                                {{ $activities->currentPage() }} out of {{ $activities->lastPage() }}
                            </span>

                            <!-- Next -->
                            @if ($activities->hasMorePages())
                                <a href="{{ $activities->nextPageUrl() }}" class="text-gray-700 hover:text-black font-bold decoration-0">&gt;</a>
                            @else
                                <span class="text-gray-400 font-bold">&gt;</span>
                            @endif
                        </div>
                    @else
                        <div class="text-center py-10">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No activities recorded</h3>
                            <p class="mt-1 text-sm text-gray-500">Wait for the user to navigate through the application.</p>
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
