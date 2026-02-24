<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Sorting Section -->

            
            <div class="grid grid-cols-1 gap-6 mb-8">
            <div class="bg-white rounded-xl shadow-sm border border-gray-100 overflow-hidden min-h-[600px]">
                <!-- Desktop Table View -->
                <div class="hidden md:block overflow-x-auto">
                    <table class="w-full text-left">
                        <thead class="bg-gray-50 border-b border-gray-200">
                            <tr>
                                <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Device</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Parameter</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Value</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Status</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Effect on Fish</th>
                                <th class="px-6 py-3 text-[10px] font-bold text-gray-400 uppercase tracking-widest">Date & Time</th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                            @forelse($alerts as $alert)
                                @php
                                    $alertTypes = [];
                                    if ($alert->turbidity > 5) {
                                        if ($alert->turbidity > 50) $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . ' NTU', 'status' => 'Data Critical', 'effect' => 'Extremely Muddy - Fish stress/death likely', 'severity' => 'critical'];
                                        else $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . ' NTU', 'status' => 'High Turbidity', 'effect' => 'Reduced clarity, potential gill stress', 'severity' => 'warning'];
                                    }
                                    if ($alert->tds > 500) {
                                        if ($alert->tds > 1000) $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => 'Dangerous', 'effect' => 'May cause mortality', 'severity' => 'critical'];
                                        else $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => 'High', 'effect' => 'Reduce growth, chronic stress', 'severity' => 'warning'];
                                    }
                                    if ($alert->ph < 6.5 || $alert->ph > 8.5) {
                                        if ($alert->ph < 6.0 || $alert->ph > 9.0) $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => 'Critical', 'effect' => 'Extreme pH stress - Immediate mortality risk', 'severity' => 'critical'];
                                        elseif ($alert->ph < 6.5) $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => 'Acidic', 'effect' => 'Below optimal 6.5 - Growth may slow', 'severity' => 'warning'];
                                        else $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => 'Alkaline', 'effect' => 'Above optimal 8.5 - Stress, ammonia toxicity risk', 'severity' => 'warning'];
                                    }
                                    if ($alert->temperature < 15 || $alert->temperature > 32) {
                                        if ($alert->temperature > 32) $alertTypes[] = ['param' => 'Water Temp', 'value' => $alert->temperature . '°C', 'status' => 'Dangerous', 'effect' => 'Can cause mortality quickly', 'severity' => 'critical'];
                                        elseif ($alert->temperature < 15) $alertTypes[] = ['param' => 'Water Temp', 'value' => $alert->temperature . '°C', 'status' => 'Cold', 'effect' => 'Metabolism slows, feeding decreases', 'severity' => 'warning'];
                                    }
                                @endphp
                                @foreach($alertTypes as $type)
                                    <tr class="hover:bg-gray-50/50 transition duration-150">
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">{{ $alert->device_id ?? 'N/A' }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-600">{{ $type['param'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-bold text-gray-900">{{ $type['value'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="inline-flex px-2.5 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg
                                                {{ $type['severity'] === 'critical' ? 'bg-red-50 text-red-600 border border-red-100' : 'bg-amber-50 text-amber-600 border border-amber-100' }}">
                                                {{ $type['status'] }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 text-xs text-gray-500 max-w-xs truncate">{{ $type['effect'] }}</td>
                                        <td class="px-6 py-4 whitespace-nowrap text-xs text-gray-400 font-medium">
                                            {{ $alert->created_at ? $alert->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}
                                        </td>
                                    </tr>
                                @endforeach
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-20 text-center text-gray-400">
                                        <div class="flex flex-col items-center">
                                            <svg class="w-16 h-16 text-gray-100 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9 12l2 2 4-4m9 2a9 9 0 11-18 0 9 9 0 0118 0z" />
                                            </svg>
                                            <p class="text-lg font-medium">Everything looks good!</p>
                                            <p class="text-sm">No alerts detected in the recent logs.</p>
                                        </div>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                <!-- Mobile Card View -->
                <div class="md:hidden divide-y divide-gray-100">
                    @forelse($alerts as $alert)
                        @php
                            $alertTypes = [];
                            if ($alert->turbidity > 5) $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . ' NTU', 'status' => 'Critical', 'severity' => $alert->turbidity > 50 ? 'critical' : 'warning'];
                            if ($alert->tds > 500) $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => 'Dangerous', 'severity' => $alert->tds > 1000 ? 'critical' : 'warning'];
                            if ($alert->ph < 6.5 || $alert->ph > 8.5) $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => 'Abnormal', 'severity' => ($alert->ph < 6.0 || $alert->ph > 9.0) ? 'critical' : 'warning'];
                            if ($alert->temperature < 15 || $alert->temperature > 32) $alertTypes[] = ['param' => 'Temp', 'value' => $alert->temperature . '°C', 'status' => 'Extreme', 'severity' => $alert->temperature > 32 ? 'critical' : 'warning'];
                        @endphp
                        @foreach($alertTypes as $type)
                            <div class="p-5 {{ $type['severity'] === 'critical' ? 'bg-red-50/30' : 'bg-amber-50/30' }}">
                                <div class="flex justify-between items-start mb-3">
                                    <div>
                                        <span class="text-[10px] font-bold text-gray-400 uppercase tracking-widest block mb-1">{{ $alert->device_id ?? 'N/A' }}</span>
                                        <h4 class="text-base font-bold text-gray-900">{{ $type['param'] }}: {{ $type['value'] }}</h4>
                                    </div>
                                    <span class="px-2 py-1 text-[10px] font-bold uppercase tracking-wider rounded-lg border
                                        {{ $type['severity'] === 'critical' ? 'bg-red-100 text-red-700 border-red-200' : 'bg-amber-100 text-amber-700 border-amber-200' }}">
                                        {{ $type['status'] }}
                                    </span>
                                </div>
                                <div class="flex items-center justify-between">
                                    <div class="flex items-center text-xs text-gray-500 font-medium">
                                        <svg class="w-3.5 h-3.5 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        {{ $alert->created_at ? $alert->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') : 'N/A' }}
                                    </div>
                                </div>
                            </div>
                        @endforeach
                    @empty
                        <div class="py-12 text-center text-gray-400 italic text-sm">
                            No alerts yet.
                        </div>
                    @endforelse
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
