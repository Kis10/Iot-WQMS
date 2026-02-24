@props(['position' => 'bottom-right'])

<div x-data="{ 
    open: false, 
    muted: localStorage.getItem('sounds_muted') === 'true',
    volume: localStorage.getItem('sounds_volume') || 100,
    aiEnabled: localStorage.getItem('sounds_ai_enabled') !== 'false',
    alertEnabled: localStorage.getItem('sounds_alert_enabled') !== 'false',
    
    toggleMute() {
        this.muted = !this.muted;
        localStorage.setItem('sounds_muted', this.muted);
        document.dispatchEvent(new CustomEvent('sound-settings-updated'));
    },
    
    updateVolume(val) {
        this.volume = val;
        localStorage.setItem('sounds_volume', val);
        document.dispatchEvent(new CustomEvent('sound-settings-updated'));
        
        // Update actual audio elements volume if they exist
        const aiAudio = document.getElementById('globalAiSound');
        const alertAudio = document.getElementById('globalAlertSound');
        if (aiAudio) aiAudio.volume = val / 100;
        if (alertAudio) alertAudio.volume = val / 100;
    },
    
    toggleAi() {
        this.aiEnabled = !this.aiEnabled;
        localStorage.setItem('sounds_ai_enabled', this.aiEnabled);
        document.dispatchEvent(new CustomEvent('sound-settings-updated'));
    },
    
    toggleAlert() {
        this.alertEnabled = !this.alertEnabled;
        localStorage.setItem('sounds_alert_enabled', this.alertEnabled);
        document.dispatchEvent(new CustomEvent('sound-settings-updated'));
    },
    
    testSound(type) {
        const id = type === 'ai' ? 'globalAiSound' : 'globalAlertSound';
        const el = document.getElementById(id);
        if (el) {
            el.currentTime = 0;
            el.volume = this.volume / 100;
            if (!this.muted) {
                el.play().catch(e => console.error('Test sound failed:', e));
            } else {
                alert('Sounds are currently muted!');
            }
        }
    }
}" 
class="fixed {{ $position === 'bottom-right' ? 'bottom-6 right-6' : 'bottom-6 left-6' }} z-[60] print:hidden">
    
    <!-- Print Button (Floating above Speaker) -->
    <button onclick="window.print()" 
            class="mb-3 w-12 h-12 rounded-full bg-white shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:text-indigo-600 hover:shadow-xl transition-all duration-300 focus:outline-none">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z"></path>
        </svg>
    </button>

    <!-- Floating Button (Speaker) -->
    <button @click="open = !open" 
            class="w-12 h-12 rounded-full bg-white shadow-lg border border-gray-200 flex items-center justify-center text-gray-600 hover:text-indigo-600 hover:shadow-xl transition-all duration-300 focus:outline-none">
        <template x-if="muted || (!aiEnabled && !alertEnabled)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" stroke-clip-rule="evenodd" />
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 14l2-2m0 0l2-2m-2 2l-2-2m2 2l2 2" />
            </svg>
        </template>
        <template x-if="!muted && (aiEnabled || alertEnabled)">
            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.536 8.464a5 5 0 010 7.072m2.828-9.9a9 9 0 010 12.728M5.586 15H4a1 1 0 01-1-1v-4a1 1 0 011-1h1.586l4.707-4.707C10.923 3.663 12 4.109 12 5v14c0 .891-1.077 1.337-1.707.707L5.586 15z" />
            </svg>
        </template>
    </button>

    <!-- Settings Panel -->
    <div x-show="open" 
         x-transition:enter="transition ease-out duration-200"
         x-transition:enter-start="opacity-0 scale-95 translate-y-2"
         x-transition:enter-end="opacity-100 scale-100 translate-y-0"
         x-transition:leave="transition ease-in duration-150"
         x-transition:leave-start="opacity-100 scale-100 translate-y-0"
         x-transition:leave-end="opacity-0 scale-95 translate-y-2"
         @click.away="open = false"
         class="absolute bottom-16 {{ $position === 'bottom-right' ? 'right-0' : 'left-0' }} w-72 bg-white rounded-2xl shadow-2xl border border-gray-100 overflow-hidden"
         x-cloak>
        
        <div class="px-5 py-4 bg-indigo-50 border-b border-indigo-100">
            <h3 class="text-sm font-bold text-indigo-900 flex items-center gap-2">
                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M9.383 3.076A1 1 0 0110 4v12a1 1 0 01-1.707.707L4.586 13H3a1 1 0 01-1-1V8a1 1 0 011-1h1.586l3.707-3.707a1 1 0 011.09-.217zM14.657 2.929a1 1 0 011.414 0A9.972 9.972 0 0119 10a9.972 9.972 0 01-2.929 7.071 1 1 0 01-1.414-1.414A7.971 7.971 0 0017 10c0-2.21-.894-4.208-2.343-5.657a1 1 0 010-1.414zm-2.829 2.828a1 1 0 011.415 0A5.983 5.983 0 0115 10a5.983 5.983 0 01-1.414 4.243 1 1 0 11-1.415-1.415A3.987 3.987 0 0013 10a3.987 3.987 0 00-1.414-2.829 1 1 0 010-1.415z" clip-rule="evenodd" />
                </svg>
                Sound Settings
            </h3>
        </div>

        <div class="p-5 space-y-6">
            <!-- Mute Toggle -->
            <div class="flex items-center justify-between">
                <span class="text-sm font-medium text-gray-700">Mute All Sounds</span>
                <button @click="toggleMute()" 
                        class="relative inline-flex h-6 w-11 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                        :class="muted ? 'bg-indigo-600' : 'bg-gray-200'">
                    <span class="pointer-events-none inline-block h-5 w-5 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                          :class="muted ? 'translate-x-5' : 'translate-x-0'"></span>
                </button>
            </div>

            <!-- Volume Slider -->
            <div class="space-y-2">
                <div class="flex justify-between">
                    <span class="text-sm font-medium text-gray-700">Master Volume</span>
                    <span class="text-xs font-bold text-indigo-600" x-text="volume + '%'"></span>
                </div>
                <input type="range" min="0" max="100" x-model="volume" @input="updateVolume($event.target.value)"
                       class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-indigo-600">
            </div>

            <div class="pt-2 space-y-4">
                <p class="text-[10px] font-bold text-gray-400 uppercase tracking-wider">Sound Notifications</p>
                
                <!-- AI Sound Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600">AI Analysis Voice</span>
                        <button @click="testSound('ai')" class="text-indigo-500 hover:text-indigo-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                        </button>
                    </div>
                    <button @click="toggleAi()" 
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="aiEnabled ? 'bg-indigo-500' : 'bg-gray-200'">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="aiEnabled ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                </div>

                <!-- Alert Sound Toggle -->
                <div class="flex items-center justify-between">
                    <div class="flex items-center gap-2">
                        <span class="text-xs font-medium text-gray-600">Critical Alerts</span>
                        <button @click="testSound('alert')" class="text-indigo-500 hover:text-indigo-700">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z"/></svg>
                        </button>
                    </div>
                    <button @click="toggleAlert()" 
                            class="relative inline-flex h-5 w-9 flex-shrink-0 cursor-pointer rounded-full border-2 border-transparent transition-colors duration-200 ease-in-out focus:outline-none"
                            :class="alertEnabled ? 'bg-indigo-500' : 'bg-gray-200'">
                        <span class="pointer-events-none inline-block h-4 w-4 transform rounded-full bg-white shadow ring-0 transition duration-200 ease-in-out"
                              :class="alertEnabled ? 'translate-x-4' : 'translate-x-0'"></span>
                    </button>
                </div>
            </div>
        </div>

        <div class="px-5 py-3 bg-gray-50 border-t border-gray-100 italic">
            <p class="text-[10px] text-gray-500 text-center">Settings are saved automatically</p>
        </div>
    </div>
</div>
