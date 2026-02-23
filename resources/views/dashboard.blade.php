<x-app-layout>
    <div class="py-12">
        <div id="dashboardContainer" class="max-w-none mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Measurement Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Turbidity Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-3">Turbidity</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-turbidity-circle" cx="60" cy="60" r="50" fill="none" stroke="#4f46e5" stroke-width="8" 
                                stroke-dasharray="{{ ($latest?->turbidity ?? 0) / 100 * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-turbidity-text" x="60" y="65" text-anchor="middle" font-size="16" font-weight="bold" fill="#111827">
                                {{ round($latest?->turbidity ?? 0, 1) }}
                            </text>
                        </svg>
                    </div>
                    <p class="text-center text-gray-400 text-[10px] sm:text-[11px] mt-2 font-medium">%</p>
                </div>

                <!-- TDS Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-3">TDS</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-tds-circle" cx="60" cy="60" r="50" fill="none" stroke="#8b5cf6" stroke-width="8" 
                                stroke-dasharray="{{ ($latest?->tds ?? 0) / 1000 * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-tds-text" x="60" y="65" text-anchor="middle" font-size="16" font-weight="bold" fill="#111827">
                                {{ round($latest?->tds ?? 0, 1) }}
                            </text>
                        </svg>
                    </div>
                    <p class="text-center text-gray-400 text-[10px] sm:text-[11px] mt-2 font-medium">mg/L</p>
                </div>

                <!-- pH Level Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-3">pH Level</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-ph-circle" cx="60" cy="60" r="50" fill="none" stroke="#10b981" stroke-width="8" 
                                stroke-dasharray="{{ (($latest?->ph ?? 0) / 14) * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-ph-text" x="60" y="65" text-anchor="middle" font-size="16" font-weight="bold" fill="#111827">
                                {{ round($latest?->ph ?? 0, 1) }}
                            </text>
                        </svg>
                    </div>
                    <p class="text-center text-gray-400 text-[10px] sm:text-[11px] mt-2 font-medium">pH</p>
                </div>

                <!-- Temperature Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition duration-200">
                    <h3 class="text-gray-500 text-[10px] sm:text-[11px] font-bold uppercase tracking-wider mb-3">Water Temp</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-temp-circle" cx="60" cy="60" r="50" fill="none" stroke="#f59e0b" stroke-width="8" 
                                stroke-dasharray="{{ (($latest?->temperature ?? 0) / 50) * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-temp-text" x="60" y="65" text-anchor="middle" font-size="16" font-weight="bold" fill="#111827">
                                {{ round($latest?->temperature ?? 0, 1) }}
                            </text>
                        </svg>
                    </div>
                    <p class="text-center text-gray-400 text-[10px] sm:text-[11px] mt-2 font-medium">°C</p>
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
                            <h4 class="text-sm font-semibold text-gray-800">AI Water Quality Analysis</h4>
                            <div class="flex items-center gap-2">
                                <span id="aiPopupRiskBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
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
                                <span id="aiPopupGrowthBadge" class="inline-flex items-center px-2.5 py-1 rounded-full text-xs font-semibold
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
                        <p id="aiPopupInsight" class="text-sm text-gray-700 leading-relaxed">
                            {{ $latestAnalysis?->ai_insight ?? 'No analysis yet. Waiting for new readings...' }}
                        </p>
                        <div id="aiPopupRecommendations" class="mt-3 pt-3 border-t border-gray-200 @if(!$latestAnalysis?->recommendations) hidden @endif">
                            <p class="text-xs font-medium text-gray-600 mb-2">Key Recommendations:</p>
                            <div id="aiPopupRecommendationsList" class="space-y-1">
                                @if($latestAnalysis?->recommendations)
                                    @foreach($latestAnalysis->recommendations as $recommendation)
                                        <div class="flex items-start">
                                            <svg class="w-4 h-4 text-blue-500 mt-0.5 mr-2 flex-shrink-0" fill="currentColor" viewBox="0 0 20 20">
                                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                            </svg>
                                            <span class="text-sm text-gray-600">{{ $recommendation }}</span>
                                        </div>
                                    @endforeach
                                @endif
                            </div>
                        </div>
                        <div class="mt-3 pt-3 border-t border-gray-200">
                            <div class="flex items-center justify-between text-xs text-gray-500">
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
                    // Merge saved state with backend latest if saved is newer
                    if (lastReadingState) {
                        lastReadingState = { ...lastReadingState, ...parsed };
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

                const updateCircle = (id, val, max) => {
                    const circle = document.getElementById(id);
                    if (circle) {
                        const circumference = 314.1;
                        const percent = Math.min(Math.max(val / max, 0), 1);
                        circle.setAttribute('stroke-dasharray', `${percent * circumference}, ${circumference}`);
                    }
                };
                const updateText = (id, val) => {
                    const text = document.getElementById(id);
                    if (text) text.textContent = Number(val).toFixed(1);
                };

                // Only update if value is present to prevent flickering to 0
                if (reading.turbidity !== undefined) {
                    updateCircle('gauge-turbidity-circle', reading.turbidity, 100);
                    updateText('gauge-turbidity-text', reading.turbidity);
                }
                if (reading.tds !== undefined) {
                    updateCircle('gauge-tds-circle', reading.tds, 1000);
                    updateText('gauge-tds-text', reading.tds);
                }
                if (reading.ph !== undefined) {
                    updateCircle('gauge-ph-circle', reading.ph, 14);
                    updateText('gauge-ph-text', reading.ph);
                }
                if (reading.temperature !== undefined) {
                    updateCircle('gauge-temp-circle', reading.temperature, 50);
                    updateText('gauge-temp-text', reading.temperature);
                }
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

            initPopupPosition();
            restorePopupIfActive();
            fetchLatestAnalysis(true);
            setInterval(() => fetchLatestAnalysis(true), analysisPollIntervalMs);
        });
    </script>
</x-app-layout>
