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
                                    if ($alert->turbidity_status !== 'Normal') {
                                        $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . '%', 'status' => $alert->turbidity_status, 'effect' => $alert->turbidity_status === 'Critical' ? 'Extremely Muddy - Fish death likely' : 'Poor Clarity - Slower growth, gill stress', 'severity' => strtolower($alert->turbidity_status)];
                                    }
                                    if ($alert->tds_status !== 'Normal') {
                                        $effect = $alert->tds_status === 'Critical' ? 'Extreme mineralization (>1500mg/L)' : 'Above typical freshwater (500-1500mg/L)';
                                        $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => $alert->tds_status, 'effect' => $effect, 'severity' => strtolower($alert->tds_status)];
                                    }
                                    if ($alert->ph_status !== 'Normal') {
                                        $effect = $alert->ph < 6.5 ? 'Below optimal 6.5 - Growth may slow' : ($alert->ph_status === 'Critical' ? 'Extreme pH stress - Immediate mortality risk' : 'Above optimal 8.5 - Stress, ammonia toxicity risk');
                                        $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => $alert->ph_status, 'effect' => $effect, 'severity' => strtolower($alert->ph_status)];
                                    }
                                    if ($alert->temperature_status !== 'Normal') {
                                        $effect = $alert->temperature < 25 ? 'Below optimal 25°C - Metabolism slowing' : ($alert->temperature_status === 'Critical' ? 'Extreme deviation - Stress/Mortality risk' : 'Above optimal 32°C - Oxygen dropping');
                                        $alertTypes[] = ['param' => 'Water Temp', 'value' => $alert->temperature . '°C', 'status' => $alert->temperature_status, 'effect' => $effect, 'severity' => strtolower($alert->temperature_status)];
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
                            if ($alert->turbidity_status !== 'Normal') $alertTypes[] = ['param' => 'Turbidity', 'value' => $alert->turbidity . '%', 'status' => $alert->turbidity_status, 'severity' => strtolower($alert->turbidity_status)];
                            if ($alert->tds_status !== 'Normal') $alertTypes[] = ['param' => 'TDS', 'value' => $alert->tds . ' mg/L', 'status' => $alert->tds_status, 'severity' => strtolower($alert->tds_status)];
                            if ($alert->ph_status !== 'Normal') $alertTypes[] = ['param' => 'pH Level', 'value' => $alert->ph, 'status' => $alert->ph_status, 'severity' => strtolower($alert->ph_status)];
                            if ($alert->temperature_status !== 'Normal') $alertTypes[] = ['param' => 'Water Temp', 'value' => $alert->temperature . '°C', 'status' => $alert->temperature_status, 'severity' => strtolower($alert->temperature_status)];
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
