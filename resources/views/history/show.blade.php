<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Reading Details') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg relative min-h-[800px] flex flex-col">
                <div class="p-8 bg-white border-b border-gray-200 flex-grow flex flex-col">
                    
                    <!-- Top Actions: Buttons -->
                    <div class="flex justify-end mb-8 print:hidden">
                        <div class="flex gap-2">
                            <a href="{{ route('history') }}" class="px-4 py-2 border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 flex items-center shadow-sm">
                                Back
                            </a>
                            <button onclick="window.print()" class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700 flex items-center shadow-md">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
                                </svg>
                                Print
                            </button>
                        </div>
                    </div>

                    <!-- Report Header -->
                    <div class="flex flex-col md:flex-row justify-between items-center mb-8 border-b-2 border-gray-100 pb-6">
                        <div class="flex items-center gap-3 mb-4 md:mb-0">
                            <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" class="w-12 h-12 object-contain">
                            <span class="text-2xl font-bold text-gray-700 tracking-wider">AQUASENSE</span>
                        </div>
                        <div class="max-w-md text-center md:text-right">
                            <h3 class="text-sm font-semibold text-gray-900 uppercase tracking-wide text-justify">IoT-based Water Quality Monitoring System for Aquaculture</h3>
                        </div>
                    </div>

                    <!-- Info: Device & Date -->
                    <div class="mb-8 pl-2">
                        <p class="text-sm text-gray-600 mb-1"><span class="font-semibold text-gray-800 w-20 inline-block">Device:</span> {{ $reading->device_id }}</p>
                        <p class="text-sm text-gray-600"><span class="font-semibold text-gray-800 w-20 inline-block">Date:</span> {{ $reading->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') }}</p>
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
                    
                    <!-- Footer -->
                    <div class="mt-auto text-center border-t border-gray-200 pt-6">
                        <div class="flex items-center justify-center gap-2 mb-2">
                            <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" class="w-6 h-6 object-contain opacity-70">
                            <span class="text-lg font-bold text-gray-500 uppercase tracking-widest">AQUASENSE</span>
                        </div>
                        <p class="text-sm text-gray-500 mb-1">© 2026 AquaSense. All rights reserved.</p>
                        <p class="text-sm text-gray-500 font-medium">Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
