<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Sorting Section -->
            <div class="mb-4 flex flex-col sm:flex-row justify-between items-center bg-white p-4 rounded-lg shadow">
                <h2 class="text-lg font-semibold text-gray-800 mb-2 sm:mb-0">Alert Log</h2>
                <form method="GET" action="{{ route('alerts') }}" class="flex flex-col sm:flex-row items-center gap-4">
                    <label class="text-sm font-medium text-gray-700">Sort By:</label>
                    <div class="flex items-center gap-2">
                         <select name="sort_by" class="border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                            <option value="created_at" {{ request('sort_by') == 'created_at' ? 'selected' : '' }}>Date & Time</option>
                            <option value="ph" {{ request('sort_by') == 'ph' ? 'selected' : '' }}>pH Level</option>
                            <option value="turbidity" {{ request('sort_by') == 'turbidity' ? 'selected' : '' }}>Turbidity</option>
                            <option value="tds" {{ request('sort_by') == 'tds' ? 'selected' : '' }}>TDS</option>
                            <option value="temperature" {{ request('sort_by') == 'temperature' ? 'selected' : '' }}>Temperature</option>
                        </select>
                        <select name="order" class="border-gray-300 text-sm focus:border-red-500 focus:ring-red-500 rounded-md shadow-sm">
                            <option value="desc" {{ request('order', 'desc') == 'desc' ? 'selected' : '' }}>Desc</option>
                            <option value="asc" {{ request('order') == 'asc' ? 'selected' : '' }}>Asc</option>
                        </select>
                    </div>
                    <button type="submit" class="bg-red-600 text-white text-sm px-3 py-2 rounded hover:bg-red-700">
                        Sort
                    </button>
                    @if(request()->has('sort_by'))
                        <a href="{{ route('alerts') }}" class="text-sm text-gray-500 hover:text-gray-700 underline">Clear</a>
                    @endif
                </form>
            </div>
            
            <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Device</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Parameter</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Value</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Status</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Effect on Fish</th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($alerts as $alert)
                                @php
                                    $alertTypes = [];
                                    $severityLevel = 'info';
                                    
                                    // Check each parameter
                                    // Clarity % Logic (100 = Clear, 0 = Dirty)
                                    // Alert if Clarity is LOW (below 50%)
                                    if ($alert->turbidity < 50) {
                                        if ($alert->turbidity < 20) {
                                            $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . '%', 'status' => 'Data Critical', 'effect' => 'Extremely Muddy - Fish death likely', 'severity' => 'critical'];
                                            $severityLevel = 'critical';
                                        } else {
                                            $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . '%', 'status' => 'Poor Clarity', 'effect' => 'Slower growth, gill stress', 'severity' => 'warning'];
                                        }
                                    }
                                    
                                    if ($alert->tds > 500) {
                                        if ($alert->tds > 1000) {
                                            $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => 'Dangerous', 'effect' => 'May cause mortality', 'severity' => 'critical'];
                                            $severityLevel = 'critical';
                                        } else {
                                            $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => 'High', 'effect' => 'Reduce growth, chronic stress', 'severity' => 'warning'];
                                        }
                                    }
                                    
                                    if ($alert->ph < 5.0 || $alert->ph > 9.0) {
                                        if ($alert->ph < 5.0) {
                                            $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => 'Acidic', 'effect' => 'Growth may slow, risk of death', 'severity' => 'warning'];
                                        } else {
                                            $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => 'Alkaline', 'effect' => 'Stress, ammonia toxicity rises', 'severity' => 'warning'];
                                        }
                                    }
                                    
                                    if ($alert->temperature < 15 || $alert->temperature > 32) {
                                        if ($alert->temperature > 32) {
                                            $alertTypes[] = ['param' => 'Temperature', 'value' => $alert->temperature . '°C', 'status' => 'Dangerous', 'effect' => 'Can cause mortality quickly', 'severity' => 'critical'];
                                            $severityLevel = 'critical';
                                        } elseif ($alert->temperature < 15) {
                                            $alertTypes[] = ['param' => 'Temperature', 'value' => $alert->temperature . '°C', 'status' => 'Cold', 'effect' => 'Metabolism slows, feeding decreases', 'severity' => 'warning'];
                                        }
                                    }
                                @endphp
                                
                                @foreach($alertTypes as $type)
                                    <tr class="hover:bg-gray-50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">{{ $alert->device_id ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $type['param'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $type['value'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-3 py-1 text-xs font-medium rounded-full 
                                                {{ $type['severity'] === 'critical' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800' }}">
                                                {{ $type['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-sm text-gray-700">{{ $type['effect'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $alert->created_at ? $alert->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}</td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-gray-500 text-sm">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m9 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            No alerts detected. Your water quality is within normal parameters.
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>


                
            </div>

          
            <!-- Pagination -->
            @if(isset($alerts) && method_exists($alerts, 'links'))
                <div class="mt-6 flex items-center justify-center gap-2 text-sm text-gray-700">
                    <a href="{{ $alerts->previousPageUrl() ?? $alerts->url(1) }}"
                        class="px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                        &lt;
                    </a>
                    <span class="px-2 py-1 text-gray-700">
                        {{ $alerts->currentPage() }} out of {{ $alerts->lastPage() }}
                    </span>
                    <a href="{{ $alerts->nextPageUrl() ?? $alerts->url($alerts->lastPage()) }}"
                        class="px-2 py-1 rounded border border-gray-300 text-gray-600 hover:bg-gray-50">
                        &gt;
                    </a>
                </div>
            @endif
        </div>
    </div>
</x-app-layout>
