<x-app-layout>
    <div class="py-12">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <!-- Back Button -->
            <div class="mb-6">
                <a href="{{ route('analysis.index') }}" class="inline-flex items-center text-sm text-gray-500 hover:text-gray-700">
                    <svg class="w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
                    </svg>
                    Back to Analyses
                </a>
            </div>

            <!-- Analysis Header -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <div class="flex items-center justify-between">
                        <h1 class="text-2xl font-bold text-gray-900">Analysis Details</h1>
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                            @if($analysis->risk_level == 'critical') bg-red-100 text-red-800
                            @elseif($analysis->risk_level == 'high') bg-orange-100 text-orange-800
                            @elseif($analysis->risk_level == 'medium') bg-yellow-100 text-yellow-800
                            @else bg-green-100 text-green-800
                            @endif">
                            {{ $analysis->risk_level === 'safe' ? 'Safe' : ucfirst($analysis->risk_level) . ' Risk' }}
                        </span>
                    </div>
                </div>
                
                <div class="px-6 py-4">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div>
                            <p class="text-sm font-medium text-gray-500">Analysis Date</p>
                            <p class="text-lg text-gray-900">{{ $analysis->analyzed_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Confidence Score</p>
                            <p class="text-lg text-gray-900">{{ $analysis->confidence_score }}%</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Analysis Type</p>
                            <p class="text-lg text-gray-900">{{ ucfirst($analysis->analysis_type) }}</p>
                        </div>
                        <div>
                            <p class="text-sm font-medium text-gray-500">Reading ID</p>
                            <p class="text-lg text-gray-900">#{{ $analysis->waterReading->id }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- AI Insight -->
            <div class="bg-white rounded-lg shadow mb-6">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">AI Insight</h2>
                </div>
                <div class="px-6 py-4">
                    <p class="text-gray-700 leading-relaxed">{{ $analysis->ai_insight }}</p>
                </div>
            </div>

            <!-- Recommendations -->
            @if($analysis->recommendations)
                <div class="bg-white rounded-lg shadow mb-6">
                    <div class="px-6 py-4 border-b border-gray-200">
                        <h2 class="text-lg font-semibold text-gray-900">Recommendations</h2>
                    </div>
                    <div class="px-6 py-4">
                        <ul class="space-y-2">
                            @foreach($analysis->recommendations as $recommendation)
                                <li class="flex items-start">
                                    <svg class="w-5 h-5 text-green-500 mt-0.5 mr-3 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-gray-700">{{ $recommendation }}</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <!-- Water Reading Data -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Original Water Reading Data</h2>
                </div>
                <div class="px-6 py-4">
                    <div class="grid grid-cols-2 md:grid-cols-4 gap-6">
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-blue-100 rounded-full mb-2">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Turbidity</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $analysis->waterReading->turbidity }}</p>
                            <p class="text-xs text-gray-500">% Clarity</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-purple-100 rounded-full mb-2">
                                <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">TDS</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $analysis->waterReading->tds }}</p>
                            <p class="text-xs text-gray-500">mg/L</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-green-100 rounded-full mb-2">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21a4 4 0 01-4-4V5a2 2 0 012-2h4a2 2 0 012 2v12a4 4 0 01-4 4zm0 0h12a2 2 0 002-2v-4a2 2 0 00-2-2h-2.343M11 7.343l1.657-1.657a2 2 0 012.828 0l2.829 2.829a2 2 0 010 2.828l-8.486 8.485M7 17h.01"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">pH Level</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $analysis->waterReading->ph }}</p>
                            <p class="text-xs text-gray-500">pH</p>
                        </div>
                        
                        <div class="text-center">
                            <div class="inline-flex items-center justify-center w-12 h-12 bg-orange-100 rounded-full mb-2">
                                <svg class="w-6 h-6 text-orange-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                                </svg>
                            </div>
                            <p class="text-sm font-medium text-gray-500">Water Temp</p>
                            <p class="text-xl font-semibold text-gray-900">{{ $analysis->waterReading->temperature }}</p>
                            <p class="text-xs text-gray-500">°C</p>
                        </div>
                    </div>
                    
                    <div class="mt-6 pt-6 border-t border-gray-200">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 text-sm">
                            <div>
                                <p class="font-medium text-gray-500">Device ID</p>
                                <p class="text-gray-900">{{ $analysis->waterReading->device_id }}</p>
                            </div>
                            <div>
                                <p class="font-medium text-gray-500">Reading Timestamp</p>
                            <p class="text-gray-900">{{ $analysis->waterReading->created_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
