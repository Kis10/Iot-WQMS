<x-app-layout>
    <!-- Scoped Styles for Landing Editor independently of Admin Theme -->
    <style>
        .landing-wrapper { font-family: 'Outfit', sans-serif; }
        .landing-wrapper .glass { background: rgba(255, 255, 255, 0.7); backdrop-filter: blur(10px); -webkit-backdrop-filter: blur(10px); }
        .landing-wrapper .gradient-text { background: linear-gradient(135deg, #2563eb 0%, #0891b2 100%); -webkit-background-clip: text; -webkit-text-fill-color: transparent; }
        
        /* Editor Styles */
        .editable-hover:hover { outline: 2px dashed #3b82f6; cursor: text; position: relative; border-radius: 4px; z-index: 10; }
        .editable-hover::after { content: 'Double-click to edit'; position: absolute; top: -20px; left: 0; background: #3b82f6; color: white; font-size: 10px; padding: 2px 6px; border-radius: 2px; opacity: 0; transition: opacity 0.2s; pointer-events: none; white-space: nowrap; }
        .editable-hover:hover::after { opacity: 1; }
        
        /* Canva-style Inline Editor */
        .input-box { 
            width: 100%; 
            background: transparent; 
            border: none; 
            outline: none; 
            resize: none; 
            font-family: inherit; 
            font-size: inherit; 
            font-weight: inherit; 
            line-height: inherit;
            text-align: inherit;
            color: inherit;
            padding: 0;
            margin: 0;
            overflow: hidden; /* Hide scrollbar */
            box-shadow: none;
        }
        
        /* Canva Purple Outline on Focus */
        .input-box:focus {
            outline: 2px solid #8b5cf6; /* Violet-500 */
            outline-offset: 2px;
            background: rgba(139, 92, 246, 0.05); /* Very faint purple tint */
            border-radius: 4px;
        }
        
        /* Hide Scrollbar for clean look */
        .no-scrollbar::-webkit-scrollbar { display: none; }
        .no-scrollbar { -ms-overflow-style: none; scrollbar-width: none; }
    </style>

    <!-- Font for Preview -->
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <div class="py-12 h-[calc(100vh-65px)] overflow-hidden" x-data="landingEditor(@js($contents))">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 h-full flex flex-col">
            
            <!-- Header / Toolbar -->
            <!-- Header / Toolbar -->
            <div class="flex justify-end items-center mb-4">
                <div class="flex items-center gap-3">
                    <div x-show="showSuccess" x-transition class="flex items-center gap-2 text-green-600 font-bold text-sm bg-green-50 px-3 py-1.5 rounded-full border border-green-100">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"></path></svg>
                        Saved Successfully!
                    </div>
                    <button @click="saveChanges" :disabled="saving" class="bg-blue-600 hover:bg-blue-500 disabled:opacity-50 text-white px-6 py-2 rounded-lg shadow-md font-bold transition flex items-center gap-2 text-sm">
                        <svg x-show="saving" class="animate-spin h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        <span x-show="!saving && !showSuccess">Save Changes</span>
                        <span x-show="saving">Saving...</span>
                        <span x-show="showSuccess">Saved!</span>
                    </button>
                </div>
            </div>

            <!-- Preview Container (Browser Mockup) -->
            <div class="flex-1 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-200 flex flex-col relative landing-wrapper">
                 <!-- Mock Browser Bar -->
                 <div class="bg-gray-100 border-b border-gray-200 px-4 py-2 flex items-center gap-2 shrink-0">
                     <div class="flex gap-1.5">
                         <div class="w-3 h-3 rounded-full bg-red-400"></div>
                         <div class="w-3 h-3 rounded-full bg-yellow-400"></div>
                         <div class="w-3 h-3 rounded-full bg-green-400"></div>
                     </div>
                     <div class="flex-1 bg-white/50 rounded text-center text-xs text-gray-500 font-mono py-0.5 mx-4 truncate">
                         https://aquasense.blog
                     </div>
                     <div class="text-gray-400">
                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15" /></svg>
                     </div>
                 </div>

                 <!-- Scrollable Viewport -->
                 <div class="flex-1 overflow-y-auto scroll-smooth bg-gray-50 relative" id="preview-viewport">
                    
                    <!-- Landing Navbar (Sticky inside container) -->
                    <nav class="sticky top-0 w-full z-40 transition-all duration-300 glass border-b border-gray-100 py-4">
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
                    <section id="home" class="relative min-h-[85vh] flex items-center justify-center bg-slate-900 overflow-hidden pt-32 pb-20 group/hero">
                        
                        <!-- Background Image -->
                        <div class="absolute inset-0 z-0">
                            <!-- Preview (New Upload/URL) -->
                            <template x-if="heroBgPreview">
                                <img :src="heroBgPreview" class="w-full h-full object-cover opacity-40">
                            </template>
                            <!-- Saved Background (From DB or Default) -->
                            <template x-if="!heroBgPreview && data.hero_bg && data.hero_bg.value">
                                <img :src="data.hero_bg.value.startsWith('http') ? data.hero_bg.value : (
                                    data.hero_bg.value.startsWith('/') ? data.hero_bg.value : (
                                    data.hero_bg.value.startsWith('img/') ? '/' + data.hero_bg.value : 
                                    '/storage/' + data.hero_bg.value
                                ))" 
                                     class="w-full h-full object-cover opacity-40 ml-0 transition-opacity duration-300"
                                     onerror="this.style.display='none'">
                            </template>
                        </div>
                        <div class="absolute inset-0 bg-gradient-to-br from-blue-900/10 via-slate-900/40 to-slate-950/40 z-0"></div>

                        <!-- Background Edit Overlay (Hover) -->
                        <div class="absolute top-8 right-8 z-30 opacity-0 group-hover/hero:opacity-100 transition duration-300">
                            <div class="relative" x-data="{ open: false }">
                                <button @click="open = !open" class="bg-white/10 hover:bg-white/20 backdrop-blur text-white p-2 rounded-full border border-white/20 transition shadow-lg">
                                     <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.232 5.232l3.536 3.536m-2.036-5.036a2.5 2.5 0 113.536 3.536L6.5 21.036H3v-3.572L16.732 3.732z" /></svg>
                                </button>
                                <!-- Dropdown -->
                                <div x-show="open" @click.away="open = false" class="absolute right-0 mt-2 w-56 bg-white rounded-xl shadow-2xl overflow-hidden border border-gray-100 py-1 z-50">
                                    <div class="px-4 py-2 border-b border-gray-100 bg-gray-50 text-xs font-bold text-gray-500 uppercase tracking-wider">Change Background</div>
                                    <button @click="$refs.bgInput.click(); open = false" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12" /></svg>
                                         Open Local File
                                    </button>
                                    <button @click="showUrlModal = true; open = false" class="w-full text-left px-4 py-3 text-sm text-gray-700 hover:bg-blue-50 hover:text-blue-600 transition flex items-center gap-2">
                                         <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1" /></svg>
                                         Paste URL
                                    </button>
                                </div>
                            </div>
                            <input type="file" x-ref="bgInput" class="hidden" accept="image/*" @change="handleFileSelect">
                        </div>

                        <!-- Content -->
                        <div class="relative z-10 text-center px-4 max-w-5xl mx-auto">
                            <br><br>
                            <!-- Hero Title -->
                <div class="mb-8" x-data="{ editing: false }">
                    <h1 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.heroInput.focus())" class="editable-hover text-5xl md:text-7xl lg:text-8xl font-black text-white leading-tight tracking-tight cursor-text"
                        x-html="data.hero_title.value"></h1>
                    <div x-show="editing" x-cloak>
                        <textarea x-ref="heroInput" x-model="data.hero_title.value" 
                            class="input-box text-5xl md:text-7xl lg:text-8xl font-black text-white leading-tight tracking-tight text-center" 
                            @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                            x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })"
                            @click.away="editing = false"></textarea>
                        <p class="text-white/50 text-xs mt-1">Supports HTML</p>
                    </div>
                </div>

                <!-- Hero Subtitle -->
                <div class="mb-12" x-data="{ editing: false }">
                    <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.subInput.focus())" class="editable-hover text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto font-medium leading-relaxed opacity-90 cursor-text"
                       x-text="data.hero_subtitle.value"></p>
                    <textarea x-ref="subInput" x-show="editing" x-cloak x-model="data.hero_subtitle.value" 
                        class="input-box text-xl md:text-2xl text-blue-100 font-medium leading-relaxed text-center max-w-3xl mx-auto"
                        @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                        x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })"
                        @click.away="editing = false"></textarea>
                </div>            </div>
                        </div>
                    </section>
                    
                    <!-- Mission Section -->
                    <section class="py-24 bg-white overflow-hidden">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                            <div class="max-w-3xl mx-auto">
                                <!-- Badge -->
                            <div class="mb-6 flex justify-center" x-data="{ editing: false }">
                                <div x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.badgeInput.focus())" class="editable-hover inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-bold cursor-text"
                                     x-text="data.mission_badge.value"></div>
                                <input x-ref="badgeInput" x-show="editing" x-cloak x-model="data.mission_badge.value" class="input-box text-sm font-bold text-blue-600 bg-blue-50 rounded-full px-4 py-1.5 text-center w-auto inline-block" @click.away="editing = false">
                            </div>
                            
                            <!-- Title -->
                            <div class="mb-8" x-data="{ editing: false }">
                                <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.misTitleInput.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight cursor-text"
                                    x-text="data.mission_title.value"></h2>
                                <textarea x-ref="misTitleInput" x-show="editing" x-cloak x-model="data.mission_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                            </div>

                            <!-- Text -->
                             <div x-data="{ editing: false }">
                                <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.misTextInput.focus())" class="editable-hover text-xl text-gray-600 leading-relaxed font-light cursor-text"
                                   x-text="data.mission_text.value"></p>
                                <textarea x-ref="misTextInput" x-show="editing" x-cloak x-model="data.mission_text.value" 
                                    class="input-box text-xl text-gray-600 leading-relaxed font-light text-center" 
                                    @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                    x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })"
                                    @click.away="editing = false"></textarea>
                            </div>
                            </div>
                        </div>
                    </section>

                    <!-- Sensors Section (Just Header) -->
                    <section class="py-24 bg-gray-50 overflow-hidden">
                         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-16">
                                 <div class="mb-4" x-data="{ editing: false }">
                                    <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sensTitleInput.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight cursor-text" x-text="data.sensors_title.value"></h2>
                                    <textarea x-ref="sensTitleInput" x-show="editing" x-cloak x-model="data.sensors_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                                </div>
                                 <div x-data="{ editing: false }">
                                    <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.sensSubInput.focus())" class="editable-hover text-gray-500 text-lg cursor-text" x-text="data.sensors_subtitle.value"></p>
                                    <textarea x-ref="sensSubInput" x-show="editing" x-cloak x-model="data.sensors_subtitle.value" rows="1" class="input-box text-gray-500 text-lg text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                                </div>
                            </div>
                             <!-- Sensors Grid (Static) -->
                             <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-12 mt-12 opacity-50 grayscale pointer-events-none">
                                 <!-- Sensor 1: pH -->
                                 <div class="sensor-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                     <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-4">
                                         <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path></svg>
                                     </div>
                                     <h3 class="font-bold text-gray-900">pH Sensor</h3>
                                 </div>
                                 <div class="sensor-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                     <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-4">
                                         <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                     </div>
                                     <h3 class="font-bold text-gray-900">Turbidity</h3>
                                 </div>
                                 <div class="sensor-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                     <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-4">
                                         <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                     </div>
                                     <h3 class="font-bold text-gray-900">Temp</h3>
                                 </div>
                                 <div class="sensor-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                     <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-4">
                                         <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                     </div>
                                     <h3 class="font-bold text-gray-900">DO</h3>
                                 </div>
                                 <div class="sensor-card bg-white p-6 rounded-2xl border border-gray-100 shadow-sm">
                                     <div class="w-12 h-12 bg-gray-100 rounded-xl flex items-center justify-center mb-4">
                                         <svg class="w-6 h-6 text-gray-900" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 15a4 4 0 004 4h9a5 5 0 10-.1-9.999 5.002 5.002 0 10-9.78 2.096A4.001 4.001 0 003 15z"></path></svg>
                                     </div>
                                     <h3 class="font-bold text-gray-900">Humidity</h3>
                                 </div>
                             </div>
                        </div>
                    </section>

                    <!-- Services Section -->
                    <section class="py-24 bg-white overflow-hidden">
                         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-16">
                                <div class="mb-4" x-data="{ editing: false }">
                                    <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.servTitleInput.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight cursor-text" x-text="data.services_title.value"></h2>
                                    <textarea x-ref="servTitleInput" x-show="editing" x-cloak x-model="data.services_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                                </div>
                                 <div x-data="{ editing: false }">
                                    <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.servSubInput.focus())" class="editable-hover text-gray-500 text-lg cursor-text" x-text="data.services_subtitle.value"></p>
                                    <textarea x-ref="servSubInput" x-show="editing" x-cloak x-model="data.services_subtitle.value" rows="1" class="input-box text-gray-500 text-lg text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                     <!-- Contact Section -->
                    <section class="py-24 bg-gray-50 overflow-hidden">
                         <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center mb-20">
                                <div class="mb-4" x-data="{ editing: false }">
                                    <h2 x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.contTitleInput.focus())" class="editable-hover text-4xl font-bold text-gray-900 tracking-tight cursor-text" x-text="data.contact_title.value"></h2>
                                    <textarea x-ref="contTitleInput" x-show="editing" x-cloak x-model="data.contact_title.value" rows="1" class="input-box text-4xl font-bold text-gray-900 tracking-tight text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                                </div>
                                 <div x-data="{ editing: false }">
                                    <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.contSubInput.focus())" class="editable-hover text-gray-500 text-lg cursor-text" x-text="data.contact_subtitle.value"></p>
                                    <textarea x-ref="contSubInput" x-show="editing" x-cloak x-model="data.contact_subtitle.value" rows="1" class="input-box text-gray-500 text-lg text-center" @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'" x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })" @click.away="editing = false"></textarea>
                                </div>
                            </div>
                        </div>
                    </section>

                    <!-- Improved Footer -->
                    <footer class="bg-white py-12 border-t border-gray-100 overflow-hidden">
                        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                            <div class="text-center">
                                <div class="flex justify-center items-center gap-3 mb-6">
                                    <img src="{{ asset('img/logo/logo-wq.png') }}" alt="Logo" class="h-8 w-auto grayscale opacity-50" />
                                    <span class="text-gray-400 font-bold tracking-tight text-xl uppercase">{{ config('app.name', 'AquaSense') }}</span>
                                </div>
                                <p class="text-gray-500 text-sm mb-4">&copy; {{ date('Y') }} {{ config('app.name', 'AquaSense') }}. All rights reserved.</p>
                                <div x-data="{ editing: false }">
                                    <p x-show="!editing" @dblclick="editing = true; $nextTick(() => $refs.footerInput.focus())" class="editable-hover text-sm font-medium text-gray-500 mt-2 cursor-text"
                                        x-text="data.footer_devs.value"></p>
                                    <textarea x-ref="footerInput" x-show="editing" x-cloak x-model="data.footer_devs.value" rows="1"
                                        class="input-box text-sm font-medium text-gray-500 text-center" 
                                        @input="$el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px'"
                                        x-init="$watch('editing', value => { if(value) { $el.style.height = 'auto'; $el.style.height = $el.scrollHeight + 'px' } })"
                                        @click.away="editing = false"></textarea>
                                </div>
                            </div>
                        </div>
                    </footer>

                 </div> <!-- End Scrollable Viewport -->
            </div>
        </div>

        <!-- URL Modal -->
        <div x-show="showUrlModal" x-cloak class="fixed inset-0 z-50 flex items-center justify-center bg-black/50 backdrop-blur-sm" x-transition style="display: none;">
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
            // Default content matching the Landing Page Seeder/Welcome View
            const defaults = {
                hero_title: { value: "IoT-Based Water Quality <br> <span class='gradient-text'>Monitoring System</span>" },
                hero_subtitle: { value: "Ensuring a sustainable aquaculture environment through high-precision IoT sensors and real-time data analytics." },
                hero_bg: { value: null }, // Default local image
                
                mission_badge: { value: "OUR MISSION" },
                mission_title: { value: "The Future of Aquaculture Management" },
                mission_text: { value: "Our system is designed to provide farmers with a robust, reliable, and user-friendly platform for monitoring vital aquatic conditions. By leveraging the power of IoT, we help eliminate the guesswork, reduce risks, and maximize productivity in aquaculture operations." },
                
                sensors_title: { value: "Integrated Sensor Technology" },
                sensors_subtitle: { value: "Our system utilizes five high-precision sensors to capture every critical metric." },
                
                services_title: { value: "Our Services" },
                services_subtitle: { value: "We provide end-to-end solutions for aquaculture technology integration." },
                
                contact_title: { value: "Contact Us" },
                contact_subtitle: { value: "Have questions? We're here to help you optimize your aquaculture operations." },
                
                footer_devs: { value: "Developed by: Kirstine A. Sanchez, Dannica J. Besinio and Joy Mae A. Samra" }
            };

            // Merge defaults with initialData (DB data overrides defaults)
            // We use a deep merge approach for specific keys to ensure structure exists
            let mergedData = { ...defaults };
            
            // If initialData is array (empty DB), it might come as [], we need object
            if (Array.isArray(initialData)) {
                initialData = {};
            }

            for (const key in defaults) {
                if (initialData[key]) {
                    mergedData[key] = initialData[key];
                }
            }
            
            // Ensure hero_bg exists specifically because we access .value
            if (!mergedData.hero_bg) mergedData.hero_bg = { value: null };
            if (!mergedData.hero_bg.value) mergedData.hero_bg.value = null;

            return {
                data: mergedData,
                heroBgPreview: null,
                heroBgFile: null,
                showUrlModal: false,
                tempUrl: '',
                saving: false,
                showSuccess: false,

                handleFileSelect(e) {
                        const file = e.target.files[0];
                        if (file) {
                            this.heroBgFile = file;
                            const reader = new FileReader();
                            reader.onload = (e) => {
                                this.heroBgPreview = e.target.result;
                            };
                            reader.readAsDataURL(file);
                        }
                },

                applyUrl() {
                    if (this.tempUrl) {
                        // Ensure object exists
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

                    // CSRF
                    formData.append('_method', 'PUT');

                    fetch('{{ route('admin.landing.update') }}', {
                        method: 'POST',
                        headers: {
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                        },
                        body: formData
                    })
                    .then(res => {
                        if (res.ok) {
                            this.showSuccess = true;
                            setTimeout(() => this.showSuccess = false, 2000);
                        } else {
                            alert('Failed to save changes.');
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('Error saving changes.');
                    })
                    .finally(() => {
                        this.saving = false;
                    });
                }
            }
        }
    </script>
</x-app-layout>
