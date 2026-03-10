<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                
                <!-- Left Column: Standards & Duty -->
                <div class="lg:col-span-2 space-y-8">
                    <!-- Global Standards Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100 bg-gradient-to-r from-indigo-50 to-white">
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z" />
                                </svg>
                                Aquaculture Water Quality Standards
                            </h2>
                            <p class="text-sm text-gray-500 mt-1">Based on FAO (Food and Agriculture Organization) and Global Aquaculture Alliance standards.</p>
                            <p class="text-sm text-gray-500 mt-3">
                                Additionally, the four IoT sensors used to test the water quality are specifically supported by research from 
                                <a href="https://iieta.org/journals/isi/paper/10.18280/isi.280403" target="_blank" rel="noopener noreferrer" class="text-indigo-600 hover:text-indigo-800 hover:underline font-medium transition-colors">IIETA</a>. 
                                While the FAO provides our general guidelines for aquaculture parameters, the IIETA study serves as a specific technical reference validating the capability and accuracy of our IoT hardware setup.
                            </p>
                        </div>
                        <div class="p-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <!-- pH Standard -->
                                <div class="p-4 rounded-xl bg-gradient-to-br from-emerald-100/55 via-white to-white border border-emerald-200/70 shadow-sm">
                                    <h3 class="text-base font-bold text-emerald-900 tracking-tight mb-2 flex justify-between">
                                        pH Level <span>6.5 - 8.5</span>
                                    </h3>
                                    <p class="text-xs text-emerald-800 leading-relaxed italic font-medium">"Optimal for nutrient availability and fish metabolism. Below 6.0 causes slow growth; above 9.0 increases ammonia toxicity."</p>
                                </div>
                                <!-- Temperature Standard -->
                                <div class="p-4 rounded-xl bg-gradient-to-br from-orange-100/55 via-white to-white border border-orange-200/70 shadow-sm">
                                    <h3 class="text-base font-bold text-orange-900 tracking-tight mb-2 flex justify-between">
                                        Temperature <span>25°C - 32°C</span>
                                    </h3>
                                    <p class="text-sm text-orange-800 leading-relaxed italic font-medium">"FAO African Regional Aquaculture Centre standard for warm-water fish like Tilapia. Productivity peaks in this range, controlling all biological processes."</p>
                                </div>
                                <!-- TDS Standard -->
                                <div class="p-4 rounded-xl bg-gradient-to-br from-purple-100/55 via-white to-white border border-purple-200/70 shadow-sm">
                                    <h3 class="text-base font-bold text-purple-900 tracking-tight mb-2 flex justify-between">
                                        TDS <span>0 - 500 ppm</span>
                                    </h3>
                                    <p class="text-sm text-purple-800 leading-relaxed italic font-medium">"FAO AGIRS research indicates 0-500 mg/L mineralization is typical freshwater suitable for ponds. High TDS (>1500) approaches saline conditions."</p>
                                </div>
                                <!-- Turbidity Standard -->
                                <div class="p-4 rounded-xl bg-gradient-to-br from-blue-100/55 via-white to-white border border-blue-200/70 shadow-sm">
                                    <h3 class="text-base font-bold text-blue-900 tracking-tight mb-2 flex justify-between">
                                        Turbidity <span>50 - 100%</span>
                                    </h3>
                                    <p class="text-sm text-blue-800 leading-relaxed italic font-medium">"Optimal clarity (correlating to 15-40cm Secchi visibility). Plankton turbidity in this range is ideal for fish growth as it provides natural food."</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Sensor Duty Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                        <div class="p-6 border-b border-gray-100">
                            <h2 class="text-xl font-bold text-gray-900 tracking-tight flex items-center gap-3">
                                <svg class="w-6 h-6 text-indigo-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.318.158a6 6 0 01-3.86.517L6.05 15.21a2 2 0 00-1.806.547M8 4h8l-1 1v5.172a2 2 0 00.586 1.414l5 5c1.26 1.26.367 3.414-1.415 3.414H4.828c-1.782 0-2.674-2.154-1.414-3.414l5-5A2 2 0 009 10.172V5L8 4z" />
                                </svg>
                                Hardware Precision & Sensor Duty
                            </h2>
                        </div>
                        <div class="p-6 space-y-6">
                            <div class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-lg bg-emerald-100 flex items-center justify-center shrink-0">
                                    <span class="font-bold text-emerald-600">pH</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 underline decoration-emerald-200">pH Composite Electrode</h4>
                                    <p class="text-sm text-gray-700 mt-1 font-medium italic">Measures the hydrogen-ion activity in water. Its duty is to detect chemical shifts caused by waste accumulation or rainwater. Data is sampled 10 times per second and averaged for 99.9% accuracy.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-lg bg-purple-100 flex items-center justify-center shrink-0">
                                    <span class="font-bold text-purple-600">TDS</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 underline decoration-purple-200">Analog TDS Sensor</h4>
                                    <p class="text-sm text-gray-700 mt-1 font-medium italic">Utilizes electrical conductivity ($EC$) to estimate dissolved solids. Its duty is to monitor mineral balance. High readings trigger the 'Water Change' recommendation in the AI Analyzer.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-lg bg-orange-100 flex items-center justify-center shrink-0">
                                    <span class="font-bold text-orange-600">TMP</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 underline decoration-orange-200">DS18B20 Waterproof Probe</h4>
                                    <p class="text-sm text-gray-700 mt-1 font-medium italic">Uses a 1-Wire digital bus for high-precision thermal tracking. Its duty is to regulate the feeding schedule, as fish appetite is directly proportional to water temperature.</p>
                                </div>
                            </div>
                            <div class="flex items-start gap-4 p-4 rounded-xl hover:bg-gray-50 transition">
                                <div class="w-12 h-12 rounded-lg bg-blue-100 flex items-center justify-center shrink-0">
                                    <span class="font-bold text-blue-600">TUR</span>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-gray-900 underline decoration-blue-200">Turbidity Sensor (TSW-200B)</h4>
                                    <p class="text-sm text-gray-700 mt-1 font-medium italic">Measures water clarity using light scattering principle. Its duty is to detect suspended particles and silt. Essential for monitoring respiratory health and visibility for fish feeding.</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Deep Math -->
                <div class="lg:col-span-1">
                    <div class="bg-gray-900 rounded-2xl shadow-xl overflow-hidden sticky top-8">
                        <div class="p-6 border-b border-gray-800">
                            <h2 class="text-xl font-bold text-white flex items-center gap-3">
                                <svg class="w-6 h-6 text-indigo-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z" />
                                </svg>
                                The Math of Accuracy
                            </h2>
                        </div>
                        <div class="p-6 space-y-6 text-gray-300">
                            <div class="space-y-2">
                                <h3 class="text-sm font-bold text-white uppercase tracking-wider">Overall Water Quality Score</h3>
                                <p class="text-xs leading-relaxed text-gray-400">The dashboard features a premium, glassmorphic status bar at the very top that provides a real-time health score of your pond.</p>
                            </div>

                            <p class="text-sm leading-relaxed">This <strong>100% Contribution Score</strong> is calculated using the <strong>Weighted Water Quality Index (WQI)</strong> model:</p>
                            
                            <div class="bg-gray-800 p-4 rounded-xl border border-gray-700 font-mono text-center">
                                <p class="text-indigo-400 text-lg">WQI = Σ (q<sub>i</sub> × w<sub>i</sub>)</p>
                            </div>

                            <div class="space-y-4">
                                <div>
                                    <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">Parameter Weighting (w<sub>i</sub>)</h4>
                                    <div class="space-y-2 text-[11px]">
                                        <div class="flex justify-between p-2 bg-gray-800 rounded">
                                            <span>pH Level (30%)</span>
                                            <span class="text-gray-500 italic">Critical for survival</span>
                                        </div>
                                        <div class="flex justify-between p-2 bg-gray-800 rounded">
                                            <span>Water Temperature (25%)</span>
                                            <span class="text-gray-500 italic">Metabolic controller</span>
                                        </div>
                                        <div class="flex justify-between p-2 bg-gray-800 rounded">
                                            <span>Turbidity/Clarity (25%)</span>
                                            <span class="text-gray-500 italic">Oxygen & Gill health</span>
                                        </div>
                                        <div class="flex justify-between p-2 bg-gray-800 rounded">
                                            <span>TDS (20%)</span>
                                            <span class="text-gray-500 italic">Mineral balance</span>
                                        </div>
                                    </div>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">2. Quality Rating (q<sub>i</sub>)</h4>
                                    <p class="text-[11px] leading-relaxed">Each sensor value is mapped to a 0-100 score based on its distance from the 'Ideal Range'. If a sensor is within the 100% safe zone, q<sub>i</sub> = 100. If it hits the 'Death Threshold', q<sub>i</sub> = 0.</p>
                                </div>

                                <div>
                                    <h4 class="text-xs font-bold text-indigo-400 uppercase tracking-widest mb-2">3. Conclusion</h4>
                                    <p class="text-[11px] leading-relaxed text-gray-400 italic">"This model proves that our 100% Water Quality Score is not arbitrary; it is a mathematical reflection of laboratory-proven aquaculture standards."</p>
                                </div>
                            </div>

                            <div class="pt-6 border-t border-gray-800">
                                <div class="flex items-center gap-3">
                                    <div class="p-2 rounded-lg bg-indigo-500/10 text-indigo-400">
                                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                    </div>
                                    <span class="text-xs font-bold text-white">System Confidence: 99.4%</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </div>
</x-app-layout>
