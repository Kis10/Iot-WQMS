<x-app-layout>
    <!-- Print Styles: Hide dashboard, show only the report -->
    <style>
        #printableReport { display: none; }
        @media print {
            body * { visibility: hidden; }
            #printableReport, #printableReport * { visibility: visible; }
            #printableReport {
                display: block !important;
                position: absolute;
                left: 0; top: 0;
                width: 100%;
                margin: 0;
                padding: 40px;
                box-shadow: none !important;
                border: none !important;
            }
            nav, aside, footer, header, .py-12 { display: none !important; }
            * {
                -webkit-print-color-adjust: exact !important;
                print-color-adjust: exact !important;
            }
        }
    </style>

    <!-- Hidden Printable Report (populated dynamically by JS) -->
    <div id="printableReport">
        <div style="max-width: 700px; margin: 0 auto; font-family: 'Figtree', sans-serif;">
            <!-- Report Header -->
            <div style="position: relative; text-align: center; border-bottom: 2px solid #e5e7eb; padding-bottom: 24px; margin-bottom: 24px; padding-top: 16px;">
                <div style="position: absolute; top: 0; left: 0; display: flex; align-items: center; gap: 10px;">
                    <img src="{{ asset('img/logo/logo-wq.png') }}" alt="AquaSense Logo" style="width: 36px; height: 36px; object-fit: contain;">
                    <span style="font-size: 18px; font-weight: 700; color: #374151; letter-spacing: 2px;">AQUASENSE</span>
                </div>
                <div style="margin-top: 48px;">
                    <h3 style="font-size: 14px; font-weight: 700; color: #111827; letter-spacing: 0.5px;">
                        IoT-based Water Quality Monitoring System for <br>
                        <span style="display: block; margin-top: 4px;">Aquaculture</span>
                    </h3>
                </div>
            </div>

            <!-- Info: Device & Date -->
            <div style="margin-bottom: 24px; padding-left: 8px;">
                <p id="printDevice" style="font-size: 13px; color: #4b5563; margin-bottom: 4px;"></p>
                <p id="printDate" style="font-size: 13px; color: #4b5563; margin-bottom: 4px;"></p>
                <p id="printLocation" style="font-size: 13px; color: #4b5563;"></p>
            </div>

            <!-- Data Table -->
            <div style="border: 1px solid #e5e7eb; border-radius: 8px; overflow: hidden; margin-bottom: 24px;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f9fafb;">
                            <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Parameter</th>
                            <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Value</th>
                            <th style="padding: 12px 20px; text-align: left; font-size: 11px; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.5px;">Status</th>
                        </tr>
                    </thead>
                    <tbody id="printTableBody"></tbody>
                </table>
            </div>

            <!-- AI Analysis -->
            <div id="printAiSection" style="margin-bottom: 24px; padding: 20px; background-color: #eff6ff; border-radius: 12px; border: 1px solid #dbeafe;"></div>

            <!-- Footer -->
            <div style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 20px; margin-top: auto;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px;">
                    <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" style="width: 20px; height: 20px; object-fit: contain; opacity: 0.7;">
                    <span style="font-size: 16px; font-weight: 700; color: #374151; letter-spacing: 1px;">AquaSense</span>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">&copy; 2026 AquaSense. All rights reserved.</p>
                <p style="font-size: 12px; color: #6b7280; font-weight: 500;">Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra</p>
            </div>
        </div>
    </div>

    <div class="py-12">
        <div id="dashboardContainer" class="max-w-none mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Overall Water Quality Status -->
            <div class="mb-8 p-6 bg-white/60 backdrop-blur-md rounded-2xl border border-white shadow-sm flex flex-col md:flex-row items-center justify-between gap-6 relative overflow-hidden">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl"></div>
                
                <div class="flex items-center gap-6">
                    <div class="relative w-20 h-20 sm:w-24 sm:h-24">
                         <svg class="w-full h-full" viewBox="0 0 100 100">
                            <circle cx="50" cy="50" r="45" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <circle id="overall-health-circle" cx="50" cy="50" r="45" fill="none" stroke="#10b981" stroke-width="8" 
                                stroke-dasharray="282.7" stroke-dashoffset="0" stroke-linecap="round" transform="rotate(-90 50 50)"/>
                            <text id="overall-health-text" x="50" y="55" text-anchor="middle" font-size="20" font-weight="black" fill="#111827" style="font-family: var(--font-coco);">100%</text>
                         </svg>
                    </div>
                    <div>
                        <h2 class="text-xl sm:text-2xl font-black text-gray-900 tracking-tight" style="font-family: var(--font-coco);">Overall Water Quality</h2>
                        <p id="overall-health-desc" class="text-gray-500 text-[10px] sm:text-xs font-medium mt-1">Analyzing real-time sensor contributions based on lab standards...</p>
                    </div>
                </div>

                <div class="flex flex-wrap justify-center md:justify-end gap-3 sm:gap-6">
                    <div class="flex flex-col items-center">
                        <span id="contrib-ph" class="text-xs sm:text-base font-black text-gray-900">pH=30.0%</span>
                        <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest">pH Level</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span id="contrib-temp" class="text-xs sm:text-base font-black text-gray-900">Temp=25.0%</span>
                        <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest">Temperature</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span id="contrib-turbidity" class="text-xs sm:text-base font-black text-gray-900">Turb=25.0%</span>
                        <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest">Turbidity</span>
                    </div>
                    <div class="flex flex-col items-center">
                        <span id="contrib-tds" class="text-xs sm:text-base font-black text-gray-900">TDS=20.0%</span>
                        <span class="text-[9px] sm:text-[10px] text-gray-400 uppercase font-black tracking-widest">TDS</span>
                    </div>
                </div>
            </div>

            <!-- Measurement Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Turbidity Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-black uppercase tracking-wider mb-3">Turbidity</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-turbidity-circle" cx="60" cy="60" r="50" fill="none" stroke="#4f46e5" stroke-width="8" 
                                stroke-dasharray="{{ ($latest?->turbidity ?? 0) / 100 * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-turbidity-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="black" fill="#111827">
                                {{ round($latest?->turbidity ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">%</p>
                        <span id="status-turbidity" class="mt-1 text-[10px] font-black uppercase tracking-tighter px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-black uppercase tracking-widest italic">Ideal: 50-100%</p>
                    </div>
                </div>

                <!-- TDS Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-black uppercase tracking-wider mb-3">TDS</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-tds-circle" cx="60" cy="60" r="50" fill="none" stroke="#8b5cf6" stroke-width="8" 
                                stroke-dasharray="{{ ($latest?->tds ?? 0) / 1000 * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-tds-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="black" fill="#111827">
                                {{ round($latest?->tds ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">mg/L</p>
                        <span id="status-tds" class="mt-1 text-[10px] font-black uppercase tracking-tighter px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-black uppercase tracking-widest italic">Ideal: 100-500</p>
                    </div>
                </div>

                <!-- pH Level Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-black uppercase tracking-wider mb-3">pH Level</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-ph-circle" cx="60" cy="60" r="50" fill="none" stroke="#10b981" stroke-width="8" 
                                stroke-dasharray="{{ (($latest?->ph ?? 0) / 14) * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-ph-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="black" fill="#111827">
                                {{ round($latest?->ph ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">pH</p>
                        <span id="status-ph" class="mt-1 text-[10px] font-black uppercase tracking-tighter px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-black uppercase tracking-widest italic">Ideal: 6.5-8.5</p>
                    </div>
                </div>

                <!-- Temperature Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-black uppercase tracking-wider mb-3">Water Temp</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-temp-circle" cx="60" cy="60" r="50" fill="none" stroke="#f59e0b" stroke-width="8" 
                                stroke-dasharray="{{ (($latest?->temperature ?? 0) / 50) * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-temp-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="black" fill="#111827">
                                {{ round($latest?->temperature ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">°C</p>
                        <span id="status-temp" class="mt-1 text-[10px] font-black uppercase tracking-tighter px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-black uppercase tracking-widest italic">Ideal: 24-30°C</p>
                    </div>
                </div>
            </div>

            <!-- Spacing Divider -->
            <div class="my-8"></div>

            <!-- Line Chart for Real-Time Monitoring -->
            <div class="mt-4">
                <div class="bg-white rounded-lg shadow p-6">
                    <h3 class="text-lg font-semibold text-gray-800 mb-6">Real-Time Monitoring Trends</h3>
                    <div class="relative" style="height: 400px;">
                        <canvas id="waterQualityChart"></canvas>
                    </div>
                </div>
            </div>

            <!-- AI Analysis Popup -->
            <div id="aiAnalysisPopup" class="absolute z-30 max-w-sm w-full sm:w-96 opacity-0 pointer-events-none transition-opacity duration-700" data-analysis-id="{{ $latestAnalysis->id ?? 0 }}">
                <div class="bg-white rounded-lg shadow-xl border border-gray-100">
                    <div id="aiPopupHandle" style="touch-action: none;" class="px-4 py-3 bg-gradient-to-r cursor-move select-none
                        @if($latestAnalysis?->risk_level == 'critical') from-red-50 to-red-100 border-l-4 border-red-500
                        @elseif($latestAnalysis?->risk_level == 'high') from-orange-50 to-orange-100 border-l-4 border-orange-500
                        @elseif($latestAnalysis?->risk_level == 'medium') from-yellow-50 to-yellow-100 border-l-4 border-yellow-500
                        @elseif($latestAnalysis?->risk_level == 'safe') from-green-50 to-green-100 border-l-4 border-green-500
                        @else from-gray-50 to-gray-100 border-l-4 border-gray-300
                        @endif">
                        <div class="flex items-center justify-between gap-3">
                            <h4 class="text-base font-black text-gray-900">AquaSense Water Quality Analysis</h4>
                            <div class="flex items-center gap-2">
                                <span id="aiPopupRiskBadge" class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-black
                                @if($latestAnalysis?->risk_level == 'critical') bg-red-100 text-red-800
                                @elseif($latestAnalysis?->risk_level == 'high') bg-orange-100 text-orange-800
                                @elseif($latestAnalysis?->risk_level == 'medium') bg-yellow-100 text-yellow-800
                                @elseif($latestAnalysis?->risk_level == 'safe') bg-green-100 text-green-800
                                @else bg-gray-100 text-gray-700
                                @endif">
                                @if($latestAnalysis)
                                    {{ $latestAnalysis->risk_level === 'safe' ? 'Safe' : ucfirst($latestAnalysis->risk_level) . ' Risk' }}
                                @else
                                    No Data
                                @endif
                                </span>
                                <span id="aiPopupGrowthBadge" class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-black
                                    @if($latestAnalysis?->risk_level == 'safe') bg-emerald-100 text-emerald-800
                                    @elseif($latestAnalysis) bg-rose-100 text-rose-800
                                    @else bg-gray-100 text-gray-700
                                    @endif">
                                    @if($latestAnalysis)
                                        {{ $latestAnalysis->risk_level === 'safe' ? 'Good for Growth' : 'Needs Action' }}
                                    @else
                                        No Data
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                    <div class="p-4">
                        <p id="aiPopupInsight" class="text-[15px] text-gray-900 leading-relaxed font-medium">
                            {{ $latestAnalysis?->ai_insight ?? 'No analysis yet. Waiting for new readings...' }}
                        </p>
                        <div id="aiPopupRecommendations" class="mt-3 pt-3 border-t border-gray-200 @if(!$latestAnalysis?->recommendations) hidden @endif">
                            <p class="text-[13px] font-black text-gray-700 mb-2">Key Recommendations:</p>
                            <div id="aiPopupRecommendationsList" class="space-y-1">
                                @if($latestAnalysis?->recommendations)
                                    @foreach($latestAnalysis->recommendations as $recommendation)
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-gray-800 font-medium">{{ $recommendation }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between text-[11px] text-gray-600 font-bold">
                                <span id="aiPopupAnalyzedAt">
                                    @if($latestAnalysis)
                                        Analyzed {{ $latestAnalysis->analyzed_at->setTimezone('Asia/Manila')->format('M j, Y g:i A') }}
                                    @else
                                        Analyzed -
                                    @endif
                                </span>
                                <span id="aiPopupConfidence">
                                    @if($latestAnalysis)
                                        AI Confidence: {{ $latestAnalysis->confidence_score }}%
                                    @else
                                        AI Confidence: -
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <!-- Chart.js Library -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    {{-- Ably script removed, handled globally in app.blade.php --}}
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const ctx = document.getElementById('waterQualityChart').getContext('2d');
            
            // --- PERSISTENCE LOGIC START ---
            const STORAGE_KEY_CHART = 'dashboardChartData';
            const STORAGE_KEY_LATEST = 'dashboardLatestReading';

            function saveStateToStorage(labels, datasets) {
                const state = {
                    labels: labels,
                    datasetsData: datasets.map(ds => ds.data)
                };
                localStorage.setItem(STORAGE_KEY_CHART, JSON.stringify(state));
            }

            function loadStateFromStorage() {
                const raw = localStorage.getItem(STORAGE_KEY_CHART);
                return raw ? JSON.parse(raw) : null;
            }

            function clearStorageState() {
                localStorage.removeItem(STORAGE_KEY_CHART);
                localStorage.removeItem(STORAGE_KEY_LATEST);
            }
            // --- PERSISTENCE LOGIC END ---

            // Check if user has explicitly cleared the dashboard
            const isCleared = localStorage.getItem('dashboardCleared') === 'true';

            // Prepare data from backend
            const backendData = @json($chartData);
            let initialLabels = [];
            let initialData = {
                turbidity: [],
                tds: [],
                ph: [],
                temp: []
            };

            // ONLY load backend data if dashboard is NOT cleared
            if (!isCleared) {
                initialLabels = backendData.map(reading => {
                    const date = new Date(reading.created_at);
                    return date.toLocaleTimeString('en-US', {
                        hour: '2-digit',
                        minute: '2-digit',
                        timeZone: 'Asia/Manila'
                    });
                });
                
                initialData = {
                    turbidity: backendData.map(r => r.turbidity || 0),
                    tds: backendData.map(r => r.tds || 0),
                    ph: backendData.map(r => r.ph || 0),
                    temp: backendData.map(r => r.temperature || 0)
                };

                // Check LocalStorage for "Continued" real-time session
                const savedState = loadStateFromStorage();
                if (savedState && savedState.labels && savedState.labels.length > 0) {
                    console.log('Restoring readings from previous session...');
                    initialLabels = savedState.labels;
                    initialData.turbidity = savedState.datasetsData[0] || [];
                    initialData.tds = savedState.datasetsData[1] || [];
                    initialData.ph = savedState.datasetsData[2] || [];
                    initialData.temp = savedState.datasetsData[3] || [];
                }
            } else {
                console.log('Dashboard is in CLEARED state. Waiting for new data.');
            }
            
            const chart = new Chart(ctx, {
                type: 'line',
                data: {
                    labels: initialLabels,
                    datasets: [
                        {
                            label: 'Turbidity (%)',
                            data: initialData.turbidity,
                            borderColor: '#3b82f6',
                            backgroundColor: 'rgba(59, 130, 246, 0.05)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#3b82f6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'TDS (mg/L)',
                            data: initialData.tds,
                            borderColor: '#8b5cf6',
                            backgroundColor: 'rgba(139, 92, 246, 0.05)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#8b5cf6',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'pH Level',
                            data: initialData.ph,
                            borderColor: '#10b981',
                            backgroundColor: 'rgba(16, 185, 129, 0.05)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#10b981',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        },
                        {
                            label: 'Water Temp (°C)',
                            data: initialData.temp,
                            borderColor: '#f59e0b',
                            backgroundColor: 'rgba(245, 158, 11, 0.05)',
                            borderWidth: 2,
                            tension: 0.4,
                            fill: true,
                            pointRadius: 4,
                            pointBackgroundColor: '#f59e0b',
                            pointBorderColor: '#fff',
                            pointBorderWidth: 2,
                            pointHoverRadius: 6
                        }]
                },
                options: {
                    responsive: true,
                    maintainAspectRatio: false,
                    plugins: {
                        legend: {
                            position: 'top',
                            labels: {
                                usePointStyle: true,
                                padding: 15,
                                font: {
                                    size: 12,
                                    weight: 'bold'
                                },
                                color: '#374151'
                            }
                        },
                        tooltip: {
                            backgroundColor: 'rgba(0, 0, 0, 0.8)',
                            padding: 12,
                            titleFont: {
                                size: 14
                            },
                            bodyFont: {
                                size: 12
                            },
                            cornerRadius: 8
                        }
                    },
                    scales: {
                        y: {
                            beginAtZero: true,
                            grid: {
                                color: 'rgba(209, 213, 219, 0.5)',
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        },
                        x: {
                            grid: {
                                display: false,
                                drawBorder: false
                            },
                            ticks: {
                                color: '#6b7280',
                                font: {
                                    size: 11
                                }
                            }
                        }
                    }
                }
            });

            function formatTimeLabel(isoString) {
                const date = new Date(isoString);
                return date.toLocaleTimeString('en-US', {
                    hour: '2-digit',
                    minute: '2-digit',
                    timeZone: 'Asia/Manila'
                });
            }

            function appendReading(reading) {
                if (!reading) return;
                
                // Fallback for missing timestamp (common in partial hardware updates)
                if (!reading.created_at) {
                    reading.created_at = new Date().toISOString();
                }

                // IMPORTANT: Any data arrival clears the "dashboard cleared" flag
                if (localStorage.getItem('dashboardCleared')) {
                    console.log('New data detected. Deactivating cleared state.');
                    localStorage.removeItem('dashboardCleared');
                }

                chart.data.labels.push(formatTimeLabel(reading.created_at));
                chart.data.datasets[0].data.push(reading.turbidity ?? 0);
                chart.data.datasets[1].data.push(reading.tds ?? 0);
                chart.data.datasets[2].data.push(reading.ph ?? 0);
                chart.data.datasets[3].data.push(reading.temperature ?? 0);

                if (chart.data.labels.length > 20) {
                    chart.data.labels.shift();
                    chart.data.datasets.forEach(ds => ds.data.shift());
                }

                chart.update('none');
                saveStateToStorage(chart.data.labels, chart.data.datasets);
            }

            let lastReadingState = @json($latest);
            try {
                const saved = localStorage.getItem(STORAGE_KEY_LATEST);
                if (saved) {
                    const parsed = JSON.parse(saved);
                    // Merge: Backend data (lastReadingState) should OVERWRITE saved state 
                    // to ensure initial page load reflects the most recent database records
                    if (lastReadingState) {
                        lastReadingState = { ...parsed, ...lastReadingState };
                    } else {
                        lastReadingState = parsed;
                    }
                }
            } catch(e) {}

            function updateGauges(reading, save = true) {
                if (!reading) return;
                
                // Merge with last known state
                if (lastReadingState) {
                    reading = { ...lastReadingState, ...reading };
                }
                
                if (save) {
                     if (!reading.created_at) reading.created_at = new Date().toISOString();
                     localStorage.setItem(STORAGE_KEY_LATEST, JSON.stringify(reading));
                     lastReadingState = reading;
                }

                const updateOverallHealth = (r) => {
                    if (!r) return;
                    // Weights: pH (30%), Temp (25%), Clarity (25%), TDS (20%)
                    const weights = { ph: 0.30, temp: 0.25, turbidity: 0.25, tds: 0.20 };
                    let scores = { ph: 100, temp: 100, turbidity: 100, tds: 100 };

                    if (r.ph < 6.5) scores.ph = Math.max(0, 100 - (6.5 - r.ph) * 50);
                    else if (r.ph > 8.5) scores.ph = Math.max(0, 100 - (r.ph - 8.5) * 50);

                    if (r.temperature < 24) scores.temp = Math.max(0, 100 - (24 - r.temperature) * 10);
                    else if (r.temperature > 30) scores.temp = Math.max(0, 100 - (r.temperature - 30) * 15);

                    if (r.turbidity < 50) scores.turbidity = Math.max(0, (r.turbidity / 50) * 100);
                    
                    if (r.tds > 500) scores.tds = Math.max(0, 100 - (r.tds - 500) * 0.1);

                    const wqi = (scores.ph * weights.ph) + (scores.temp * weights.temp) + (scores.turbidity * weights.turbidity) + (scores.tds * weights.tds);
                    
                    const circle = document.getElementById('overall-health-circle');
                    const text = document.getElementById('overall-health-text');
                    const desc = document.getElementById('overall-health-desc');

                    if (circle && text) {
                        const circumference = 282.7;
                        const offset = circumference - (wqi / 100) * circumference;
                        circle.style.transition = 'stroke-dashoffset 1s ease-in-out, stroke 1s';
                        circle.setAttribute('stroke-dashoffset', offset);
                        
                        let color = '#10b981';
                        let msg = 'Excellent water quality. Ideal for fish growth.';
                        
                        if (wqi < 60) {
                            color = '#ef4444';
                            msg = 'CRITICAL: Water conditions may lead to high stress/mortality.';
                        } else if (wqi < 85) {
                            color = '#f59e0b';
                            msg = 'Warning: Suboptimal conditions detected. Check sensors.';
                        }
                        circle.setAttribute('stroke', color);
                        text.textContent = Math.round(wqi) + '%';
                        if (desc) desc.textContent = msg;

                        // Update individual contributions in UI
                        const updateContrib = (id, score, weight, prefix) => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = `${prefix}=${(score * weight).toFixed(1)}%`;
                        };
                        updateContrib('contrib-ph', scores.ph, weights.ph, 'pH');
                        updateContrib('contrib-temp', scores.temp, weights.temp, 'Temp');
                        updateContrib('contrib-turbidity', scores.turbidity, weights.turbidity, 'Turb');
                        updateContrib('contrib-tds', scores.tds, weights.tds, 'TDS');
                    }
                };

                const updateCircle = (id, val, max, param) => {
                    const circle = document.getElementById(id);
                    if (circle) {
                        const circumference = 314.1;
                        const percent = Math.min(Math.max(val / max, 0), 1);
                        circle.setAttribute('stroke-dasharray', `${percent * circumference}, ${circumference}`);
                        
                        // Dynamic Colors
                        let color = '#4f46e5'; // Default Indigo
                        if (param === 'ph') {
                            if (val < 6.0 || val > 9.0) color = '#ef4444'; // Red (Critical)
                            else if (val < 6.5 || val > 8.5) color = '#f59e0b'; // Amber (Suboptimal)
                            else color = '#10b981'; // Green (Safe 6.5-8.5)
                        } else if (param === 'tds') {
                            if (val > 1000) color = '#ef4444';
                            else if (val > 500) color = '#f59e0b';
                            else color = '#8b5cf6'; // Violet
                        } else if (param === 'turbidity') {
                            if (val < 20) color = '#ef4444'; // Red (Critical)
                            else if (val < 50) color = '#f59e0b'; // Amber (Suboptimal)
                            else color = '#4f46e5'; // Indigo (Normal 50-100)
                        } else if (param === 'temp') {
                            if (val < 15 || val > 35) color = '#ef4444';
                            else if (val < 20 || val > 30) color = '#f59e0b';
                            else color = '#f97316'; // Orange
                        }
                        circle.setAttribute('stroke', color);

                        // Update Status Label
                        const statusLabel = document.getElementById(`status-${param}`);
                        if (statusLabel) {
                            let statusText = 'Normal';
                            let statusClass = 'bg-emerald-50 text-emerald-600';
                            
                            if (color === '#ef4444') {
                                statusText = 'Critical';
                                statusClass = 'bg-red-50 text-red-600';
                            } else if (color === '#f59e0b') {
                                statusText = 'Warning';
                                statusClass = 'bg-amber-50 text-amber-600';
                            }
                            
                            statusLabel.textContent = statusText;
                            statusLabel.className = `mt-1 text-[9px] font-bold uppercase tracking-tighter px-2 py-0.5 rounded-full ${statusClass}`;
                        }
                    }
                };
                const updateText = (id, val) => {
                    const text = document.getElementById(id);
                    if (text) text.textContent = Number(val).toFixed(2);
                };
                // Only update if value is present to prevent flickering to 0
                if (reading.turbidity !== undefined) {
                    updateCircle('gauge-turbidity-circle', reading.turbidity, 100, 'turbidity');
                    updateText('gauge-turbidity-text', reading.turbidity);
                }
                if (reading.tds !== undefined) {
                    updateCircle('gauge-tds-circle', reading.tds, 1000, 'tds');
                    updateText('gauge-tds-text', reading.tds);
                }
                if (reading.ph !== undefined) {
                    updateCircle('gauge-ph-circle', reading.ph, 14, 'ph');
                    updateText('gauge-ph-text', reading.ph);
                }
                if (reading.temperature !== undefined) {
                    updateCircle('gauge-temp-circle', reading.temperature, 50, 'temp');
                    updateText('gauge-temp-text', reading.temperature);
                }

                // Update Overall Health Score
                updateOverallHealth(reading);
            }

            // Restore Gauges on Load
            if (!isCleared) {
                if (lastReadingState) {
                    updateGauges(lastReadingState, false);
                }
            } else {
                updateGauges({ turbidity: 0, tds: 0, ph: 0, temperature: 0 }, false);
            }

            let lastUpdateTimestamp = 0;
            const THROTTLE_MS = 1000; // 1 second throttle for UI smoothness

            window.addEventListener('new-reading', function(event) {
                 const reading = event.detail;
                 if (reading) {
                     const now = Date.now();
                     
                     // Always update Chart - merge with backfill if partial
                     const fullReading = lastReadingState ? { ...lastReadingState, ...reading } : reading;
                     appendReading(fullReading);

                     // Throttled Gauge Update
                     if (now - lastUpdateTimestamp > THROTTLE_MS) {
                         updateGauges(reading);
                         lastUpdateTimestamp = now;
                     }
                 }
            });

            // Refresh dashboard functionality
            const refreshButton = document.getElementById('refreshDashboard');
            const refreshButtonMobile = document.getElementById('refreshDashboardMobile');
            
            if (refreshButton) {
                refreshButton.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Are you sure you want to clear the dashboard real-time data? This won\'t delete your history.')) {
                        refreshDashboard();
                    }
                });
            }
            
            if (refreshButtonMobile) {
                refreshButtonMobile.addEventListener('click', function(e) {
                    e.preventDefault();
                    if (confirm('Clear real-time dashboard data?')) {
                        refreshDashboard();
                    }
                });
            }

            function refreshDashboard() {
                // Frontend-only Reset
                clearStorageState();
                localStorage.setItem('dashboardCleared', 'true');
                lastReadingState = null;

                chart.data.labels = [];
                chart.data.datasets.forEach(dataset => {
                    dataset.data = [];
                });
                chart.update();

                updateGauges({
                    turbidity: 0,
                    tds: 0,
                    ph: 0,
                    temperature: 0
                }, false);

                lastUpdateTimestamp = 0;
                console.log('Dashboard cleared.');
            }

            const dashboardContainer = document.getElementById('dashboardContainer');
            const popup = document.getElementById('aiAnalysisPopup');
            const popupHandle = document.getElementById('aiPopupHandle');
            const riskBadge = document.getElementById('aiPopupRiskBadge');
            const growthBadge = document.getElementById('aiPopupGrowthBadge');
            const insight = document.getElementById('aiPopupInsight');
            const recommendations = document.getElementById('aiPopupRecommendations');
            const recommendationsList = document.getElementById('aiPopupRecommendationsList');
            const analyzedAt = document.getElementById('aiPopupAnalyzedAt');
            const confidence = document.getElementById('aiPopupConfidence');
            const sound = document.getElementById('aiNotificationSound');
            const deviceSelect = document.getElementById('device_id');
            const speciesSelect = document.getElementById('species');
            const currentSpeciesLabel = document.getElementById('currentSpeciesLabel');

            if (deviceSelect && speciesSelect) {
                const syncSpeciesSelect = () => {
                    const selectedOption = deviceSelect.options[deviceSelect.selectedIndex];
                    const species = selectedOption ? selectedOption.dataset.species : null;
                    const speciesLabel = selectedOption ? selectedOption.dataset.speciesLabel : null;
                    if (species) {
                        speciesSelect.value = species;
                    }
                    if (speciesLabel && currentSpeciesLabel) {
                        currentSpeciesLabel.textContent = speciesLabel;
                    }
                };

                deviceSelect.addEventListener('change', syncSpeciesSelect);
                syncSpeciesSelect();
            }
            
            if (!dashboardContainer || !popup || !popupHandle || !riskBadge || !growthBadge || !insight || !recommendations || !recommendationsList || !analyzedAt || !confidence) {
                return;
            }

            const popupStateKey = 'aiAnalysisPopupState';
            const popupPositionKey = 'aiAnalysisPopupPosition';
            const popupLastShownKey = 'aiAnalysisLastShownId';
            const popupDurationMs = 30000;
            const analysisPollIntervalMs = 5000;
            const headerBaseClasses = 'px-4 py-3 bg-gradient-to-r cursor-move select-none';
            const badgeBaseClasses = 'inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold';
            let hideTimeoutId = null;
            const riskLabels = {
                critical: 'Critical Risk',
                high: 'High Risk',
                medium: 'Medium Risk',
                safe: 'Safe'
            };
            const growthStyles = {
                good: 'bg-emerald-100 text-emerald-800',
                action: 'bg-rose-100 text-rose-800',
                default: 'bg-gray-100 text-gray-700'
            };

            const riskStyles = {
                critical: {
                    header: 'from-red-50 to-red-100 border-l-4 border-red-500',
                    badge: 'bg-red-100 text-red-800'
                },
                high: {
                    header: 'from-orange-50 to-orange-100 border-l-4 border-orange-500',
                    badge: 'bg-orange-100 text-orange-800'
                },
                medium: {
                    header: 'from-yellow-50 to-yellow-100 border-l-4 border-yellow-500',
                    badge: 'bg-yellow-100 text-yellow-800'
                },
                safe: {
                    header: 'from-green-50 to-green-100 border-l-4 border-green-500',
                    badge: 'bg-green-100 text-green-800'
                },
                default: {
                    header: 'from-gray-50 to-gray-100 border-l-4 border-gray-300',
                    badge: 'bg-gray-100 text-gray-700'
                }
            };

            function formatManilaDateTime(isoString) {
                if (!isoString) return null;
                const date = new Date(isoString);
                if (Number.isNaN(date.getTime())) return null;
                return new Intl.DateTimeFormat('en-US', {
                    timeZone: 'Asia/Manila',
                    month: 'short',
                    day: 'numeric',
                    year: 'numeric',
                    hour: 'numeric',
                    minute: '2-digit',
                    hour12: true
                }).format(date);
            }

            function loadState(key) {
                try {
                    const raw = localStorage.getItem(key);
                    return raw ? JSON.parse(raw) : null;
                } catch (error) {
                    console.error('Failed to read popup state:', error);
                    return null;
                }
            }

            function saveState(key, value) {
                try {
                    localStorage.setItem(key, JSON.stringify(value));
                } catch (error) {
                    console.error('Failed to save popup state:', error);
                }
            }

            function getAnalysisPayload(analysis) {
                if (!analysis) return null;
                return {
                    id: analysis.id,
                    risk_level: analysis.risk_level,
                    ai_insight: analysis.ai_insight,
                    recommendations: analysis.recommendations,
                    analyzed_at: analysis.analyzed_at,
                    confidence_score: analysis.confidence_score
                };
            }

            function renderRecommendations(items) {
                recommendationsList.innerHTML = '';
                if (!Array.isArray(items) || items.length === 0) {
                    recommendations.classList.add('hidden');
                    return;
                }
                recommendations.classList.remove('hidden');
                items.forEach(item => {
                    const row = document.createElement('div');
                    row.className = 'flex items-start';

                    const icon = document.createElementNS('http://www.w3.org/2000/svg', 'svg');
                    icon.setAttribute('class', 'w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0');
                    icon.setAttribute('fill', 'currentColor');
                    icon.setAttribute('viewBox', '0 0 20 20');

                    const path = document.createElementNS('http://www.w3.org/2000/svg', 'path');
                    path.setAttribute('fill-rule', 'evenodd');
                    path.setAttribute('clip-rule', 'evenodd');
                    path.setAttribute('d', 'M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z');
                    icon.appendChild(path);

                    const text = document.createElement('span');
                    text.className = 'text-sm text-gray-600';
                    text.textContent = item;

                    row.appendChild(icon);
                    row.appendChild(text);
                    recommendationsList.appendChild(row);
                });
            }

            function updatePopupContent(analysis) {
                if (!analysis) {
                    popupHandle.className = `${headerBaseClasses} ${riskStyles.default.header}`;
                    riskBadge.className = `${badgeBaseClasses} ${riskStyles.default.badge}`;
                    riskBadge.textContent = 'No Data';
                    growthBadge.className = `${badgeBaseClasses} ${growthStyles.default}`;
                    growthBadge.textContent = 'No Data';
                    insight.textContent = 'No analysis yet. Waiting for new readings...';
                    analyzedAt.textContent = 'Analyzed -';
                    confidence.textContent = 'AI Confidence: -';
                    renderRecommendations([]);
                    return;
                }

                const level = (analysis.risk_level || '').toLowerCase();
                const styles = riskStyles[level] || riskStyles.default;
                popupHandle.className = `${headerBaseClasses} ${styles.header}`;
                riskBadge.className = `${badgeBaseClasses} ${styles.badge}`;
                riskBadge.textContent = riskLabels[level] || 'No Data';
                if (level === 'safe') {
                    growthBadge.className = `${badgeBaseClasses} ${growthStyles.good}`;
                    growthBadge.textContent = 'Good for Growth';
                } else if (level) {
                    growthBadge.className = `${badgeBaseClasses} ${growthStyles.action}`;
                    growthBadge.textContent = 'Needs Action';
                } else {
                    growthBadge.className = `${badgeBaseClasses} ${growthStyles.default}`;
                    growthBadge.textContent = 'No Data';
                }

                insight.textContent = analysis.ai_insight || 'No analysis insight available.';
                renderRecommendations(analysis.recommendations);

                const formatted = formatManilaDateTime(analysis.analyzed_at);
                analyzedAt.textContent = formatted ? `Analyzed ${formatted}` : 'Analyzed -';

                const confidenceScore = Number.parseFloat(analysis.confidence_score);
                confidence.textContent = Number.isFinite(confidenceScore)
                    ? `AI Confidence: ${confidenceScore.toFixed(1)}%`
                    : 'AI Confidence: -';
            }

            function scheduleHide(expiresAt) {
                if (hideTimeoutId) {
                    clearTimeout(hideTimeoutId);
                }
                const remaining = expiresAt - Date.now();
                if (remaining <= 0) {
                    hidePopup();
                    return;
                }
                hideTimeoutId = setTimeout(hidePopup, remaining);
            }

            function showPopup(analysis, expiresAtOverride, playSound = true) {
                if (!analysis) return;
                const now = Date.now();
                const expiresAt = expiresAtOverride || now + popupDurationMs;

                updatePopupContent(analysis);
                popup.classList.remove('opacity-0', 'pointer-events-none');
                popup.classList.add('opacity-100', 'pointer-events-auto');

                if (false && sound) {
                    // Global script handles sound
                }

                scheduleHide(expiresAt);

                saveState(popupStateKey, {
                    analysis: getAnalysisPayload(analysis),
                    expiresAt: expiresAt
                });
            }

            function hidePopup() {
                popup.classList.add('opacity-0', 'pointer-events-none');
                popup.classList.remove('opacity-100', 'pointer-events-auto');
                const currentState = loadState(popupStateKey);
                if (currentState) {
                    currentState.expiresAt = 0;
                    saveState(popupStateKey, currentState);
                }
            }

            function restorePopupIfActive() {
                const state = loadState(popupStateKey);
                if (state && state.expiresAt && state.expiresAt > Date.now() && state.analysis) {
                    showPopup(state.analysis, state.expiresAt, false);
                    return true;
                }
                return false;
            }

            function persistAnalysisId(analysis) {
                if (!analysis) return;
                const state = loadState(popupStateKey);
                const activeExpiresAt = state && state.expiresAt && state.expiresAt > Date.now() ? state.expiresAt : 0;

                saveState(popupStateKey, {
                    analysis: getAnalysisPayload(analysis),
                    expiresAt: activeExpiresAt
                });
            }

            function positionPopup(x, y, persist) {
                const containerWidth = dashboardContainer.clientWidth;
                const containerHeight = dashboardContainer.clientHeight;
                const popupWidth = popup.offsetWidth;
                const popupHeight = popup.offsetHeight;

                const maxX = Math.max(0, containerWidth - popupWidth);
                const maxY = Math.max(0, containerHeight - popupHeight);

                const clampedX = Math.min(Math.max(x, 0), maxX);
                const clampedY = Math.min(Math.max(y, 0), maxY);

                popup.style.left = `${clampedX}px`;
                popup.style.top = `${clampedY}px`;

                if (persist) {
                    saveState(popupPositionKey, { x: clampedX, y: clampedY });
                }
            }

            function initPopupPosition() {
                const savedPosition = loadState(popupPositionKey);
                if (savedPosition && Number.isFinite(savedPosition.x) && Number.isFinite(savedPosition.y)) {
                    positionPopup(savedPosition.x, savedPosition.y, false);
                    return;
                }

                const margin = 16;
                const containerWidth = dashboardContainer.clientWidth;
                const popupWidth = popup.offsetWidth;
                const defaultX = containerWidth - popupWidth - margin;
                const defaultY = margin;
                positionPopup(defaultX, defaultY, false);
            }

            let dragState = null;

            popupHandle.addEventListener('pointerdown', function(event) {
                event.preventDefault();
                const popupRect = popup.getBoundingClientRect();
                const containerRect = dashboardContainer.getBoundingClientRect();

                dragState = {
                    pointerId: event.pointerId,
                    offsetX: event.clientX - popupRect.left,
                    offsetY: event.clientY - popupRect.top,
                    containerLeft: containerRect.left,
                    containerTop: containerRect.top
                };

                popupHandle.setPointerCapture(event.pointerId);
            });

            popupHandle.addEventListener('pointermove', function(event) {
                if (!dragState || dragState.pointerId !== event.pointerId) return;

                const containerRect = dashboardContainer.getBoundingClientRect();
                const nextX = event.clientX - containerRect.left - dragState.offsetX;
                const nextY = event.clientY - dragState.offsetY;

                positionPopup(nextX, nextY, false);
            });

            // Listen for Global Analysis Event (from App Layout Ably)
            window.addEventListener('new-analysis', function(event) {
                const analysis = event.detail;
                if (analysis) {
                    showPopup(analysis);
                    saveState(popupLastShownKey, analysis.id);

                    // Auto-Print: 5-second delay, in-page print overlay
                    const readingId = analysis.water_reading_id;
                    if (readingId) {
                        setTimeout(() => {
                            populatePrintReport(analysis);
                            window.print();
                        }, 5000);
                    }
                }
            });

            function endDrag(event) {
                if (!dragState || dragState.pointerId !== event.pointerId) return;
                popupHandle.releasePointerCapture(event.pointerId);
                dragState = null;

                const currentX = Number.parseFloat(popup.style.left) || 0;
                const currentY = Number.parseFloat(popup.style.top) || 0;
                positionPopup(currentX, currentY, true);
            }

            popupHandle.addEventListener('pointerup', endDrag);
            popupHandle.addEventListener('pointercancel', endDrag);

            window.addEventListener('resize', function() {
                const currentX = Number.parseFloat(popup.style.left) || 0;
                const currentY = Number.parseFloat(popup.style.top) || 0;
                positionPopup(currentX, currentY, false);
            });

            function fetchLatestAnalysis(showOnFetch) {
                fetch('{{ route("analysis.latest") }}')
                    .then(res => res.json())
                    .then(analysis => {
                        if (!analysis || !analysis.id) {
                            updatePopupContent(null);
                            return;
                        }

                        const state = loadState(popupStateKey);
                        const isActive = state && state.expiresAt && state.expiresAt > Date.now();
                        const storedId = state && state.analysis ? state.analysis.id : null;
                        const isNew = storedId ? Number(storedId) !== Number(analysis.id) : true;
                        const lastShownId = loadState(popupLastShownKey);
                        const analyzedAtMs = Date.parse(analysis.analyzed_at);
                        const isRecent = Number.isFinite(analyzedAtMs) && (Date.now() - analyzedAtMs) <= popupDurationMs;

                        if (showOnFetch) {
                            if (isNew) {
                                showPopup(analysis);
                                saveState(popupLastShownKey, analysis.id);
                                return;
                            }
                            if (isActive) {
                                updatePopupContent(analysis);
                                return;
                            }
                            if (isRecent && Number(lastShownId) !== Number(analysis.id)) {
                                showPopup(analysis);
                                saveState(popupLastShownKey, analysis.id);
                                return;
                            }
                            updatePopupContent(analysis);
                            persistAnalysisId(analysis);
                            return;
                        }

                        updatePopupContent(analysis);
                        persistAnalysisId(analysis);
                    })
                    .catch(err => console.error('AI Poll Error:', err));
            }

            // ============================
            // Auto-Print Report Builder
            // ============================
            function populatePrintReport(analysis) {
                const reading = lastReadingState;
                if (!reading) return;

                // Device & Date
                const deviceEl = document.getElementById('printDevice');
                const dateEl = document.getElementById('printDate');
                const locationEl = document.getElementById('printLocation');

                deviceEl.innerHTML = '<span style="font-weight:600;color:#1f2937;display:inline-block;width:70px;">Device:</span> ' + (reading.device_id || 'ESP32-WQ-01');
                
                const now = new Date();
                const dateStr = now.toLocaleDateString('en-US', { month: 'short', day: 'numeric', year: 'numeric', timeZone: 'Asia/Manila' }) 
                    + ' ' + now.toLocaleTimeString('en-US', { hour: 'numeric', minute: '2-digit', hour12: true, timeZone: 'Asia/Manila' });
                dateEl.innerHTML = '<span style="font-weight:600;color:#1f2937;display:inline-block;width:70px;">Date:</span> ' + dateStr;
                locationEl.innerHTML = '<span style="font-weight:600;color:#1f2937;display:inline-block;width:70px;">Location:</span> Po-Ok, Hinoba-an, Negros Occidental';

                // Parameter Table
                const tbody = document.getElementById('printTableBody');
                const params = [
                    { name: 'Turbidity', value: parseFloat(reading.turbidity).toFixed(2) + '%', standard: '50-100%', critical: reading.turbidity < 50 },
                    { name: 'TDS', value: parseFloat(reading.tds).toFixed(2) + ' ppm', standard: '100-500', critical: reading.tds > 500 },
                    { name: 'pH Level', value: parseFloat(reading.ph).toFixed(2), standard: '6.5-8.5', critical: reading.ph < 6.5 || reading.ph > 8.5 },
                    { name: 'Water Temp', value: parseFloat(reading.temperature).toFixed(2) + '°C', standard: '24-30°C', critical: reading.temperature < 20 || reading.temperature > 32 },
                ];

                tbody.innerHTML = params.map(p => {
                    const statusBg = p.critical ? 'background-color:#fef2f2;color:#991b1b;' : 'background-color:#f0fdf4;color:#166534;';
                    const statusText = p.critical ? 'Critical' : 'Normal';
                    return `<tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px 20px;font-size:13px;font-weight:500;color:#111827;">${p.name}</td>
                        <td style="padding:12px 20px;font-size:13px;color:#111827;">${p.value} <span style="font-size:10px;color:#9ca3af;font-style:italic;margin-left:4px;">(Standard: ${p.standard})</span></td>
                        <td style="padding:12px 20px;">
                            <span style="display:inline-block;padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:600;${statusBg}">${statusText}</span>
                        </td>
                    </tr>`;
                }).join('');

                // AI Analysis Section
                const aiSection = document.getElementById('printAiSection');
                let aiHtml = `<h4 style="font-size:12px;font-weight:700;color:#1e3a5f;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:flex;align-items:center;gap:8px;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Analyzed by AquaSense
                </h4>`;
                aiHtml += `<p style="color:#374151;line-height:1.6;font-weight:500;">${analysis.ai_insight || 'No analysis insight available.'}</p>`;

                if (analysis.recommendations && analysis.recommendations.length > 0) {
                    aiHtml += `<div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(219,234,254,0.5);">
                        <h5 style="font-size:11px;font-weight:700;color:#1e40af;text-transform:uppercase;margin-bottom:8px;">Recommendations:</h5>
                        <ul style="list-style:disc;padding-left:20px;margin:0;">`;
                    analysis.recommendations.forEach(rec => {
                        aiHtml += `<li style="font-size:13px;color:rgba(30,58,95,0.8);margin-bottom:4px;">${rec}</li>`;
                    });
                    aiHtml += `</ul></div>`;
                }
                aiSection.innerHTML = aiHtml;
            }

            initPopupPosition();
            restorePopupIfActive();
            fetchLatestAnalysis(true);
            setInterval(() => fetchLatestAnalysis(true), analysisPollIntervalMs);
        });
    </script>
</x-app-layout>
