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
                min-height: 100vh;
                margin: 0;
                padding: 40px;
                padding-bottom: 100px;
                box-shadow: none !important;
                border: none !important;
            }
            #printFooter {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                padding: 16px 40px;
                background: white;
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

        </div>

            <!-- Footer (fixed at bottom of printed page) -->
            <div id="printFooter" style="text-align: center; border-top: 1px solid #e5e7eb; padding-top: 16px;">
                <div style="display: flex; align-items: center; justify-content: center; gap: 8px; margin-bottom: 8px;">
                    <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" style="width: 20px; height: 20px; object-fit: contain; opacity: 0.7;">
                    <span style="font-size: 16px; font-weight: 700; color: #374151; letter-spacing: 1px;">AquaSense</span>
                </div>
                <p style="font-size: 12px; color: #6b7280; margin-bottom: 4px;">&copy; 2026 AquaSense. All rights reserved.</p>
                <p style="font-size: 12px; color: #6b7280; font-weight: 500;">Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra</p>
            </div>
    </div>

    <div class="py-12">
        <div id="dashboardContainer" class="max-w-none mx-auto px-4 sm:px-6 lg:px-8 relative">
            <!-- Overall Water Quality Status -->
            <div class="mb-8 bg-white/60 backdrop-blur-md rounded-2xl border border-white shadow-sm relative overflow-hidden">
                <!-- Background Decoration -->
                <div class="absolute top-0 right-0 -mt-8 -mr-8 w-48 h-48 bg-indigo-500/5 rounded-full blur-3xl pointer-events-none"></div>

                <!-- Top Row: Knob + Title + Sensor Buttons -->
                <div class="flex flex-col md:flex-row items-stretch relative z-10">
                    <!-- Left: Knob + Title/Description -->
                    <div class="flex items-center gap-5 p-6 flex-1 min-w-0">
                        <div class="relative shrink-0">
                             <svg class="w-16 h-16 sm:w-20 sm:h-20" viewBox="0 0 120 120">
                                <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                                <circle id="overall-health-circle" cx="60" cy="60" r="50" fill="none" stroke="#10b981" stroke-width="8" 
                                    stroke-dasharray="314.1" stroke-dashoffset="0" stroke-linecap="round" transform="rotate(-90 60 60)"/>
                                <text id="overall-health-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="bold" fill="#111827">100%</text>
                             </svg>
                        </div>
                        <div class="min-w-0">
                            <h2 class="text-base sm:text-lg font-bold text-gray-900">Overall Water Quality</h2>
                            <p id="overall-health-desc" class="text-gray-500 text-[10px] sm:text-xs font-medium mt-1 leading-relaxed">Analyzing real-time sensor contributions based on lab standards...</p>
                        </div>
                    </div>

                    <!-- Right: Sensor Buttons in Gray Panel -->
                    <div class="bg-gray-50/80 md:rounded-r-2xl flex items-center justify-center px-4 sm:px-6 py-4 md:py-0 shrink-0">
                        <div class="flex flex-row items-center gap-4 sm:gap-7">
                            <div class="flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform" onclick="showFormulaModal('turbidity')">
                                <span id="contrib-turbidity" class="text-xs sm:text-sm font-bold text-gray-900 whitespace-nowrap">Turb=0.0%</span>
                                <span class="text-[8px] sm:text-[9px] text-gray-400 uppercase font-bold whitespace-nowrap tracking-wide">Turbidity (25%)</span>
                            </div>
                            <div class="flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform" onclick="showFormulaModal('tds')">
                                <span id="contrib-tds" class="text-xs sm:text-sm font-bold text-gray-900 whitespace-nowrap">TDS=0.0%</span>
                                <span class="text-[8px] sm:text-[9px] text-gray-400 uppercase font-bold whitespace-nowrap tracking-wide">TDS (20%)</span>
                            </div>
                            <div class="flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform" onclick="showFormulaModal('ph')">
                                <span id="contrib-ph" class="text-xs sm:text-sm font-bold text-gray-900 whitespace-nowrap">pH=0.0%</span>
                                <span class="text-[8px] sm:text-[9px] text-gray-400 uppercase font-bold whitespace-nowrap tracking-wide">pH Level (30%)</span>
                            </div>
                            <div class="flex flex-col items-center justify-center cursor-pointer hover:scale-110 transition-transform" onclick="showFormulaModal('temp')">
                                <span id="contrib-temp" class="text-xs sm:text-sm font-bold text-gray-900 whitespace-nowrap">Temp=0.0%</span>
                                <span class="text-[8px] sm:text-[9px] text-gray-400 uppercase font-bold whitespace-nowrap tracking-wide">Temperature (25%)</span>
                            </div>
                            
                            
                        </div>
                    </div>
                </div>

                <!-- Bottom: Recommendation (full width, expands with text) -->
                <div class="px-6 pb-5 pt-1 relative z-10">
                    <p id="overall-health-recommendation" class="text-gray-600 text-[10px] sm:text-xs font-medium leading-relaxed">Waiting for the latest reading to generate recommendation.</p>
                </div>
            </div>

            <!-- Measurement Cards Grid -->
            <div class="grid grid-cols-2 lg:grid-cols-4 gap-4 sm:gap-6">
                <!-- Turbidity Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="showFormulaModal('turbidity')">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-bold  tracking-widest mb-3">Turbidity (25%)</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-turbidity-circle" cx="60" cy="60" r="50" fill="none" stroke="#4f46e5" stroke-width="8" 
                                stroke-dasharray="{{ ($latest?->turbidity ?? 0) / 100 * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-turbidity-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="bold" fill="#111827">
                                {{ round($latest?->turbidity ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">%</p>
                        <span id="status-turbidity" class="mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-bold uppercase italic">Ideal: 50-100%</p>
                    </div>
                </div>

                <!-- TDS Card -->
                <div class="relative z-10 bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="showFormulaModal('tds')">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-bold  tracking-widest mb-3">TDS (20%)</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-tds-circle" cx="60" cy="60" r="50" fill="none" stroke="#8b5cf6" stroke-width="8" 
                                stroke-dasharray="{{ ($latest?->tds ?? 0) / 1000 * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-tds-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="bold" fill="#111827">
                                {{ round($latest?->tds ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">mg/L</p>
                        <span id="status-tds" class="mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-bold uppercase italic">Ideal: 0-500</p>
                    </div>
                </div>

                <!-- pH Level Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="showFormulaModal('ph')">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-bold  tracking-widest mb-3">pH Level (30%)</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-ph-circle" cx="60" cy="60" r="50" fill="none" stroke="#10b981" stroke-width="8" 
                                stroke-dasharray="{{ (($latest?->ph ?? 0) / 14) * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-ph-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="bold" fill="#111827">
                                {{ round($latest?->ph ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">pH</p>
                        <span id="status-ph" class="mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-bold uppercase italic">Ideal: 6.5-8.5</p>
                    </div>
                </div>

                <!-- Temperature Card -->
                <div class="bg-white rounded-xl shadow-sm border border-gray-100 p-4 hover:shadow-md transition-all duration-200 cursor-pointer" onclick="showFormulaModal('temp')">
                    <h3 class="text-gray-500 text-xs sm:text-sm font-bold  tracking-widest mb-3">Water Temp (25%)</h3>
                    <div class="flex justify-center">
                        <svg class="w-20 h-20 sm:w-24 sm:h-24 lg:w-32 lg:h-32" viewBox="0 0 120 120">
                            <!-- Background Circle -->
                            <circle cx="60" cy="60" r="50" fill="none" stroke="#f3f4f6" stroke-width="8"/>
                            <!-- Progress Circle -->
                            <circle id="gauge-temp-circle" cx="60" cy="60" r="50" fill="none" stroke="#f59e0b" stroke-width="8" 
                                stroke-dasharray="{{ (($latest?->temperature ?? 0) / 50) * 314.1 }}, 314.1" stroke-dashoffset="0" stroke-linecap="round"
                                transform="rotate(-90 60 60)"/>
                            <!-- Center Value -->
                            <text id="gauge-temp-text" x="60" y="65" text-anchor="middle" font-size="20" font-weight="bold" fill="#111827">
                                {{ round($latest?->temperature ?? 0, 2) }}
                            </text>
                        </svg>
                    </div>
                    <div class="flex flex-col items-center mt-2">
                        <p class="text-gray-400 text-[11px] sm:text-xs font-medium">°C</p>
                        <span id="status-temp" class="mt-1 text-[10px] font-bold uppercase px-2 py-0.5 rounded-full bg-gray-50 text-gray-400">Normal</span>
                        <p class="mt-1.5 text-[9px] text-gray-400 font-bold uppercase italic">Ideal: 25-32°C</p>
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
                            <h4 class="text-base font-bold text-gray-900">AquaSense Water Quality Analysis</h4>
                            <div class="flex items-center gap-2">
                                <span id="aiPopupRiskBadge" class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-bold
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
                                <span id="aiPopupGrowthBadge" class="inline-flex items-center px-3 py-1.5 rounded-full text-[13px] font-bold
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
                            <p class="text-[13px] font-bold text-gray-700 mb-2">Key Recommendations:</p>
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
        /**
         * Water Quality Index (WQI) Calculator
         * Based on FAO (Food and Agriculture Organization) water quality guidelines
         * for freshwater aquaculture (tilapia, catfish, carp).
         *
         * Weighted Arithmetic WQI Method:
         *   WQI = Σ (Wi × qi)
         * where Wi = weight of parameter i, qi = sub-index score (0-100)
         *
         * FAO Reference Ranges (Aquaculture):
         *   pH:          6.5 – 8.5
         *   Temperature: 25  – 32 °C
         *   Turbidity:   ≥ 50 % clarity (lower = worse)
         *   TDS:         0 – 500 mg/L
         *
         * Weight allocation follows parameter impact on fish health:
         *   pH         = 0.30 (30%) — most critical for fish survival
         *   Temperature = 0.25 (25%) — affects metabolism & dissolved O₂
         *   Turbidity   = 0.25 (25%) — affects light, feeding, gill health
         *   TDS         = 0.20 (20%) — mineral load indicator
         */
        window.getWQIInfo = function(reading) {
            if (!reading) return null;

            const weights = { ph: 0.30, temp: 0.25, turbidity: 0.25, tds: 0.20 };

            // --- Sub-index scoring functions (qi) ---
            // Each returns 0-100 score which will be multiplied by its weight

            // pH Sub-index: Peak at 7.5 = 100%
            // Drops down towards 0% gracefully in both directions
            function scorePH(val) {
                if (val <= 7.5) {
                    return Math.max(0, (val / 7.5) * 100);
                } else {
                    return Math.max(0, ((14.0 - val) / (14.0 - 7.5)) * 100);
                }
            }

            // Temperature Sub-index: Peak at 28°C = 100%
            // Decreases symmetrically
            function scoreTemp(val) {
                if (val <= 28) {
                    return Math.max(0, (val / 28) * 100);
                } else {
                    // Assuming 56°C is absolute 0 score symmetrically
                    return Math.max(0, ((56 - val) / 28) * 100);
                }
            }

            // Turbidity Sub-index: Exact percentage mapping
            function scoreTurbidity(val) {
                return Math.max(0, Math.min(100, val));
            }

            // TDS Sub-index: Lower is better. 0 = 100%, 500 = 50%, 1000 = 0%
            function scoreTDS(val) {
                return Math.max(0, 100 - (val / 10));
            }

            const ph = parseFloat(reading.ph) || 0;
            const temp = parseFloat(reading.temperature) || 0;
            const turbidity = parseFloat(reading.turbidity) || 0;
            const tds = parseFloat(reading.tds) || 0;

            const scores = {
                ph: scorePH(ph),
                temp: scoreTemp(temp),
                turbidity: scoreTurbidity(turbidity),
                tds: scoreTDS(tds)
            };

            // WQI = Σ (Wi × qi)
            const wqi = Math.min(100, Math.max(0,
                scores.ph * weights.ph +
                scores.temp * weights.temp +
                scores.turbidity * weights.turbidity +
                scores.tds * weights.tds
            ));

            // Classification & color based on WQI score
            let color, msg;
            if (wqi >= 90) {
                color = '#10b981'; // Emerald
                msg = 'Excellent water quality — all parameters within FAO optimal range for aquaculture.';
            } else if (wqi >= 70) {
                color = '#22c55e'; // Green
                msg = 'Good water quality — minor deviations from FAO standards, suitable for fish growth.';
            } else if (wqi >= 50) {
                color = '#f59e0b'; // Amber
                msg = 'Fair water quality — some parameters outside FAO range, corrective action recommended.';
            } else if (wqi >= 25) {
                color = '#f97316'; // Orange
                msg = 'Poor water quality — multiple parameters outside safe FAO limits, immediate action needed.';
            } else {
                color = '#ef4444'; // Red
                msg = 'Critical water quality — dangerous conditions for aquaculture, urgent intervention required.';
            }

            return { wqi, color, msg, scores, weights };
        };

        // Alias for use inside DOMContentLoaded scope
        const getWQIInfo = window.getWQIInfo;

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
            let lastAnalysisState = @json($latestAnalysis);
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
                     // Keep print report in sync
                     populatePrintReport();
                }

                const buildFAOGrowthRecommendation = (r, info) => {
                    const ph = Number.parseFloat(r.ph);
                    const temp = Number.parseFloat(r.temperature);
                    const turbidity = Number.parseFloat(r.turbidity);
                    const tds = Number.parseFloat(r.tds);

                    const values = [ph, temp, turbidity, tds];
                    if (values.every(v => !Number.isFinite(v) || v <= 0)) {
                        return 'Waiting for a valid latest reading to generate your recommendation.';
                    }

                    const issues = [];
                    if (Number.isFinite(ph)) {
                        if (ph < 6.5) issues.push('the water is too acidic');
                        else if (ph > 8.5) issues.push('the water is too alkaline');
                    }

                    if (Number.isFinite(temp)) {
                        if (temp < 25) issues.push('the water temperature is too cold');
                        else if (temp > 32) issues.push('the water temperature is too hot');
                    }

                    if (Number.isFinite(turbidity)) {
                        if (turbidity < 50) issues.push('the water clarity is too poor');
                    }

                    if (Number.isFinite(tds)) {
                        if (tds > 1500) issues.push('there is extreme mineralization');
                        else if (tds > 500) issues.push('the dissolved solids are slightly elevated');
                    }

                    if (info.wqi >= 90) {
                        return "The water quality is currently excellent. Therefore, keep your current management practices and continue monitoring to maintain these ideal conditions, because your fish will thrive here.";
                    } else if (info.wqi >= 70) {
                        let text = "The water quality is generally good and suitable for fish growth.";
                        if (issues.length > 0) {
                            text += ` However, because ${issues.join(' and ')}, you should keep a close eye on it or else it might start to stress the fish over time.`;
                        } else {
                            text += " Keep monitoring the pond to prevent any sudden drops in quality.";
                        }
                        return text;
                    } else {
                        let text = "The water quality is poor and not optimal for fish growth right now.";
                        if (issues.length > 0) {
                            text += ` This is primarily because ${issues.join(' and ')}. Therefore, we highly recommend taking corrective action immediately, or else the fish could experience severe health issues.`;
                        } else {
                            text += " Therefore, we highly recommend taking corrective action immediately to stabilize the pond.";
                        }
                        return text;
                    }
                };

                const updateOverallHealth = (r) => {
                    const info = window.getWQIInfo(r);
                    if (!info) return;
                    
                    const circle = document.getElementById('overall-health-circle');
                    const text = document.getElementById('overall-health-text');
                    const desc = document.getElementById('overall-health-desc');
                    const recommendation = document.getElementById('overall-health-recommendation');

                    if (circle && text) {
                        const circumference = 314.1;
                        const offset = circumference - (info.wqi / 100) * circumference;
                        circle.style.transition = 'stroke-dashoffset 1s ease-in-out, stroke 1s';
                        circle.setAttribute('stroke-dashoffset', offset);
                        circle.setAttribute('stroke', info.color);
                        text.textContent = Math.round(info.wqi) + '%';
                        if (desc) desc.textContent = info.msg;
                        if (recommendation) recommendation.textContent = buildFAOGrowthRecommendation(r, info);

                        // Update individual contributions in UI
                        const updateContrib = (id, score, weight, prefix) => {
                            const el = document.getElementById(id);
                            if (el) el.textContent = `${prefix}=${(score * weight).toFixed(1)}%`;
                        };
                        updateContrib('contrib-ph', info.scores.ph, info.weights.ph, 'pH');
                        updateContrib('contrib-temp', info.scores.temp, info.weights.temp, 'Temp');
                        updateContrib('contrib-turbidity', info.scores.turbidity, info.weights.turbidity, 'Turb');
                        updateContrib('contrib-tds', info.scores.tds, info.weights.tds, 'TDS');
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
                            if (val > 1500) color = '#ef4444'; // Red (Critical)
                            else if (val > 500) color = '#f59e0b'; // Amber (Warning)
                            else color = '#8b5cf6'; // Violet (Normal)
                        } else if (param === 'turbidity') {
                            if (val < 20) color = '#ef4444'; // Red (Critical)
                            else if (val < 50) color = '#f59e0b'; // Amber (Suboptimal)
                            else color = '#4f46e5'; // Indigo (Normal 50-100)
                        } else if (param === 'temp') {
                            if (val < 15 || val > 35) color = '#ef4444';
                            else if (val < 25 || val > 32) color = '#f59e0b';
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
                lastAnalysisState = analysis;
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
                    lastAnalysisState = analysis;
                    showPopup(analysis);
                    saveState(popupLastShownKey, analysis.id);

                    // Auto-Print removed as per user request
                    const readingId = analysis.water_reading_id;
                    if (readingId) {
                        populatePrintReport(analysis);
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
            function populatePrintReport(analysis = lastAnalysisState) {
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
                const getStatus = (param, val) => {
                    if (param === 'turbidity') {
                        if (val < 20) return { text: 'Critical', bg: 'background-color:#fef2f2;color:#991b1b;' };
                        if (val < 50) return { text: 'Warning', bg: 'background-color:#fffbeb;color:#92400e;' };
                        return { text: 'Normal', bg: 'background-color:#f0fdf4;color:#166534;' };
                    }
                    if (param === 'tds') {
                        if (val > 1500) return { text: 'Critical', bg: 'background-color:#fef2f2;color:#991b1b;' };
                        if (val > 500) return { text: 'Warning', bg: 'background-color:#fffbeb;color:#92400e;' };
                        return { text: 'Normal', bg: 'background-color:#f0fdf4;color:#166534;' };
                    }
                    if (param === 'ph') {
                        if (val < 6.0 || val > 9.0) return { text: 'Critical', bg: 'background-color:#fef2f2;color:#991b1b;' };
                        if (val < 6.5 || val > 8.5) return { text: 'Warning', bg: 'background-color:#fffbeb;color:#92400e;' };
                        return { text: 'Normal', bg: 'background-color:#f0fdf4;color:#166534;' };
                    }
                    if (param === 'temp') {
                        if (val < 15 || val > 35) return { text: 'Critical', bg: 'background-color:#fef2f2;color:#991b1b;' };
                        if (val < 25 || val > 32) return { text: 'Warning', bg: 'background-color:#fffbeb;color:#92400e;' };
                        return { text: 'Normal', bg: 'background-color:#f0fdf4;color:#166534;' };
                    }
                };

                const params = [
                    { name: 'Turbidity', value: parseFloat(reading.turbidity).toFixed(2) + '%', standard: '50-100%', status: getStatus('turbidity', reading.turbidity) },
                    { name: 'TDS', value: parseFloat(reading.tds).toFixed(2) + ' ppm', standard: '0-500', status: getStatus('tds', reading.tds) },
                    { name: 'pH Level', value: parseFloat(reading.ph).toFixed(2), standard: '6.5-8.5', status: getStatus('ph', reading.ph) },
                    { name: 'Water Temp', value: parseFloat(reading.temperature).toFixed(2) + '°C', standard: '25-32°C', status: getStatus('temp', reading.temperature) },
                ];

                tbody.innerHTML = params.map(p => {
                    return `<tr style="border-top:1px solid #e5e7eb;">
                        <td style="padding:12px 20px;font-size:13px;font-weight:500;color:#111827;">${p.name}</td>
                        <td style="padding:12px 20px;font-size:13px;color:#111827;">${p.value} <span style="font-size:10px;color:#9ca3af;font-style:italic;margin-left:4px;">(Standard: ${p.standard})</span></td>
                        <td style="padding:12px 20px;">
                            <span style="display:inline-block;padding:2px 10px;border-radius:9999px;font-size:11px;font-weight:600;${p.status.bg}">${p.status.text}</span>
                        </td>
                    </tr>`;
                }).join('');

                // AI Analysis Section - REVERTED to original AI Insight
                const aiSection = document.getElementById('printAiSection');
                if (!analysis || !analysis.ai_insight) {
                    aiSection.innerHTML = '<p style="color:#6b7280;font-style:italic;">Awaiting AI analysis for these readings...</p>';
                    return;
                }
                let aiHtml = `<h4 style="font-size:12px;font-weight:700;color:#1e3a5f;text-transform:uppercase;letter-spacing:0.5px;margin-bottom:8px;display:flex;align-items:center;gap:8px;">
                    <svg style="width:18px;height:18px;" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path></svg>
                    Analyzed by AquaSense
                </h4>`;
                aiHtml += `<p style="color:#374151;line-height:1.6;font-weight:500;">${analysis.ai_insight || 'No analysis insight available.'}</p>`;

                if (analysis.recommendations && analysis.recommendations.length > 0) {
                    const topRec = analysis.recommendations[0];
                    aiHtml += `<div style="margin-top:12px;padding-top:12px;border-top:1px solid rgba(219,234,254,0.5);">
                        <h5 style="font-size:11px;font-weight:700;color:#1e40af;text-transform:uppercase;margin-bottom:8px;">Recommendation:</h5>
                        <p style="font-size:13px;color:rgba(30,58,95,0.8);margin:0;padding-left:4px;">${topRec}</p>
                    </div>`;
                }
                aiSection.innerHTML = aiHtml;
            }

            window.triggerDashboardPrint = function() {
                populatePrintReport(lastAnalysisState || {});
                window.print();
            };

            window.showFormulaModal = function(paramType) {
                const modal = document.getElementById('formulaModal');
                const content = document.getElementById('modal-content');
                const reading = lastReadingState;
                if (!modal || !content) return;

                if (!reading) {
                    document.getElementById('modal-title-text').textContent = 'No Data Available';
                    content.innerHTML = `
                        <div class="flex flex-col items-center justify-center p-8 space-y-4 text-center">
                            <div class="w-16 h-16 bg-gray-50 rounded-full flex items-center justify-center text-gray-400 mb-2 border border-gray-100 shadow-sm">
                                <svg class="w-8 h-8 opacity-50" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                            </div>
                            <h4 class="text-lg font-bold text-gray-700 tracking-tight">No data gathered</h4>
                            <p class="text-xs text-gray-500 max-w-[250px] mx-auto leading-relaxed">The system is waiting for the sensor to transmit real-time readings to calculate the WQI.</p>
                        </div>
                    `;
                    modal.classList.remove('hidden');
                    document.body.style.overflow = 'hidden';
                    return;
                }

                const info = getWQIInfo(reading);
                let title = '';
                let val = 0;
                let weight = 0;
                let score = 0;
                let formulaText = '';
                let calcText = '';
                let formulaExplanation = '';
                let unit = '';

                switch(paramType) {
                    case 'ph':
                        title = 'pH Level (30%)';
                        val = parseFloat(reading.ph).toFixed(2);
                        weight = info.weights.ph;
                        score = info.scores.ph;
                        unit = 'pH';
                        if (reading.ph <= 7.5) {
                            formulaText = '(Current Reading / 7.5) * 100';
                            calcText = `(${val} / 7.5) * 100 = ${score.toFixed(1)}`;
                            formulaExplanation = "Since 7.5 is the peak perfect target for pH, we divide the reading by 7.5 to calculate its percentage towards that goal.";
                        } else {
                            formulaText = '((14.0 - Current Reading) / 6.5) * 100';
                            calcText = `((14.0 - ${val}) / 6.5) * 100 = ${score.toFixed(1)}`;
                            formulaExplanation = "The reading is above the 7.5 peak. We subtract the reading from the max pH of 14, and divide by the 6.5 difference (14.0 - 7.5) to scale it down.";
                        }
                        break;
                    case 'temp':
                        title = 'Temperature (25%)';
                        val = parseFloat(reading.temperature).toFixed(2);
                        weight = info.weights.temp;
                        score = info.scores.temp;
                        unit = '°C';
                        if (reading.temperature <= 28) {
                            formulaText = '(Current Reading / 28) * 100';
                            calcText = `(${val} / 28) * 100 = ${score.toFixed(1)}`;
                            formulaExplanation = "Since 28°C is the peak target, we divide the reading by 28 to see how close it is to perfection.";
                        } else {
                            formulaText = '((56 - Current Reading) / 28) * 100';
                            calcText = `((56 - ${val}) / 28) * 100 = ${score.toFixed(1)}`;
                            formulaExplanation = "The reading is above 28°C. We subtract it from an upper limit (56°C) and divide by 28 to symmetrically scale the score down.";
                        }
                        break;
                    case 'turbidity':
                        title = 'Turbidity (25%)';
                        val = parseFloat(reading.turbidity).toFixed(2);
                        weight = info.weights.turbidity;
                        score = info.scores.turbidity;
                        unit = '%';
                        formulaText = '(Current Reading / 100) * 100';
                        calcText = `(${val} / 100) * 100 = ${score.toFixed(1)}`;
                        formulaExplanation = "Turbidity clarity is measured purely out of a 100% scale, so we divide by 100 to map it continuously.";
                        break;
                    case 'tds':
                        title = 'TDS (20%)';
                        val = parseFloat(reading.tds).toFixed(2);
                        weight = info.weights.tds;
                        score = info.scores.tds;
                        unit = 'mg/L';
                        formulaText = '100 - (Current Reading / 10)';
                        calcText = `100 - (${val} / 10) = ${score.toFixed(1)}`;
                        formulaExplanation = "For TDS, 0 is the perfect score. It decreases by 1% for every 10 mg/L, so we divide the reading by 10 to act as the exact deduction algorithm.";
                        break;
                }

                document.getElementById('modal-title-text').textContent = title;
                // Build the WQI breakdown bar showing all 4 contributions
                const contribPH = (info.scores.ph * info.weights.ph).toFixed(1);
                const contribTemp = (info.scores.temp * info.weights.temp).toFixed(1);
                const contribTurb = (info.scores.turbidity * info.weights.turbidity).toFixed(1);
                const contribTDS = (info.scores.tds * info.weights.tds).toFixed(1);
                const currentParam = paramType;

                const paramColors = { ph: '#10b981', temp: '#f59e0b', turbidity: '#4f46e5', tds: '#8b5cf6' };
                const paramLabels = { ph: 'pH', temp: 'Temp', turbidity: 'Turb', tds: 'TDS' };
                const contribs = { ph: contribPH, temp: contribTemp, turbidity: contribTurb, tds: contribTDS };

                let wqiBarHtml = '';
                for (const [key, label] of Object.entries(paramLabels)) {
                    const isActive = key === currentParam;
                    const opacity = isActive ? '1' : '0.35';
                    const ring = isActive ? 'ring-2 ring-offset-1 ring-indigo-400' : '';
                    wqiBarHtml += `<div class="flex flex-col items-center ${ring} rounded-lg px-1.5 py-1" style="opacity:${opacity}">
                        <div class="w-3 h-3 rounded-full mb-1" style="background:${paramColors[key]}"></div>
                        <span class="text-[10px] font-black text-gray-700">${contribs[key]}%</span>
                        <span class="text-[8px] font-bold text-gray-400 uppercase">${label}</span>
                    </div>`;
                }

                content.innerHTML = `
                    <!-- WQI Overview -->
                    <div class="p-4 bg-gradient-to-r from-indigo-50 to-blue-50 rounded-2xl border border-indigo-100 space-y-3">
                        <div class="flex items-center justify-between">
                            <div>
                                <p class="text-[9px] font-black text-indigo-400 uppercase tracking-[2px] mb-1">Peak Target WQI</p>
                                <p class="text-[11px] text-gray-500 font-medium italic">WQI = Σ (W<sub>i</sub> × q<sub>i</sub>)</p>
                            </div>
                            <div class="text-right">
                                <span class="text-2xl font-black tracking-tight" style="color:${info.color}">${Math.round(info.wqi)}%</span>
                                <p class="text-[9px] font-bold text-gray-400 uppercase">Overall</p>
                            </div>
                        </div>
                        <div class="flex justify-around pt-2 border-t border-indigo-100/50">
                            ${wqiBarHtml}
                        </div>
                    </div>

                    <!-- Parameter Reading -->
                    <div class="p-5 bg-indigo-50/50 rounded-2xl border border-indigo-100 space-y-4">
                        <div class="flex justify-between items-center text-[10px] font-bold text-indigo-400 uppercase tracking-widest">
                            <span>Calculation Target</span>
                            <span class="bg-white px-2 py-0.5 rounded-full border border-indigo-100">${paramType === 'ph' ? 'Target: 7.5 pH' : paramType === 'temp' ? 'Target: 28 °C' : paramType === 'turbidity' ? 'Clarity Percentage' : 'Target: 0 mg/L (Max 1000)'}</span>
                        </div>
                        <div class="flex justify-between items-end">
                            <span class="text-sm font-bold text-gray-600">Real-time Reading</span>
                            <div class="text-right">
                                <span class="text-3xl font-black text-indigo-600 tracking-tighter">${val}</span>
                                <span class="text-xs font-bold text-indigo-400 ml-1 uppercase">${unit}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <div class="flex items-center gap-2">
                             <div class="h-px flex-1 bg-gray-100"></div>
                             <span class="text-[9px] font-black text-gray-400 uppercase tracking-[2px]">Sub-index Calculation (q<sub>i</sub>)</span>
                             <div class="h-px flex-1 bg-gray-100"></div>
                        </div>
                        <div class="p-4 bg-gray-50 rounded-2xl border border-gray-100 font-mono text-xs flex items-center gap-4">
                             <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-white border border-gray-200 flex items-center justify-center text-indigo-600 shadow-sm">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                             </div>
                             <div class="flex-1">
                                <p class="text-[10px] text-gray-400 font-bold uppercase mb-1">Standard Formula</p>
                                <span class="font-bold text-gray-700 italic tracking-tight block">${formulaText}</span>
                                <p class="text-[9px] text-indigo-600 font-medium leading-relaxed bg-white border border-indigo-100/50 rounded p-2 mt-2 shadow-sm">${formulaExplanation}</p>
                             </div>
                        </div>
                    </div>

                    <div class="space-y-4">
                        <div class="relative pl-8 space-y-6 before:absolute before:left-[15px] before:top-2 before:bottom-2 before:w-0.5 before:bg-indigo-100">
                            <!-- Step 1 -->
                            <div class="relative">
                                <div class="absolute -left-10 w-5 h-5 rounded-full bg-white border-4 border-indigo-500 z-10"></div>
                                <div>
                                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-wider mb-1">Step 01: Sub-index Score (q<sub>i</sub>)</p>
                                    <div class="p-3 bg-white rounded-xl border border-gray-100 shadow-sm font-mono text-[11px] text-gray-600 italic">
                                        ${calcText}
                                    </div>
                                </div>
                            </div>
                            <!-- Step 2 -->
                            <div class="relative">
                                <div class="absolute -left-10 w-5 h-5 rounded-full bg-white border-4 border-indigo-500 z-10"></div>
                                <div>
                                    <p class="text-[10px] font-black text-indigo-500 uppercase tracking-wider mb-1">Step 02: Weighted Contribution (W<sub>i</sub> × q<sub>i</sub>)</p>
                                    <div class="p-4 bg-white rounded-xl border border-gray-100 shadow-sm space-y-2">
                                        <div class="flex justify-between text-xs text-gray-500">
                                            <span>q<sub>i</sub> × W<sub>i</sub></span>
                                            <span class="font-bold">${score.toFixed(1)} × ${(weight * 100)}%</span>
                                        </div>
                                        <div class="h-px bg-gray-50"></div>
                                        <div class="flex justify-between items-center">
                                            <span class="text-xs font-bold text-gray-900">Contribution to WQI</span>
                                            <span class="text-xl font-black text-indigo-600 tracking-tight">${(score * weight).toFixed(1)}%</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="p-4 bg-amber-50 rounded-2xl border border-amber-100 flex gap-3 shadow-sm">
                        <div class="flex-shrink-0 w-8 h-8 rounded-xl bg-white flex items-center justify-center text-sm shadow-sm">📐</div>
                        <p class="text-[11px] text-amber-900 leading-relaxed font-medium">
                            This parameter contributes <b>${(score * weight).toFixed(1)}%</b> out of its maximum <b>${(weight * 100)}%</b> toward the overall WQI of <b>${Math.round(info.wqi)}%</b>.
                            ${score < 100 ? 'Points are proportionally calculated based on deviation from the perfect target.' : 'Full contribution — the reading is perfectly at the target value.'}
                        </p>
                    </div>

                    <div class="p-3 bg-gray-50 rounded-xl border border-gray-100">
                        <p class="text-[9px] text-gray-400 font-medium text-center italic">
                            Calculation Mode: Exact Proportion Peak Target Algorithm
                        </p>
                    </div>
                `;

                modal.classList.remove('hidden');
                document.body.style.overflow = 'hidden';
            };

            window.hideFormulaModal = function() {
                document.getElementById('formulaModal').classList.add('hidden');
                document.body.style.overflow = 'auto';
            };

            initPopupPosition();
            restorePopupIfActive();
            updateGauges(lastReadingState, false);
            fetchLatestAnalysis(true);
            setInterval(() => fetchLatestAnalysis(true), analysisPollIntervalMs);
        });
    </script>
    <!-- WQI Formula Modal -->
    <div id="formulaModal" class="fixed inset-0 z-50 overflow-y-auto hidden" aria-labelledby="modal-title" role="dialog" aria-modal="true">
        <div class="flex items-center justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
            <div class="fixed inset-0 bg-black/40 backdrop-blur-sm transition-opacity" aria-hidden="true" onclick="hideFormulaModal()"></div>
            <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
            <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-md sm:w-full border border-gray-100">
                <div class="px-6 py-4 bg-indigo-50 border-b border-indigo-100">
                    <h3 class="text-sm font-bold text-indigo-900 flex items-center justify-between gap-2" id="modal-title">
                        <div class="flex items-center gap-2">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd" />
                            </svg>
                            <span class="uppercase tracking-wider">WQI Solution: <span id="modal-title-text" class="normal-case">Parameter Calculation</span></span>
                        </div>
                        <button onclick="hideFormulaModal()" class="text-indigo-400 hover:text-indigo-600 transition-colors p-1 rounded-lg">
                            <svg class="h-5 w-5" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </button>
                    </h3>
                </div>
                <div class="bg-white p-6 relative">
                    <div id="modal-content" class="space-y-6">
                        <!-- Content will be injected here -->
                    </div>
                </div>
                <div class="px-6 py-4 bg-gray-50 border-t border-gray-100 flex justify-end">
                    <button type="button" class="inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-4 py-2 bg-white text-xs font-bold text-gray-700 hover:bg-gray-50 hover:text-indigo-600 transition-all focus:outline-none" onclick="hideFormulaModal()">Close Details</button>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
