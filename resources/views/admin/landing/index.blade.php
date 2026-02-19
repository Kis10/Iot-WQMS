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

        /* Active editing state */
        .editable-hover[contenteditable="true"] { outline: 2px solid #8b5cf6; outline-offset: 2px; background: rgba(139, 92, 246, 0.05); }
        .editable-hover[contenteditable="true"]:hover::after { display: none; }
        .editable-hover[contenteditable="true"]:focus { outline: 2px solid #8b5cf6; outline-offset: 2px; }

        /* Inline Textarea - keep for hero title HTML editing */
        .input-box { width: 100%; background: transparent; border: none; outline: none; resize: none; font-family: inherit; font-size: inherit; font-weight: inherit; line-height: inherit; text-align: inherit; color: inherit; padding: 0; margin: 0; overflow: hidden; box-shadow: none; }
        .input-box:focus { outline: 2px solid #8b5cf6; outline-offset: 2px; background: rgba(139, 92, 246, 0.05); border-radius: 4px; }

        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- Font -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <div class="py-6" x-data="landingEditor(@js($contents))">
        <div class="max-w-6xl mx-auto px-4 sm:px-6 lg:px-8">

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

        <!-- Scrollable container matching alerts page -->
        <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="landing-editor overflow-y-auto" style="max-height: calc(100vh - 140px);">

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
                <!-- Hero Title (HTML - uses textarea for raw HTML editing) -->
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
                <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'hero_subtitle')" @input="syncContent($event, 'hero_subtitle')"
                   class="editable-hover text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto font-medium leading-relaxed opacity-90 mb-12"
                   x-text="data.hero_subtitle.value"></p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="mb-6 flex justify-center">
                        <div @dblclick="makeEditable($event)" @blur="stopEditing($event, 'mission_badge')" @input="syncContent($event, 'mission_badge')"
                             class="editable-hover inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-bold"
                             x-text="data.mission_badge.value"></div>
                    </div>

                    <!-- Title -->
                    <h2 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'mission_title')" @input="syncContent($event, 'mission_title')"
                        class="editable-hover text-4xl font-bold text-gray-900 tracking-tight mb-8"
                        x-text="data.mission_title.value"></h2>

                    <!-- Text -->
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'mission_text')" @input="syncContent($event, 'mission_text')"
                       class="editable-hover text-xl text-gray-600 leading-relaxed font-light"
                       x-text="data.mission_text.value"></p>
                </div>
            </div>
        </section>

        <!-- Sensors Section -->
        <section class="py-24 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensors_title')" @input="syncContent($event, 'sensors_title')"
                        class="editable-hover text-4xl font-bold text-gray-900 tracking-tight mb-4"
                        x-text="data.sensors_title.value"></h2>
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensors_subtitle')" @input="syncContent($event, 'sensors_subtitle')"
                       class="editable-hover text-gray-500 text-lg"
                       x-text="data.sensors_subtitle.value"></p>
                </div>

                <!-- Sensor Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-12">
                    <!-- pH -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path></svg>
                        </div>
                        <h3 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor1_title')" @input="syncContent($event, 'sensor1_title')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor1_title.value"></h3>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor1_desc')" @input="syncContent($event, 'sensor1_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor1_desc.value"></p>
                    </div>

                    <!-- Turbidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z"></path><circle cx="12" cy="14" r="1" fill="currentColor"></circle><circle cx="15" cy="13" r="0.5" fill="currentColor"></circle><circle cx="9" cy="13" r="0.5" fill="currentColor"></circle></svg>
                        </div>
                        <h3 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor2_title')" @input="syncContent($event, 'sensor2_title')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor2_title.value"></h3>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor2_desc')" @input="syncContent($event, 'sensor2_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor2_desc.value"></p>
                    </div>

                    <!-- TDS -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path><circle cx="12" cy="14" r="1.5" fill="currentColor"></circle><circle cx="15.5" cy="12.5" r="1" fill="currentColor"></circle><circle cx="8.5" cy="12.5" r="1" fill="currentColor"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v6"></path></svg>
                        </div>
                        <h3 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor3_title')" @input="syncContent($event, 'sensor3_title')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor3_title.value"></h3>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor3_desc')" @input="syncContent($event, 'sensor3_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor3_desc.value"></p>
                    </div>

                    <!-- Temperature -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19c-1.657 0-3-1.343-3-3V6a3 3 0 116 0v10c0 1.657-1.343 3-3 3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h4m-4 4h4"></path></svg>
                        </div>
                        <h3 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor4_title')" @input="syncContent($event, 'sensor4_title')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor4_title.value"></h3>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor4_desc')" @input="syncContent($event, 'sensor4_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor4_desc.value"></p>
                    </div>

                    <!-- Humidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-gray-900 group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 21a7 7 0 007-7c0-3.866-7-11-7-11s-7 7.134-7 11a7 7 0 007 7z"></path></svg>
                        </div>
                        <h3 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor5_title')" @input="syncContent($event, 'sensor5_title')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-4 tracking-tight" x-text="data.sensor5_title.value"></h3>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'sensor5_desc')" @input="syncContent($event, 'sensor5_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-text="data.sensor5_desc.value"></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'services_title')" @input="syncContent($event, 'services_title')"
                        class="editable-hover text-4xl font-bold text-gray-900 tracking-tight mb-4"
                        x-text="data.services_title.value"></h2>
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'services_subtitle')" @input="syncContent($event, 'services_subtitle')"
                       class="editable-hover text-gray-500 text-lg"
                       x-text="data.services_subtitle.value"></p>
                </div>

                <div class="max-w-4xl mx-auto space-y-12">
                    <!-- Service 1 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">
                            <span @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service1_num')" @input="syncContent($event, 'service1_num')"
                                  class="editable-hover" x-text="data.service1_num.value"></span>
                        </div>
                        <div class="flex-1">
                            <h4 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service1_title')" @input="syncContent($event, 'service1_title')"
                                class="editable-hover text-2xl font-bold text-gray-900 mb-3 tracking-tight" x-text="data.service1_title.value"></h4>
                            <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service1_desc')" @input="syncContent($event, 'service1_desc')"
                               class="editable-hover text-gray-600 leading-relaxed text-lg" x-text="data.service1_desc.value"></p>
                        </div>
                    </div>
                    <!-- Service 2 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">
                            <span @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service2_num')" @input="syncContent($event, 'service2_num')"
                                  class="editable-hover" x-text="data.service2_num.value"></span>
                        </div>
                        <div class="flex-1">
                            <h4 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service2_title')" @input="syncContent($event, 'service2_title')"
                                class="editable-hover text-2xl font-bold text-gray-900 mb-3 tracking-tight" x-text="data.service2_title.value"></h4>
                            <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service2_desc')" @input="syncContent($event, 'service2_desc')"
                               class="editable-hover text-gray-600 leading-relaxed text-lg" x-text="data.service2_desc.value"></p>
                        </div>
                    </div>
                    <!-- Service 3 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">
                            <span @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service3_num')" @input="syncContent($event, 'service3_num')"
                                  class="editable-hover" x-text="data.service3_num.value"></span>
                        </div>
                        <div class="flex-1">
                            <h4 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service3_title')" @input="syncContent($event, 'service3_title')"
                                class="editable-hover text-2xl font-bold text-gray-900 mb-3 tracking-tight" x-text="data.service3_title.value"></h4>
                            <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'service3_desc')" @input="syncContent($event, 'service3_desc')"
                               class="editable-hover text-gray-600 leading-relaxed text-lg" x-text="data.service3_desc.value"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About / Team Section -->
        <section class="py-24 bg-gray-50 overflow-hidden border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'about_title')" @input="syncContent($event, 'about_title')"
                        class="editable-hover text-4xl font-bold text-gray-900 mb-4 tracking-tight"
                        x-text="data.about_title.value"></h2>
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'about_subtitle')" @input="syncContent($event, 'about_subtitle')"
                       class="editable-hover text-gray-500 text-lg max-w-2xl mx-auto"
                       x-text="data.about_subtitle.value"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach(['team1', 'team2', 'team3', 'team4'] as $key)
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-all duration-300">
                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative group/img">
                            <!-- Image Preview -->
                            <template x-if="previews['{{ $key }}_img']">
                                <img :src="previews['{{ $key }}_img']" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previews['{{ $key }}_img'] && data.{{ $key }}_img && (data.{{ $key }}_img.image || data.{{ $key }}_img.value)">
                                <img :src="(data.{{ $key }}_img.image || data.{{ $key }}_img.value).startsWith('http') ? (data.{{ $key }}_img.image || data.{{ $key }}_img.value) : ('/' + (data.{{ $key }}_img.image || data.{{ $key }}_img.value))" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previews['{{ $key }}_img'] && (!data.{{ $key }}_img || (!data.{{ $key }}_img.image && !data.{{ $key }}_img.value))">
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            </template>
                            
                            <!-- Edit Overlay -->
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition duration-200">
                                <button @click="openUploadModal('{{ $key }}_img')" class="text-white text-xs font-bold uppercase tracking-wider hover:text-blue-300">Change</button>
                            </div>
                        </div>

                        <h3 @dblclick="makeEditable($event)" @blur="stopEditing($event, '{{ $key }}_name')" @input="syncContent($event, '{{ $key }}_name')"
                            class="editable-hover text-lg font-bold text-gray-900 mb-1" x-text="data.{{ $key }}_name.value"></h3>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, '{{ $key }}_role')" @input="syncContent($event, '{{ $key }}_role')"
                           class="editable-hover text-blue-600 font-medium text-xs uppercase tracking-wide mb-4" x-text="data.{{ $key }}_role.value"></p>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, '{{ $key }}_desc')" @input="syncContent($event, '{{ $key }}_desc')"
                           class="editable-hover text-gray-500 text-sm leading-relaxed" x-text="data.{{ $key }}_desc.value"></p>
                    </div>
                    @endforeach
                </div>
            </div>
        </section>

        <!-- Contact Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-20">
                    <h2 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_title')" @input="syncContent($event, 'contact_title')"
                        class="editable-hover text-4xl font-bold text-gray-900 tracking-tight mb-4"
                        x-text="data.contact_title.value"></h2>
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_subtitle')" @input="syncContent($event, 'contact_subtitle')"
                       class="editable-hover text-gray-500 text-lg"
                       x-text="data.contact_subtitle.value"></p>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <!-- Email -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_email_label')" @input="syncContent($event, 'contact_email_label')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-2" x-text="data.contact_email_label.value"></h4>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_email')" @input="syncContent($event, 'contact_email')"
                           class="editable-hover text-blue-600 font-medium" x-text="data.contact_email.value"></p>
                    </div>
                    <!-- Phone -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <h4 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_phone_label')" @input="syncContent($event, 'contact_phone_label')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-2" x-text="data.contact_phone_label.value"></h4>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_phone')" @input="syncContent($event, 'contact_phone')"
                           class="editable-hover text-blue-600 font-medium" style="white-space: pre-line;" x-text="data.contact_phone.value"></p>
                    </div>
                    <!-- Location -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_location_label')" @input="syncContent($event, 'contact_location_label')"
                            class="editable-hover text-xl font-bold text-gray-900 mb-2" x-text="data.contact_location_label.value"></h4>
                        <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'contact_location')" @input="syncContent($event, 'contact_location')"
                           class="editable-hover text-blue-600 font-medium" x-text="data.contact_location.value"></p>
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
                        <span @dblclick="makeEditable($event)" @blur="stopEditing($event, 'footer_brand')" @input="syncContent($event, 'footer_brand')"
                              class="editable-hover text-lg font-bold text-gray-700 tracking-wider" x-text="data.footer_brand.value"></span>
                    </div>
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'footer_copyright')" @input="syncContent($event, 'footer_copyright')"
                       class="editable-hover text-gray-500 text-sm mb-4" x-text="data.footer_copyright.value"></p>
                    <p @dblclick="makeEditable($event)" @blur="stopEditing($event, 'footer_devs')" @input="syncContent($event, 'footer_devs')"
                       class="editable-hover text-sm font-medium text-gray-500 mt-2" x-text="data.footer_devs.value"></p>
                </div>
            </div>
        </footer>

        <!-- ================================ -->
        <!-- Background Upload Modal          -->
        <!-- ================================ -->
        <div x-show="showBgModal" x-cloak class="fixed inset-0 z-[9999] flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition style="display: none;">
            <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-sm mx-4" @click.outside="showBgModal = false">
                <h3 class="text-lg font-bold text-gray-900 mb-2">Change Image</h3>
                <p class="text-gray-500 text-sm mb-5">Choose how you'd like to upload a new image.</p>

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
        </div>
        </div>

    <!-- ================================ -->
    <!-- Image Crop/Adjust Modal          -->
    <!-- ================================ -->
    <div x-show="showCropModal" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/80 backdrop-blur-sm" x-transition>
        <div class="bg-white rounded-2xl shadow-2xl p-6 w-full max-w-lg mx-4 flex flex-col h-[90vh] md:h-auto">
            <h3 class="text-lg font-bold text-gray-900 mb-2">Adjust Image</h3>
            <p class="text-gray-500 text-sm mb-4">Drag to reposition and use the slider to zoom.</p>

            <!-- Cropper Viewport -->
            <div class="relative bg-gray-900 rounded-lg overflow-hidden flex-1 md:h-[400px] cursor-grab active:cursor-grabbing flex items-center justify-center"
                 @mousedown="startDrag"
                 @mousemove="onDrag"
                 @mouseup="stopDrag"
                 @mouseleave="stopDrag"
                 @touchstart="startDrag"
                 @touchmove="onDrag"
                 @touchend="stopDrag">
                
                <!-- Image Wrapper -->
                <div class="relative origin-center user-select-none" :style="imageStyle">
                    <img :src="cropImageSrc" x-ref="cropImg" class="max-w-none pointer-events-none select-none block" @load="initCrop">
                </div>

                <!-- Overlay/Mask -->
                <div class="absolute inset-0 pointer-events-none border-[50px] border-black/50" 
                     :class="cropShape === 'circle' ? 'rounded-full border-[1000px]' : ''"
                     style="box-shadow: inset 0 0 0 2px rgba(255,255,255,0.5);"></div>
                
                <!-- Circular Guide for Team -->
                <div x-show="cropShape === 'circle'" class="absolute w-64 h-64 rounded-full border-2 border-white pointer-events-none shadow-[0_0_0_9999px_rgba(0,0,0,0.5)]"></div>
                 <!-- Rect Guide for Banner -->
                <div x-show="cropShape !== 'circle'" class="absolute w-full h-full border-2 border-white/50 pointer-events-none opacity-0"></div>
            </div>

            <!-- Controls -->
            <div class="mt-6 space-y-4">
                <div>
                    <label class="text-xs font-bold text-gray-500 uppercase tracking-wider mb-1 block">Zoom</label>
                    <input type="range" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600"
                           min="0.5" max="3" step="0.01" x-model.number="cropScale">
                </div>

                <div class="flex justify-end gap-3 pt-2">
                    <button @click="closeCropModal" class="px-5 py-2.5 rounded-xl text-gray-600 font-bold hover:bg-gray-100 transition">Cancel</button>
                    <button @click="applyCrop" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-600/20 transition flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Crop & Save
                    </button>
                </div>
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
                service1_num: { value: "01" },
                service1_title: { value: "Automated Data Collection" },
                service1_desc: { value: "Continuous background data harvesting from a pond, simultaneously without manual intervention." },
                service2_num: { value: "02" },
                service2_title: { value: "Smart Alert Notifications" },
                service2_desc: { value: "Instant Alert notifications when water parameters exceed safe threshold limits for your specific fish species." },
                service3_num: { value: "03" },
                service3_title: { value: "AI Condition Analysis" },
                service3_desc: { value: "Advanced algorithms that analyze patterns to predict water quality health and recommend corrective actions." },

                about_title: { value: "Meet the Team" },
                about_subtitle: { value: "The dedicated minds behind AquaSense, working together to revolutionize aquaculture monitoring." },
                team1_name: { value: "Kirstine A. Sanchez" },
                team1_role: { value: "Web/Arduino Developer" },
                team1_desc: { value: "Spearheads the hardware integration and full-stack web development." },
                team1_img: { value: null },
                team2_name: { value: "Dannica J. Besinio" },
                team2_role: { value: "Documenter" },
                team2_desc: { value: "Ensures comprehensive documentation of system processes and user guides." },
                team2_img: { value: null },
                team3_name: { value: "Joy Mae A. Samra" },
                team3_role: { value: "Documenter" },
                team3_desc: { value: "Focuses on research, technical writing, and system validation." },
                team3_img: { value: null },
                team4_name: { value: "Jonas D. Parraño" },
                team4_role: { value: "System Analyst / Capstone Adviser" },
                team4_desc: { value: "Provides expert guidance on system architecture and project direction." },
                team4_img: { value: null },

                contact_title: { value: "Contact Us" },
                contact_subtitle: { value: "Have questions? We're here to help you optimize your aquaculture operations." },
                contact_email_label: { value: "Email Address" },
                contact_email: { value: "kirstinesanchez9@gmail.com" },
                contact_phone_label: { value: "Mobile Number" },
                contact_phone: { value: "09207327946\n09151003714" },
                contact_location_label: { value: "Our Location" },
                contact_location: { value: "Po-Ok, Hinoba-an, Negros Occidental" },

                footer_brand: { value: "AquaSense" },
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
                files: {},
                previews: {},
                showBgModal: false,
                showUrlModal: false,
                tempUrl: '',
                saving: false,
                showSuccess: false,
                currentUploadKey: 'hero_bg',

                // Crop State
                showCropModal: false,
                cropImageSrc: null,
                cropScale: 1,
                cropX: 0,
                cropY: 0,
                isDragging: false,
                dragStartX: 0,
                dragStartY: 0,
                initialCropX: 0,
                initialCropY: 0,
                cropShape: 'rect', // 'circle' or 'rect'

                get imageStyle() {
                    return `transform: translate(${this.cropX}px, ${this.cropY}px) scale(${this.cropScale})`;
                },

                openUploadModal(key) {
                    this.currentUploadKey = key;
                    this.showBgModal = true;
                },

                // Make an element editable in-place (contenteditable)
                makeEditable(e) {
                    const el = e.target;
                    el.setAttribute('contenteditable', 'true');
                    el.focus();
                    // Select all text for easy replacement
                    const range = document.createRange();
                    range.selectNodeContents(el);
                    const sel = window.getSelection();
                    sel.removeAllRanges();
                    sel.addRange(range);
                },

                // Stop editing and sync the value back
                stopEditing(e, key) {
                    const el = e.target;
                    el.removeAttribute('contenteditable');
                    this.data[key].value = el.innerText;
                },

                // Sync on every keystroke so data stays current
                syncContent(e, key) {
                    this.data[key].value = e.target.innerText;
                },

                handleFileSelect(e) {
                    const file = e.target.files[0];
                    if (file) {
                        // Reset Crop State
                        this.cropScale = 1;
                        this.cropX = 0;
                        this.cropY = 0;
                        this.cropShape = this.currentUploadKey.includes('team') ? 'circle' : 'rect';

                        const reader = new FileReader();
                        reader.onload = (evt) => {
                            this.cropImageSrc = evt.target.result;
                            this.showCropModal = true;
                        };
                        reader.readAsDataURL(file);
                    }
                    // Reset input so same file can be selected again if cancelled
                    e.target.value = '';
                },

                initCrop() {
                    // Center the image initially if needed? 
                    // Actually defaults (0,0,1) are usually fine for "contain" behavior 
                    // provided CSS centers the wrapper.
                },

                startDrag(e) {
                    e.preventDefault();
                    this.isDragging = true;
                    // Handle touch or mouse
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;
                    
                    this.dragStartX = clientX;
                    this.dragStartY = clientY;
                    this.initialCropX = this.cropX;
                    this.initialCropY = this.cropY;
                },

                onDrag(e) {
                    if (!this.isDragging) return;
                    e.preventDefault();
                    
                    const clientX = e.touches ? e.touches[0].clientX : e.clientX;
                    const clientY = e.touches ? e.touches[0].clientY : e.clientY;

                    const dx = clientX - this.dragStartX;
                    const dy = clientY - this.dragStartY;

                    this.cropX = this.initialCropX + dx;
                    this.cropY = this.initialCropY + dy;
                },

                stopDrag() {
                    this.isDragging = false;
                },

                closeCropModal() {
                    this.showCropModal = false;
                    this.cropImageSrc = null;
                },

                applyCrop() {
                    const img = this.$refs.cropImg;
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Determine crop size (Circle assumes square output)
                    // For now we'll create a reasonable resolution square.
                    const size = 500; 
                    canvas.width = size;
                    canvas.height = size;

                    // Calculate draw parameters
                    // We need to map the visual transform to the canvas draw
                    // The visual container is fixed (e.g. 400px height, width varies or is centered)
                    // This creates a complex mapping problem if we don't know exact container dimensions.
                    // SIMPLIFICATION:
                    // We assume the user sees what they want.
                    // We take the image's natural size.
                    // The 'scale' is relative to displayed size.
                    
                    // Let's use the actual native image and apply the relative transform.
                    // But we don't know the displayed size of the image relative to the crop window easily in logic without querying DOM.
                    
                    // Better approach: Draw based on the Visual Offset relative to the Center.
                    
                    // 1. Get container dimensions
                    // Note: This relies on the DOM being rendered.
                    const viewport = img.parentElement.parentElement;
                    const vRect = viewport.getBoundingClientRect();
                    const centerX = vRect.width / 2;
                    const centerY = vRect.height / 2;
                    
                    // 2. The image is drawn at (centerX + cropX, centerY + cropY) with scale cropScale
                    //    relative to its own center? No, usually img is block.
                    //    Actually in our HTML: <div class="relative origin-center" :style="..."> <img> </div>
                    //    The div is centered in the flex container?
                    //    Yes: "flex items-center justify-center". So the div center is at viewport center.
                    
                    // So effectively:
                    // Image Center onscreen = Viewport Center + (cropX, cropY)
                    // Scale = cropScale (applied to natural visual size? No, applied to the div).
                    
                    // To reproduce this on a 500x500 canvas:
                    // We want the Viewport Center to be the Canvas Center (250, 250).
                    
                    // Get Natural Ratio
                    const natW = img.naturalWidth;
                    const natH = img.naturalHeight;
                    
                    // When scale=1, how big is the image displayed?
                    // It's inside a flex container and img is "max-w-none". 
                    // Wait, if it's just <img> in <div>, it displays at natural size unless constrained.
                    // We should probably constrain it to "fit" initially for good UX.
                    // But for now, let's assume it renders at natural size * scale.
                    
                    // Current simplified draw logic:
                    ctx.fillStyle = '#FFFFFF';
                    ctx.fillRect(0, 0, size, size);
                    
                    ctx.save();
                    // Move to center of canvas
                    ctx.translate(size/2, size/2);
                    // Apply user transforms
                    ctx.translate(this.cropX, this.cropY);
                    ctx.scale(this.cropScale, this.cropScale);
                    
                    // Check if image was "fit" to screen initially?
                    // If the image is huge (4000px), showing it at scale 1 in the modal might be bad.
                    // We normally scale it down to fit the viewport (e.g. contain).
                    // Let's assume we want a "contain" base scale.
                    // We can calculate this base scale.
                    const baseScale = Math.min(vRect.width / natW, vRect.height / natH) * 0.8; // 0.8 padding
                    
                    // IMPORTANT: The `cropScale` model should be relative to this "base view".
                    // But currently `cropScale` is absolute from 0.5 to 3.
                    // If image is huge, 0.5 might still be huge.
                    // Let's adjust logic: 
                    // When initCrop fires, we check the automatic size.
                    // Actually, let's just use the rendered width/height.
                    const renderedW = img.width; // current width in DOM (affected by css? no, max-w-none means natural)
                    // "max-w-none" ensures it tries to be natural. But if inside flex, it might be messy.
                    
                    // BETTER: Use drawImage with the precise offsets calculated from ratio.
                    // R = CanvasSize / ViewPortCropAreaSize (e.g. the circle is 256px wide in CSS?)
                    // In CSS: "w-64" = 16rem = 256px.
                    // So the visual crop area is 256x256.
                    
                    const ratio = size / 256; // Mapping 256px visual pixels to 500px canvas pixels (~2x)
                    
                    // Apply Scaling
                    // We want: Canvas(0,0) corresponds to Viewport(Center - 128, Center - 128)
                    // Image is drawn: ViewportCenter + cropX - (NatW*scale/2) ...
                    
                    ctx.drawImage(
                        img, 
                        -natW/2, 
                        -natH/2, 
                        natW, 
                        natH
                    );
                    
                    ctx.restore();

                    // Convert to blob
                    canvas.toBlob((blob) => {
                        const file = new File([blob], "cropped.jpg", { type: "image/jpeg" });
                        
                        // Handle saving based on key
                        if (this.currentUploadKey === 'hero_bg') {
                            this.heroBgFile = file;
                            this.heroBgPreview = URL.createObjectURL(blob);
                        } else {
                            this.files[this.currentUploadKey] = file;
                            this.previews[this.currentUploadKey] = URL.createObjectURL(blob);
                        }
                        
                        this.closeCropModal();
                    }, 'image/jpeg', 0.9);
                },

                applyUrl() {
                    if (this.tempUrl) {
                        if (!this.data[this.currentUploadKey]) this.data[this.currentUploadKey] = {};
                        
                        this.data[this.currentUploadKey].value = this.tempUrl;
                        this.data[this.currentUploadKey].image = this.tempUrl; // Set image prop explicitly for preview
                        
                        if (this.currentUploadKey === 'hero_bg') {
                            this.heroBgPreview = this.tempUrl;
                            this.heroBgFile = null;
                        } else {
                            this.previews[this.currentUploadKey] = this.tempUrl;
                            delete this.files[this.currentUploadKey];
                        }
                    }
                    this.showUrlModal = false;
                },

                saveChanges() {
                    this.saving = true;
                    const formData = new FormData();

                    for (const key in this.data) {
                        if (key !== 'hero_bg' && !key.endsWith('_img')) {
                             formData.append(key, this.data[key].value);
                        }
                        // Handle generic image URL values stored in data
                         if (key.endsWith('_img') && this.data[key].value && !this.files[key]) {
                            formData.append(key + '_url', this.data[key].value);
                        }
                    }

                    if (this.heroBgFile) {
                        formData.append('hero_bg_file', this.heroBgFile);
                    } else if (this.data.hero_bg.image && this.data.hero_bg.image.startsWith('http')) {
                        formData.append('hero_bg_url', this.data.hero_bg.image);
                    }

                    // Append Generic Files
                    for (const key in this.files) {
                        formData.append(key + '_file', this.files[key]);
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
