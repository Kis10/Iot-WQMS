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
                margin: 0;
                padding: 40px; /* Increased padding provided space */
                box-shadow: none !important;
                border: none !important;
                overflow: visible !important;
            }
            /* Hide Sidebar, Header, Footer, and other UI elements explicitly just in case */
            nav, aside, footer, header {
                display: none !important;
            }
            /* Ensure background colors print */
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }

            /* Ensure background colors print */
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
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->turbidity }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reading->turbidity > 25)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Critical</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">TDS</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->tds }} ppm</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reading->tds > 500)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Critical</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">pH Level</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->ph }}</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reading->ph < 6.0 || $reading->ph > 8.0)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Critical</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Temperature</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->temperature }}°C</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($reading->temperature < 15 || $reading->temperature > 32)
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-red-100 text-red-800">Critical</span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">Normal</span>
                                        @endif
                                    </td>
                                </tr>
                                <tr class="hover:bg-gray-50">
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">Humidity</td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">{{ $reading->humidity }}%</td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">Info</span>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- AI Analysis Section -->
                    @if($reading->waterAnalyses->isNotEmpty())
                        <div class="mb-8 p-6 bg-blue-50 rounded-xl border border-blue-100">
                            <h4 class="text-sm font-bold text-blue-900 uppercase tracking-wider mb-2 flex items-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                                Analyzed by AI
                            </h4>
                            <p class="text-gray-700 leading-relaxed font-medium">
                                {{ $reading->waterAnalyses->first()->ai_insight }}
                            </p>
                            @if($reading->waterAnalyses->first()->recommendations)
                                <div class="mt-4 pt-4 border-t border-blue-200/50">
                                    <h5 class="text-xs font-bold text-blue-800 uppercase mb-2">Recommendations:</h5>
                                    <ul class="list-disc list-inside space-y-1 text-sm text-blue-900/80">
                                        @foreach($reading->waterAnalyses->first()->recommendations as $rec)
                                            <li>{{ $rec }}</li>
                                        @endforeach
                                    </ul>
                                </div>
                            @endif
                        </div>
                    @endif
                    
                    <!-- Footer -->
                    <div class="mt-auto text-center border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" class="w-6 h-6 object-contain opacity-70">
                            <span class="text-lg font-bold text-gray-700 tracking-wider">AquaSense</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">© 2026 AquaSense. All rights reserved.</p>
                        <p class="text-sm text-gray-500 font-medium">Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
