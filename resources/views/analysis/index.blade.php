<x-app-layout>
    <div class="py-12">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="mb-8">
                <h1 class="text-3xl font-bold text-gray-900">AI Water Quality Analysis</h1>
                <p class="mt-2 text-gray-600">Automated analysis and insights for water quality monitoring</p>
            </div>

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-8">
                <!-- Summary Cards -->
                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-green-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Total Analyses</p>
                            <p class="text-2xl font-semibold text-gray-900">{{ $analyses->total() }}</p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-blue-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Latest Analysis</p>
                            <p class="text-lg font-semibold text-gray-900">
                                @if($analyses->first())
                                    {{ $analyses->first()->analyzed_at->diffForHumans() }}
                                @else
                                    No analyses yet
                                @endif
                            </p>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-lg shadow p-6">
                    <div class="flex items-center">
                        <div class="flex-shrink-0 bg-yellow-100 rounded-full p-3">
                            <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                        </div>
                        <div class="ml-4">
                            <p class="text-sm font-medium text-gray-500">Avg Confidence</p>
                            <p class="text-2xl font-semibold text-gray-900">
                                @if($analyses->count() > 0)
                                    {{ round($analyses->avg('confidence_score'), 1) }}%
                                @else
                                    --
                                @endif
                            </p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis List -->
            <div class="bg-white rounded-lg shadow">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h2 class="text-lg font-semibold text-gray-900">Recent Analyses</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @forelse($analyses as $analysis)
                        <div class="p-6 hover:bg-gray-50 transition-colors">
                            <div class="flex items-start justify-between">
                                <div class="flex-1">
                                    <div class="flex items-center space-x-3 mb-2">
                                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium
                                            @if($analysis->risk_level == 'critical') bg-red-100 text-red-800
                                            @elseif($analysis->risk_level == 'high') bg-orange-100 text-orange-800
                                            @elseif($analysis->risk_level == 'medium') bg-yellow-100 text-yellow-800
                                            @else bg-green-100 text-green-800
                                            @endif">
                                            {{ $analysis->risk_level === 'safe' ? 'Safe' : ucfirst($analysis->risk_level) . ' Risk' }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            {{ $analysis->analyzed_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') }}
                                        </span>
                                        <span class="text-sm text-gray-500">
                                            Confidence: {{ $analysis->confidence_score }}%
                                        </span>
                                    </div>
                                    
                                    <p class="text-gray-900 mb-3">{{ $analysis->ai_insight }}</p>
                                    
                                    @if($analysis->recommendations)
                                        <div class="mb-3">
                                            <p class="text-sm font-medium text-gray-700 mb-1">Recommendations:</p>
                                            <ul class="text-sm text-gray-600 list-disc list-inside">
                                                @foreach($analysis->recommendations as $recommendation)
                                                    <li>{{ $recommendation }}</li>
                                                @endforeach
                                            </ul>
                                        </div>
                                    @endif
                                    
                                    <div class="flex items-center space-x-4 text-sm text-gray-500">
                                        <span>Turbidity: {{ $analysis->waterReading->turbidity }} NTU</span>
                                        <span>TDS: {{ $analysis->waterReading->tds }} mg/L</span>
                                        <span>pH: {{ $analysis->waterReading->ph }}</span>
                                        <span>Temp: {{ $analysis->waterReading->temperature }}°C</span>
                                    </div>
                                </div>
                                
                                <div class="ml-4">
                                    <a href="{{ route('analysis.show', $analysis) }}" 
                                       class="inline-flex items-center px-3 py-2 border border-gray-300 shadow-sm text-sm leading-4 font-medium rounded-md text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @empty
                        <div class="p-6 text-center">
                            <svg class="mx-auto h-12 w-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                            </svg>
                            <h3 class="mt-2 text-sm font-medium text-gray-900">No analyses yet</h3>
                            <p class="mt-1 text-sm text-gray-500">AI analyses will appear here 5 minutes after new water readings are received.</p>
                        </div>
                    @endforelse
                </div>
                
                @if($analyses->hasPages())
                    <div class="px-6 py-4 border-t border-gray-200">
                        {{ $analyses->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-app-layout>
