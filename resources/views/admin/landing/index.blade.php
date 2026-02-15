<x-app-layout>
    <!-- Scoped Styles -->
    <style>
        .landing-editor { font-family: 'Outfit', sans-serif; }
        .landing-editor .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .landing-editor .gradient-text { background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        .landing-editor .sensor-card:hover { transform: translateY(-10px); box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1); }

        /* Editable Hover */
        .editable-hover { position: relative; cursor: text; border-radius: 4px; transition: outline 0.15s; }
        .editable-hover:hover { outline: 2px dashed #8b5cf6; }
        .editable-hover:hover::after { content: 'Double-click to edit'; position: absolute; top: -22px; left: 0; background: #8b5cf6; color: white; font-size: 10px; padding: 2px 8px; border-radius: 4px; white-space: nowrap; z-index: 20; pointer-events: none; }

        /* Inline Textarea */
        .input-box { width: 100%; background: transparent; border: none; outline: none; resize: none; font-family: inherit; font-size: inherit; font-weight: inherit; line-height: inherit; text-align: inherit; color: inherit; padding: 0; margin: 0; overflow: hidden; box-shadow: none; }
        .input-box:focus { outline: 2px solid #8b5cf6; outline-offset: 2px; background: rgba(139, 92, 246, 0.05); border-radius: 4px; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <div class="landing-editor h-[calc(100vh-65px)] overflow-y-auto" x-data="landingEditor(@js($contents))">

        <!-- Floating Save Button -->
        <div class="fixed top-20 right-8 z-50 flex items-center gap-3">
            <div x-show="showSuccess" x-transition x-cloak class="flex items-center gap-2 text-green-600 font-bold text-sm bg-green-50 px-3 py-1.5 rounded-full border border-green-100 shadow-lg">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                Saved!
            </div>
            <button @click="saveChanges" :disabled="saving" class="bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white px-6 py-2.5 rounded-xl shadow-xl font-bold transition flex items-center gap-2 text-sm">
                <svg x-show="saving" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <span x-show="!saving && !showSuccess">Save Changes</span>
                <span x-show="saving">Saving...</span>
                <span x-show="showSuccess && !saving">Saved!</span>
            </button>
        </div>

        <!-- ============================================== -->
        <!-- LIVE PREVIEW - EXACT REPLICA OF LANDING PAGE   -->
        <!-- ============================================== -->

        <!-- Navbar -->
        <nav class="w-full z-40 glass border-b border-gray-100 py-4">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex items-center w-full">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-xl overflow-hidden shadow-sm bg-white p-1">
                            <img src="{{ asset('img/logo/logo-wq.png') }}" class="w-full h-full object-contain" />
                        </div>
                        <span class="text-2xl font-bold tracking-tight text-gray-900">{{ config('app.name', 'AquaSense') }}</span>
                    </div>
                    <div class="hidden md:flex items-center space-x-8 text-sm font-semibold ml-auto">
                        <span class="text-gray-400 cursor-not-allowed">Home</span>
                        <span class="text-gray-400 cursor-not-allowed">Sensors</span>
                        <span class="text-gray-400 cursor-not-allowed">Services</span>
                        <span class="text-gray-400 cursor-not-allowed">Contact</span>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <section class="relative min-h-[85vh] flex items-center justify-center bg-slate-900 overflow-hidden pt-32 pb-20 group/hero">
            <!-- Background Image -->
            <div class="absolute inset-0 z-0">
                <template x-if="heroBgPreview">
                    <img :src="heroBgPreview" class="w-full h-full object-cover opacity-40">
                </template>
                <template x-if="!heroBgPreview && data.hero_bg && data.hero_bg.value">
                    <img :src="data.hero_bg.value.startsWith('http') ? data.hero_bg.value : (
                        data.hero_bg.value.startsWith('/') ? data.hero_bg.value : (
                        data.hero_bg.value.startsWith('img/') ? '/' + data.hero_bg.value :
                        '/storage/' + data.hero_bg.value
                    ))"
                         class="w-full h-full object-cover opacity-40"
                         onerror="this.style.display='none'">
                </template>
            </div>
            <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 via-slate-900/40 to-slate-950/40 z-0"></div>

            <!-- BG Edit Button (Top Right) -->
            <div class="absolute top-8 right-8 z-30 opacity-0 group-hover/hero:opacity-100 transition duration-300">
                <button @click="showBgModal = true" class="bg-white/10 hover:bg-white/20 backdrop-blur text-white p-2.5 rounded-full border border-white/20 transition shadow-lg" title="Change Background">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                </button>
            </div>

            <!-- Hero Content -->
            <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
                <br><br>
                <!-- Hero Title -->
                <div class="mb-8" x-data="{ editing: false }">
                    <h1 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.heroTitle.focus())" class="editable-hover text-5xl md:text-7xl lg:text-8xl font-black text-white leading-tight tracking-tight"
                        x-html="data.hero_title.value"></h1>
                    <div x-show="editing" x-cloak>
                        <textarea x-ref="heroTitle" x-model="data.hero_title.value"
                            class="input-box text-5xl md:text-7xl lg:text-8xl font-black text-white leading-tight tracking-tight text-center"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })"
                            @click.away="editing = false"></textarea>
                        <p class="text-white/50 text-xs mt-1">Supports HTML</p>
                    </div>
                </div>

                <!-- Hero Subtitle -->
                <div class="mb-12" x-data="{ editing: false }">
                    <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.heroSub.focus())" class="editable-hover text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto font-medium leading-relaxed opacity-90"
                       x-text="data.hero_subtitle.value"></p>
                    <textarea x-ref="heroSub" x-show="editing" x-cloak x-model="data.hero_subtitle.value"
                        class="input-box text-xl md:text-2xl text-blue-100 font-medium leading-relaxed text-center max-w-3xl mx-auto"
                        @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                        x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })"
                        @click.away="editing = false"></textarea>
                </div>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="mb-6 flex justify-center" x-data="{ editing: false }">
                        <div x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.badge.focus())" class="editable-hover inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-bold"
                             x-text="data.mission_badge.value"></div>
                        <input x-ref="badge" x-show="editing" x-cloak x-model="data.mission_badge.value" class="input-box text-sm font-bold text-blue-600 bg-blue-50 rounded-full px-4 py-1.5 text-center w-auto inline-block" @click.away="editing = false">
                    </div>

                    <!-- Title -->
                    <div class="mb-8" x-data="{ editing: false }">
                        <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.misTitle.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight"
                            x-text="data.mission_title.value"></h2>
                        <textarea x-ref="misTitle" x-show="editing" x-cloak x-model="data.mission_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>

                    <!-- Text -->
                    <div x-data="{ editing: false }">
                        <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.misText.focus())" class="editable-hover text-xl text-gray-600 leading-relaxed font-light"
                           x-text="data.mission_text.value"></p>
                        <textarea x-ref="misText" x-show="editing" x-cloak x-model="data.mission_text.value"
                            class="input-box text-xl text-gray-600 leading-relaxed font-light text-center"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })"
                            @click.away="editing = false"></textarea>
                    </div>
                </div>
            </div>
        </section>

        <!-- Sensors Section -->
        <section class="py-24 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="mb-4" x-data="{ editing: false }">
                        <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sensTitle.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight" x-text="data.sensors_title.value"></h2>
                        <textarea x-ref="sensTitle" x-show="editing" x-cloak x-model="data.sensors_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>
                    <div x-data="{ editing: false }">
                        <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sensSub.focus())" class="editable-hover text-gray-500 text-lg" x-text="data.sensors_subtitle.value"></p>
                        <textarea x-ref="sensSub" x-show="editing" x-cloak x-model="data.sensors_subtitle.value" rows="1" class="input-box text-gray-500 text-lg text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>
                </div>

                <!-- Sensor Cards (Full size, editable titles & descriptions) -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-12">
                    <!-- pH -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path></svg>
                        </div>
                        <div x-data="{ editing: false }">
                            <h3 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s1t.focus())" class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor1_title.value"></h3>
                            <input x-ref="s1t" x-show="editing" x-cloak x-model="data.sensor1_title.value" class="input-box text-xl font-bold text-gray-900 mb-4 tracking-tight" @click.away="editing = false">
                        </div>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s1d.focus())" class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor1_desc.value"></p>
                            <textarea x-ref="s1d" x-show="editing" x-cloak x-model="data.sensor1_desc.value" class="input-box text-gray-600 text-sm leading-relaxed" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                        </div>
                    </div>

                    <!-- Turbidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z"></path><circle cx="12" cy="14" r="1" fill="currentColor"></circle><circle cx="15" cy="13" r="0.5" fill="currentColor"></circle><circle cx="9" cy="13" r="0.5" fill="currentColor"></circle></svg>
                        </div>
                        <div x-data="{ editing: false }">
                            <h3 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s2t.focus())" class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor2_title.value"></h3>
                            <input x-ref="s2t" x-show="editing" x-cloak x-model="data.sensor2_title.value" class="input-box text-xl font-bold text-gray-900 mb-4 tracking-tight" @click.away="editing = false">
                        </div>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s2d.focus())" class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor2_desc.value"></p>
                            <textarea x-ref="s2d" x-show="editing" x-cloak x-model="data.sensor2_desc.value" class="input-box text-gray-600 text-sm leading-relaxed" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                        </div>
                    </div>

                    <!-- TDS -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path><circle cx="12" cy="14" r="1.5" fill="currentColor"></circle><circle cx="15.5" cy="12.5" r="1" fill="currentColor"></circle><circle cx="8.5" cy="12.5" r="1" fill="currentColor"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v6"></path></svg>
                        </div>
                        <div x-data="{ editing: false }">
                            <h3 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s3t.focus())" class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor3_title.value"></h3>
                            <input x-ref="s3t" x-show="editing" x-cloak x-model="data.sensor3_title.value" class="input-box text-xl font-bold text-gray-900 mb-4 tracking-tight" @click.away="editing = false">
                        </div>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s3d.focus())" class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor3_desc.value"></p>
                            <textarea x-ref="s3d" x-show="editing" x-cloak x-model="data.sensor3_desc.value" class="input-box text-gray-600 text-sm leading-relaxed" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                        </div>
                    </div>

                    <!-- Temperature -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19c-1.657 0-3-1.343-3-3V6a3 3 0 116 0v10c0 1.657-1.343 3-3 3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h4m-4 4h4"></path></svg>
                        </div>
                        <div x-data="{ editing: false }">
                            <h3 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s4t.focus())" class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor4_title.value"></h3>
                            <input x-ref="s4t" x-show="editing" x-cloak x-model="data.sensor4_title.value" class="input-box text-xl font-bold text-gray-900 mb-4 tracking-tight" @click.away="editing = false">
                        </div>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s4d.focus())" class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor4_desc.value"></p>
                            <textarea x-ref="s4d" x-show="editing" x-cloak x-model="data.sensor4_desc.value" class="input-box text-gray-600 text-sm leading-relaxed" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                        </div>
                    </div>

                    <!-- Humidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a7 7 0 007-7c0-3.866-7-11-7-11s-7 7.134-7 11a7 7 0 007 7z"></path></svg>
                        </div>
                        <div x-data="{ editing: false }">
                            <h3 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s5t.focus())" class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor5_title.value"></h3>
                            <input x-ref="s5t" x-show="editing" x-cloak x-model="data.sensor5_title.value" class="input-box text-xl font-bold text-gray-900 mb-4 tracking-tight" @click.away="editing = false">
                        </div>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.s5d.focus())" class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor5_desc.value"></p>
                            <textarea x-ref="s5d" x-show="editing" x-cloak x-model="data.sensor5_desc.value" class="input-box text-gray-600 text-sm leading-relaxed" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <div class="mb-4" x-data="{ editing: false }">
                        <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.servTitle.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight" x-text="data.services_title.value"></h2>
                        <textarea x-ref="servTitle" x-show="editing" x-cloak x-model="data.services_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>
                    <div x-data="{ editing: false }">
                        <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.servSub.focus())" class="editable-hover text-gray-500 text-lg" x-text="data.services_subtitle.value"></p>
                        <textarea x-ref="servSub" x-show="editing" x-cloak x-model="data.services_subtitle.value" rows="1" class="input-box text-gray-500 text-lg text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>
                </div>

                <div class="max-w-4xl mx-auto space-y-12">
                    <!-- Service 1 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">01</div>
                        <div class="flex-1">
                            <div x-data="{ editing: false }">
                                <h4 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sv1t.focus())" class="editable-hover text-2xl font-bold text-gray-900 mb-3 tracking-tight" x-text="data.service1_title.value"></h4>
                                <input x-ref="sv1t" x-show="editing" x-cloak x-model="data.service1_title.value" class="input-box text-2xl font-bold text-gray-900 mb-3 tracking-tight" @click.away="editing = false">
                            </div>
                            <div x-data="{ editing: false }">
                                <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sv1d.focus())" class="editable-hover text-gray-600 leading-relaxed text-lg" x-text="data.service1_desc.value"></p>
                                <textarea x-ref="sv1d" x-show="editing" x-cloak x-model="data.service1_desc.value" class="input-box text-gray-600 leading-relaxed text-lg" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Service 2 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">02</div>
                        <div class="flex-1">
                            <div x-data="{ editing: false }">
                                <h4 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sv2t.focus())" class="editable-hover text-2xl font-bold text-gray-900 mb-3 tracking-tight" x-text="data.service2_title.value"></h4>
                                <input x-ref="sv2t" x-show="editing" x-cloak x-model="data.service2_title.value" class="input-box text-2xl font-bold text-gray-900 mb-3 tracking-tight" @click.away="editing = false">
                            </div>
                            <div x-data="{ editing: false }">
                                <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sv2d.focus())" class="editable-hover text-gray-600 leading-relaxed text-lg" x-text="data.service2_desc.value"></p>
                                <textarea x-ref="sv2d" x-show="editing" x-cloak x-model="data.service2_desc.value" class="input-box text-gray-600 leading-relaxed text-lg" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                            </div>
                        </div>
                    </div>
                    <!-- Service 3 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">03</div>
                        <div class="flex-1">
                            <div x-data="{ editing: false }">
                                <h4 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sv3t.focus())" class="editable-hover text-2xl font-bold text-gray-900 mb-3 tracking-tight" x-text="data.service3_title.value"></h4>
                                <input x-ref="sv3t" x-show="editing" x-cloak x-model="data.service3_title.value" class="input-box text-2xl font-bold text-gray-900 mb-3 tracking-tight" @click.away="editing = false">
                            </div>
                            <div x-data="{ editing: false }">
                                <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sv3d.focus())" class="editable-hover text-gray-600 leading-relaxed text-lg" x-text="data.service3_desc.value"></p>
                                <textarea x-ref="sv3d" x-show="editing" x-cloak x-model="data.service3_desc.value" class="input-box text-gray-600 leading-relaxed text-lg" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-24 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <div class="mb-4" x-data="{ editing: false }">
                        <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.contTitle.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight" x-text="data.contact_title.value"></h2>
                        <textarea x-ref="contTitle" x-show="editing" x-cloak x-model="data.contact_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>
                    <div x-data="{ editing: false }">
                        <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.contSub.focus())" class="editable-hover text-gray-500 text-lg" x-text="data.contact_subtitle.value"></p>
                        <textarea x-ref="contSub" x-show="editing" x-cloak x-model="data.contact_subtitle.value" rows="1" class="input-box text-gray-500 text-lg text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })" @click.away="editing = false"></textarea>
                    </div>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <!-- Email -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Email Address</h4>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.cemail.focus())" class="editable-hover text-blue-600 font-medium" x-text="data.contact_email.value"></p>
                            <input x-ref="cemail" x-show="editing" x-cloak x-model="data.contact_email.value" class="input-box text-blue-600 font-medium text-center" @click.away="editing = false">
                        </div>
                    </div>
                    <!-- Phone -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Mobile Number</h4>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.cphone.focus())" class="editable-hover text-blue-600 font-medium" x-text="data.contact_phone.value"></p>
                            <textarea x-ref="cphone" x-show="editing" x-cloak x-model="data.contact_phone.value" class="input-box text-blue-600 font-medium text-center" rows="2" @click.away="editing = false"></textarea>
                        </div>
                    </div>
                    <!-- Location -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 class="text-xl font-bold text-gray-900 mb-2">Our Location</h4>
                        <div x-data="{ editing: false }">
                            <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.cloc.focus())" class="editable-hover text-blue-600 font-medium" x-text="data.contact_location.value"></p>
                            <input x-ref="cloc" x-show="editing" x-cloak x-model="data.contact_location.value" class="input-box text-blue-600 font-medium text-center" @click.away="editing = false">
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-white py-12 border-t border-gray-100 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center">
                    <div class="flex justify-center items-center gap-3 mb-6">
                        <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="h-8 w-auto grayscale opacity-50" />
                        <div x-data="{ editing: false }">
                            <span x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.footerBrand.focus())" class="editable-hover text-gray-400 font-bold tracking-tight text-xl uppercase" x-text="data.footer_brand.value"></span>
                            <input x-ref="footerBrand" x-show="editing" x-cloak x-model="data.footer_brand.value" class="input-box text-gray-400 font-bold tracking-tight text-xl uppercase text-center w-48" @click.away="editing = false">
                        </div>
                    </div>
                    <div x-data="{ editing: false }" class="mb-4">
                        <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.footerCopy.focus())" class="editable-hover text-gray-500 text-sm" x-text="data.footer_copyright.value"></p>
                        <input x-ref="footerCopy" x-show="editing" x-cloak x-model="data.footer_copyright.value" class="input-box text-gray-500 text-sm text-center" @click.away="editing = false">
                    </div>
                    <div x-data="{ editing: false }">
                        <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.footerDevs.focus())" class="editable-hover text-sm font-medium text-gray-500 mt-2"
                            x-text="data.footer_devs.value"></p>
                        <textarea x-ref="footerDevs" x-show="editing" x-cloak x-model="data.footer_devs.value" rows="1"
                            class="input-box text-sm font-medium text-gray-500 text-center"
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            x-init="$watch('editing', v => { if(v) { $nextTick(() => { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' }) } })"
                            @click.away="editing = false"></textarea>
                    </div>
                </div>
            </div>
        </footer>

        <!-- ================================ -->
        <!-- Background Upload Modal          -->
        <!-- ================================ -->
        <div x-show="showBgModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.outside="showBgModal = false">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Change Background</h3>
                <p class="text-gray-500 text-sm mb-5">Choose how you'd like to upload a new hero background image.</p>

                <div class="space-y-3">
                    <button @click="$refs.bgFileInput.click(); showBgModal = false" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition text-left">
                        <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center text-blue-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">Upload Local File</div>
                            <div class="text-gray-400 text-xs">Choose an image from your device</div>
                        </div>
                    </button>
                    <button @click="showBgModal = false; showUrlModal = true" class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition text-left">
                        <div class="w-10 h-10 bg-purple-100 rounded-lg flex items-center justify-center text-purple-600 shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">Upload URL or Link</div>
                            <div class="text-gray-400 text-xs">Paste an image URL from the web</div>
                        </div>
                    </button>
                </div>

                <button @click="showBgModal = false" class="mt-4 w-full text-center text-gray-400 hover:text-gray-600 text-sm font-medium transition">Cancel</button>
            </div>
        </div>
        <input type="file" x-ref="bgFileInput" class="hidden" accept="image/*" @change="handleFileSelect">

        <!-- URL Input Modal -->
        <div x-show="showUrlModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-md mx-4" @click.outside="showUrlModal = false; tempUrl = ''">
                <h3 class="text-lg font-bold text-gray-900 mb-4">Paste Image URL</h3>
                <input type="text" x-model="tempUrl" placeholder="https://example.com/image.jpg" class="w-full border-gray-300 rounded-lg shadow-sm focus:border-blue-500 focus:ring-blue-500 mb-4">
                <div class="flex justify-end gap-3">
                    <button type="button" @click.prevent.stop="showUrlModal = false; tempUrl = ''" class="px-4 py-2 rounded-lg text-gray-600 hover:text-gray-800 hover:bg-gray-100 font-medium transition">Cancel</button>
                    <button type="button" @click="applyUrl" class="bg-blue-600 text-white px-6 py-2 rounded-lg font-bold hover:bg-blue-700 transition">Apply</button>
                </div>
            </div>
        </div>

    </div>

    <script>
        function landingEditor(initialData) {
            const defaults = {
                hero_title: { value: "IoT-Based Water Quality <br> <span class='gradient-text'>Monitoring System</span>" },
                hero_subtitle: { value: "Ensuring a sustainable aquaculture environment through high-precision IoT sensors and real-time data analytics." },
                hero_bg: { value: null },

                mission_badge: { value: "OUR MISSION" },
                mission_title: { value: "The Future of Aquaculture Management" },
                mission_text: { value: "Our system is designed to provide farmers with a robust, reliable, and user-friendly platform for monitoring vital aquatic conditions. By leveraging the power of IoT, we help eliminate the guesswork, reduce risks, and maximize productivity in aquaculture operations." },

                sensors_title: { value: "Integrated Sensor Technology" },
                sensors_subtitle: { value: "Our system utilizes five high-precision sensors to capture every critical metric." },

                sensor1_title: { value: "pH Sensor" },
                sensor1_desc: { value: "Measures the acidity or alkalinity of the water to ensure a healthy environment for aquatic life." },
                sensor2_title: { value: "Turbidity" },
                sensor2_desc: { value: "Detects water clarity by measuring suspended particles, crucial for accurate quality assessment." },
                sensor3_title: { value: "TDS Sensor" },
                sensor3_desc: { value: "Monitors the concentration of dissolved substances, indicating the overall purity of the water." },
                sensor4_title: { value: "Temperature" },
                sensor4_desc: { value: "Tracks water temperature to prevent thermal stress and maintain optimal growth rates for fish." },
                sensor5_title: { value: "Humidity" },
                sensor5_desc: { value: "Monitors air moisture levels around the pond, affecting evaporation and equipment safety." },

                services_title: { value: "Our Services" },
                services_subtitle: { value: "We provide end-to-end solutions for aquaculture technology integration." },
                service1_title: { value: "Automated Data Collection" },
                service1_desc: { value: "Continuous background data harvesting from a pond, simultaneously without manual intervention." },
                service2_title: { value: "Smart Alert Notifications" },
                service2_desc: { value: "Instant Alert notifications when water parameters exceed safe threshold limits for your specific fish species." },
                service3_title: { value: "AI Condition Analysis" },
                service3_desc: { value: "Advanced algorithms that analyze patterns to predict water quality health and recommend corrective actions." },

                contact_title: { value: "Contact Us" },
                contact_subtitle: { value: "Have questions? We're here to help you optimize your aquaculture operations." },
                contact_email: { value: "kirstinesanchez9@gmail.com" },
                contact_phone: { value: "09207327946\n09151003714" },
                contact_location: { value: "Po-Ok, Hinoba-an, Negros Occidental" },

                footer_brand: { value: "AQUASENSE" },
                footer_copyright: { value: "\u00a9 2026 AquaSense. All rights reserved." },
                footer_devs: { value: "Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra" }
            };

            let mergedData = { ...defaults };
            if (Array.isArray(initialData)) initialData = {};
            for (const key in defaults) {
                if (initialData[key]) mergedData[key] = initialData[key];
            }
            if (!mergedData.hero_bg) mergedData.hero_bg = { value: null };
            if (!mergedData.hero_bg.value) mergedData.hero_bg.value = null;

            return {
                data: mergedData,
                heroBgPreview: null,
                heroBgFile: null,
                showBgModal: false,
                showUrlModal: false,
                tempUrl: '',
                saving: false,
                showSuccess: false,

                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (file) {
                        this.heroBgFile = file;
                        const reader = new FileReader();
                        reader.onload = (e) => { this.heroBgPreview = e.target.result; };
                        reader.readAsDataURL(file);
                    }
                },

                applyUrl() {
                    if (this.tempUrl) {
                        if (!this.data.hero_bg) this.data.hero_bg = {};
                        this.data.hero_bg.value = this.tempUrl;
                        this.heroBgPreview = this.tempUrl;
                        this.heroBgFile = null;
                    }
                    this.showUrlModal = false;
                },

                saveChanges() {
                    this.saving = true;
                    const formData = new FormData();

                    for (const key in this.data) {
                        if (key !== 'hero_bg') {
                            formData.append(key, this.data[key].value);
                        }
                    }

                    if (this.heroBgFile) {
                        formData.append('hero_bg_file', this.heroBgFile);
                    } else if (this.tempUrl) {
                        formData.append('hero_bg_url', this.tempUrl);
                    }

                    formData.append('_method', 'PUT');

                    fetch('{{ route('admin.landing.update') }}', {
                        method: 'POST',
                        headers: { 'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content },
                        body: formData
                    })
                    .then(res => {
                        if (res.ok) {
                            this.showSuccess = true;
                            setTimeout(() => this.showSuccess = false, 2000);
                        } else { alert('Failed to save changes.'); }
                    })
                    .catch(err => { console.error(err); alert('Error saving changes.'); })
                    .finally(() => { this.saving = false; });
                }
            }
        }
    </script>
</x-app-layout>
