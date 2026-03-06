<x-app-layout>
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            #printable-content, #printable-content * {
                visibility: visible;
            }
            #printable-content {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
                min-height: 100vh;
                margin: 0;
                padding: 40px;
                padding-bottom: 100px;
                box-shadow: none !important;
                border: none !important;
                overflow: visible !important;
            }
            #historyPrintFooter, #historyPrintFooter * {
                visibility: visible;
            }
            #historyPrintFooter {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 16px 40px;
                background: white;
            }
            nav, aside, footer, header {
                display: none !important;
            }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div id="printable-content" class="bg-white overflow-hidden shadow-sm sm:rounded-lg relative min-h-[800px] flex flex-col">
                <div class="p-8 bg-white border-b border-gray-200 flex-grow flex flex-col">
                    
                    <!-- Report Header -->
                    <div class="relative flex flex-col items-center mb-12 border-b-2 border-gray-100 pb-8 pt-6 header-container">
                         <!-- Fixed Logo (Visible on both Screen and Print) -->
                         <!-- Using absolute positioning for the logo to keep it top-left relative to the container -->
                        <div class="absolute top-0 left-0 flex items-center gap-3">
                            <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" class="w-10 h-10 object-contain">
                            <span class="text-xl font-bold text-gray-700 tracking-wider">AQUASENSE</span>
                        </div>
                        
                        <!-- Centered Title -->
                        <div class="w-full text-center mt-12">
                            <h3 class="text-base font-bold text-gray-900 tracking-wide text-center">
                                IoT-based Water Quality Monitoring System for <br>
                                <span class="block text-center mt-1">Aquaculture</span>
                            </h3>
                        </div>
                    </div>

                    <!-- Info: Device & Date -->
                    <div class="mb-8 pl-2">
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold text-gray-800 w-20 inline-block">Device:</span> {{ $reading->device_id }}</p>
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold text-gray-800 w-20 inline-block">Date:</span> {{ $reading->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') }}</p>
                        <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800 w-20 inline-block">Location:</span> {{ $location }}</p>
                    </div>

                    <!-- Data Table -->
                    <div class="overflow-hidden rounded-lg border border-gray-200 mb-8">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Parameter</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Value</th>
                                    <th scope="col" class="px-6 py-4 text-left text-xs font-semibold text-gray-500 uppercase tracking-wider">Status</th>
                                </tr>
                            </thead>
                            <tbody class="bg-white divide-y divide-gray-200">
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Turbidity</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->turbidity }}% <span class="text-gray-400 text-xs font-normal ml-1 italic">(Standard: 50-100%)</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = $reading->turbidity_status;
                                            $color = $status === 'Critical' ? 'bg-red-100 text-red-800' : ($status === 'Warning' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">{{ $status }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TDS</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->tds }} ppm <span class="text-gray-400 text-xs font-normal ml-1 italic">(Standard: 0-500)</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = $reading->tds_status;
                                            $color = $status === 'Critical' ? 'bg-red-100 text-red-800' : ($status === 'Warning' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">{{ $status }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">pH Level</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->ph }} <span class="text-gray-400 text-xs font-normal ml-1 italic">(Standard: 6.5-8.5)</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = $reading->ph_status;
                                            $color = $status === 'Critical' ? 'bg-red-100 text-red-800' : ($status === 'Warning' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">{{ $status }}</span>
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Water Temp</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->temperature }}°C <span class="text-gray-400 text-xs font-normal ml-1 italic">(Standard: 25-32°C)</span></td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @php
                                            $status = $reading->temperature_status;
                                            $color = $status === 'Critical' ? 'bg-red-100 text-red-800' : ($status === 'Warning' ? 'bg-amber-100 text-amber-800' : 'bg-green-100 text-green-800');
                                        @endphp
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full {{ $color }}">{{ $status }}</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- AI Analysis Section -->
                    @if($reading->waterAnalyses->isNotEmpty())
                        @php
                            $analysis = $reading->waterAnalyses->first();
                            
                            // Re-calculate the Peak Target WQI directly in PHP for historical printouts
                            $ph = (float)($reading->ph ?? 0);
                            $temp = (float)($reading->temperature ?? 0);
                            $turb = (float)($reading->turbidity ?? 0);
                            $tds = (float)($reading->tds ?? 0);
                            
                            $scorePH = $ph <= 7.5 ? max(0, ($ph / 7.5) * 100) : max(0, ((14.0 - $ph) / 6.5) * 100);
                            $scoreTemp = $temp <= 28 ? max(0, ($temp / 28) * 100) : max(0, ((56 - $temp) / 28) * 100);
                            $scoreTurb = max(0, min(100, $turb));
                            $scoreTDS = max(0, 100 - ($tds / 10));
                            
                            $wqiRaw = min(100, max(0, 
                                $scorePH * 0.30 + $scoreTemp * 0.25 + $scoreTurb * 0.25 + $scoreTDS * 0.20
                            ));
                            $wqiScore = round($wqiRaw);
                            
                            $wqiColor = '#ef4444';
                            $wqiMsg = 'Critical water quality — dangerous conditions for aquaculture, urgent intervention required.';
                            if ($wqiScore >= 90) {
                                $wqiColor = '#10b981';
                                $wqiMsg = 'Excellent water quality — all parameters near perfect peak targets for aquaculture.';
                            } elseif ($wqiScore >= 70) {
                                $wqiColor = '#22c55e';
                                $wqiMsg = 'Good water quality — minor deviations from perfect targets, suitable for fish growth.';
                            } elseif ($wqiScore >= 50) {
                                $wqiColor = '#f59e0b';
                                $wqiMsg = 'Fair water quality — some parameters deviating notably from targets, action recommended.';
                            } elseif ($wqiScore >= 25) {
                                $wqiColor = '#f97316';
                                $wqiMsg = 'Poor water quality — multiple parameters far from safe limits, immediate action needed.';
                            }
                            
                            // Build the final display HTML
                            $cleanInsight = "Overall Water Quality: <span style='font-weight:800;color:{$wqiColor}'>{$wqiScore}%</span> - {$wqiMsg}";
                            
                            $recommendations = $analysis->recommendations ?? [];
                            $topRec = is_array($recommendations) && count($recommendations) > 0 ? $recommendations[0] : null;
                        @endphp
                        <div class="mb-8 p-6 bg-blue-50 rounded-xl border border-blue-100">
                            <h4 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Analyzed by AquaSense
                            </h4>
                            <p class="text-gray-700 leading-relaxed font-medium">
                                {!! $cleanInsight !!}
                            </p>
                            @if($topRec)
                                <div class="mt-4 pt-4 border-t border-blue-200/50">
                                    <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Recommendation:</h5>
                                    <p class="text-sm text-blue-900/80 pl-1">{{ $topRec }}</p>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                </div>
            </div>
        </div>
    </div>

    <!-- Footer (fixed at bottom of printed page) -->
    <div id="historyPrintFooter" style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 16px;">
        <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px;">
            <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" style="width: 20px; height: 20px; object-fit: contain; opacity: 0.7;">
            <span style="font-size: 16px; font-weight: 700; color: #374151; letter-spacing: 1px;">AquaSense</span>
        </div>
        <p style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">© 2026 AquaSense. All rights reserved.</p>
        <p style="font-size: 12px; color: #6b7280; font-weight: 500;">Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra</p>
    </div>
</x-app-layout>
