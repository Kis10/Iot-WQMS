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
                <span x-show="!saving">Save Changes</span>
                <span x-show="saving">Saving...</span>
            </button>
        </div>

        <!-- Floating Text Editor Toolbar -->
        <div x-show="activeElement" x-transition.opacity.duration.200ms
             class="fixed top-24 left-1/2 -translate-x-1/2 z-[100] bg-white rounded-xl shadow-2xl border border-gray-200 p-2 flex items-center gap-2 overflow-x-auto max-w-[90vw] no-scrollbar">
            
            <!-- Font Handling (ExecCommand doesn't support generic font families easily without values, simplifying to Serif/Sans/Mono or keeping generic) -->
            <!-- Font Size -->
            <div class="flex items-center border-r border-gray-200 pr-2 gap-1">
                <button @mousedown.prevent="format('decreaseFontSize')" class="p-1.5 hover:bg-gray-100 rounded text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path></svg>
                </button>
                <span class="text-xs font-bold text-gray-500 w-8 text-center" x-text="currentFontSize + 'px'"></span>
                <button @mousedown.prevent="format('increaseFontSize')" class="p-1.5 hover:bg-gray-100 rounded text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                </button>
            </div>

            <!-- Color -->
            <div class="flex items-center border-r border-gray-200 pr-2 pl-2 relative">
                <input type="color" @input="format('foreColor', $event.target.value)" class="w-8 h-8 rounded cursor-pointer border-none p-0 overflow-hidden" title="Text Color">
            </div>

            <!-- Styles -->
            <div class="flex items-center border-r border-gray-200 pr-2 pl-2 gap-1">
                <button @mousedown.prevent="format('bold')" :class="{ 'bg-blue-100 text-blue-600': isBold, 'hover:bg-gray-100 text-gray-600': !isBold }" class="p-2 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M6 4h8a4 4 0 014 4 4 4 0 01-4 4H6V4zm0 8h9a4 4 0 014 4 4 4 0 01-4 4H6v-12"></path></svg>
                </button>
                <button @mousedown.prevent="format('italic')" :class="{ 'bg-blue-100 text-blue-600': isItalic, 'hover:bg-gray-100 text-gray-600': !isItalic }" class="p-2 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16m4 4l4 4-4 4M6 16l-4-4 4-4"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 20l4-16"></path></svg> <!-- Use generic I icon -->
                    <span class="font-serif italic font-bold">I</span>
                </button>
                <button @mousedown.prevent="format('underline')" :class="{ 'bg-blue-100 text-blue-600': isUnderline, 'hover:bg-gray-100 text-gray-600': !isUnderline }" class="p-2 rounded transition">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 4v6a6 6 0 0012 0V4M4 20h16"></path></svg>
                </button>
                <button @mousedown.prevent="format('strikeThrough')" class="p-2 hover:bg-gray-100 rounded text-gray-600" title="Strikethrough">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 12h14M12 5v14"></path></svg>
                </button>
            </div>

            <!-- Alignment & Lists -->
            <div class="flex items-center border-r border-gray-200 pr-2 pl-2 gap-1">
                <button @mousedown.prevent="format('justifyLeft')" class="p-2 hover:bg-gray-100 rounded text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h10M4 18h16"></path></svg>
                </button>
                <button @mousedown.prevent="format('justifyCenter')" class="p-2 hover:bg-gray-100 rounded text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path></svg>
                </button>
                <button @mousedown.prevent="format('justifyRight')" class="p-2 hover:bg-gray-100 rounded text-gray-600">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M10 12h10M4 18h16"></path></svg>
                </button>
                <button @mousedown.prevent="format('insertUnorderedList')" class="p-2 hover:bg-gray-100 rounded text-gray-600" title="Bullet List">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16M4 6.01l.01-.011"></path></svg> <!-- Generic List -->
                </button>
            </div>

            <!-- Misc -->
            <div class="flex items-center pl-2 gap-1">
                 <button @mousedown.prevent="format('removeFormat')" class="p-2 hover:bg-red-50 text-red-500 rounded transition" title="Clear Formatting">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg>
                </button>
            </div>
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
                        <span class="text-gray-400 cursor-not-allowed">About</span>
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
                <template x-if="!heroBgPreview && data.hero_bg && (data.hero_bg.image || data.hero_bg.value)">
                    <img :src="(data.hero_bg.image || data.hero_bg.value).startsWith('http') ? (data.hero_bg.image || data.hero_bg.value) : (
                        (data.hero_bg.image || data.hero_bg.value).startsWith('/') ? (data.hero_bg.image || data.hero_bg.value) : (
                        (data.hero_bg.image || data.hero_bg.value).startsWith('img/') ? '/' + (data.hero_bg.image || data.hero_bg.value) :
                        '/storage/' + (data.hero_bg.image || data.hero_bg.value)
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
                <div class="mb-8">
                    <h1 @click="makeEditableHtml($event)" 
                        @blur="stopEditingHtml($event, 'hero_title')"
                        @keydown.enter.prevent="$event.target.blur()"
                        class="editable-hover text-5xl md:text-7xl lg:text-8xl font-black text-white leading-tight tracking-tight outline-none focus:outline-none"
                        x-html="data.hero_title.value"></h1>
                    <p class="text-white/30 text-[10px] mt-2 font-mono">Double-click to edit (Supports HTML)</p>
                </div>

                <!-- Hero Subtitle -->
                <p @click="makeEditable($event)" @blur="stopEditing($event, 'hero_subtitle')"
                   class="editable-hover text-xl md:text-2xl text-blue-100 max-w-3xl mx-auto font-medium leading-relaxed opacity-90 mb-12"
                   x-html="data.hero_subtitle.value" :style="data.hero_subtitle.style || ''"></p>
            </div>
        </section>

        <!-- Mission Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <div class="max-w-3xl mx-auto">
                    <!-- Badge -->
                    <div class="mb-6 flex justify-center">
                        <div @click="makeEditable($event)" @blur="stopEditing($event, 'mission_badge')"
                             class="editable-hover inline-flex items-center px-4 py-1.5 rounded-full bg-blue-50 text-blue-600 text-sm font-bold"
                             x-html="data.mission_badge.value" :style="data.mission_badge.style || ''"></div>
                    </div>

                    <!-- Title -->
                    <h2 @click="makeEditable($event)" @blur="stopEditing($event, 'mission_title')"
                        class="editable-hover text-4xl font-bold tracking-tight mb-8" style="color: #0D1A63;"
                        x-html="data.mission_title.value" :style="data.mission_title.style || ''"></h2>

                    <!-- Text -->
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'mission_text')"
                       class="editable-hover text-xl text-gray-600 leading-relaxed font-light"
                       x-html="data.mission_text.value" :style="data.mission_text.style || ''"></p>
                </div>
            </div>
        </section>

        <!-- Sensors Section -->
        <section class="py-24 bg-gray-50 overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 @click="makeEditable($event)" @blur="stopEditing($event, 'sensors_title')"
                        class="editable-hover text-4xl font-bold tracking-tight mb-4" style="color: #0D1A63;"
                        x-html="data.sensors_title.value" :style="data.sensors_title.style || ''"></h2>
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'sensors_subtitle')"
                       class="editable-hover text-gray-500 text-lg"
                       x-html="data.sensors_subtitle.value" :style="data.sensors_subtitle.style || ''"></p>
                </div>

                <!-- Sensor Cards -->
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-12">
                    <!-- pH -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path></svg>
                        </div>
                        <h3 @click="makeEditable($event)" @blur="stopEditing($event, 'sensor1_title')"
                            class="editable-hover text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;" x-html="data.sensor1_title.value" :style="data.sensor1_title.style || ''"></h3>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'sensor1_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-html="data.sensor1_desc.value" :style="data.sensor1_desc.style || ''"></p>
                    </div>

                    <!-- Turbidity -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 10a1 1 0 011-1h4a1 1 0 110 2h-4a1 1 0 01-1-1z"></path><circle cx="12" cy="14" r="1" fill="currentColor"></circle><circle cx="15" cy="13" r="0.5" fill="currentColor"></circle><circle cx="9" cy="13" r="0.5" fill="currentColor"></circle></svg>
                        </div>
                        <h3 @click="makeEditable($event)" @blur="stopEditing($event, 'sensor2_title')"
                            class="editable-hover text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;" x-html="data.sensor2_title.value" :style="data.sensor2_title.style || ''"></h3>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'sensor2_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-html="data.sensor2_desc.value" :style="data.sensor2_desc.style || ''"></p>
                    </div>

                    <!-- TDS -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.428 15.428a2 2 0 00-1.022-.547l-2.387-.477a6 6 0 00-3.86.517l-.691.383a4 4 0 01-2.573.344l-2.387-.477a2 2 0 00-1.022.547l-.736.736a2 2 0 000 2.828l.736.736a2 2 0 001.022.547l2.387.477a6 6 0 003.86-.517l.691-.383a4 4 0 012.573-.344l2.387.477a2 2 0 001.022-.547l.736-.736a2 2 0 000-2.828l-.736-.736z"></path><circle cx="12" cy="14" r="1.5" fill="currentColor"></circle><circle cx="15.5" cy="12.5" r="1" fill="currentColor"></circle><circle cx="8.5" cy="12.5" r="1" fill="currentColor"></circle><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 3v6"></path></svg>
                        </div>
                        <h3 @click="makeEditable($event)" @blur="stopEditing($event, 'sensor3_title')"
                            class="editable-hover text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;" x-html="data.sensor3_title.value" :style="data.sensor3_title.style || ''"></h3>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'sensor3_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-html="data.sensor3_desc.value" :style="data.sensor3_desc.style || ''"></p>
                    </div>

                    <!-- Temperature -->
                    <div class="sensor-card bg-white p-8 rounded-2xl border border-gray-100 transition-all duration-300 group mb-8">
                        <div class="w-16 h-16 bg-gray-100 rounded-2xl flex items-center justify-center mb-6 text-gray-900 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300 shadow-sm">
                            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19c-1.657 0-3-1.343-3-3V6a3 3 0 116 0v10c0 1.657-1.343 3-3 3z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9h4m-4 4h4"></path></svg>
                        </div>
                        <h3 @click="makeEditable($event)" @blur="stopEditing($event, 'sensor4_title')"
                            class="editable-hover text-xl font-bold mb-4 tracking-tight" style="color: #0D1A63;" x-html="data.sensor4_title.value" :style="data.sensor4_title.style || ''"></h3>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'sensor4_desc')"
                           class="editable-hover text-gray-600 text-sm leading-relaxed" x-html="data.sensor4_desc.value" :style="data.sensor4_desc.style || ''"></p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Services Section -->
        <section class="py-24 bg-white overflow-hidden">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 @click="makeEditable($event)" @blur="stopEditing($event, 'services_title')"
                        class="editable-hover text-4xl font-bold tracking-tight mb-4" style="color: #0D1A63;"
                        x-html="data.services_title.value" :style="data.services_title.style || ''"></h2>
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'services_subtitle')"
                       class="editable-hover text-gray-500 text-lg"
                       x-html="data.services_subtitle.value" :style="data.services_subtitle.style || ''"></p>
                </div>

                <div class="max-w-4xl mx-auto space-y-12">
                    <!-- Service 1 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">
                            <span @click="makeEditable($event)" @blur="stopEditing($event, 'service1_num')"
                                  class="editable-hover" x-html="data.service1_num.value" :style="data.service1_num.style || ''"></span>
                        </div>
                        <div class="flex-1">
                            <h4 @click="makeEditable($event)" @blur="stopEditing($event, 'service1_title')"
                                class="editable-hover text-2xl font-bold mb-3 tracking-tight" style="color: #0D1A63;" x-html="data.service1_title.value" :style="data.service1_title.style || ''"></h4>
                            <p @click="makeEditable($event)" @blur="stopEditing($event, 'service1_desc')"
                               class="editable-hover text-gray-600 leading-relaxed text-lg" x-html="data.service1_desc.value" :style="data.service1_desc.style || ''"></p>
                        </div>
                    </div>
                    <!-- Service 2 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">
                            <span @click="makeEditable($event)" @blur="stopEditing($event, 'service2_num')"
                                  class="editable-hover" x-html="data.service2_num.value" :style="data.service2_num.style || ''"></span>
                        </div>
                        <div class="flex-1">
                            <h4 @click="makeEditable($event)" @blur="stopEditing($event, 'service2_title')"
                                class="editable-hover text-2xl font-bold mb-3 tracking-tight" style="color: #0D1A63;" x-html="data.service2_title.value" :style="data.service2_title.style || ''"></h4>
                            <p @click="makeEditable($event)" @blur="stopEditing($event, 'service2_desc')"
                               class="editable-hover text-gray-600 leading-relaxed text-lg" x-html="data.service2_desc.value" :style="data.service2_desc.style || ''"></p>
                        </div>
                    </div>
                    <!-- Service 3 -->
                    <div class="flex gap-8 items-start">
                        <div class="shrink-0 w-14 h-14 rounded-2xl bg-gray-100 flex items-center justify-center text-black font-bold text-xl">
                            <span @click="makeEditable($event)" @blur="stopEditing($event, 'service3_num')"
                                  class="editable-hover" x-html="data.service3_num.value" :style="data.service3_num.style || ''"></span>
                        </div>
                        <div class="flex-1">
                            <h4 @click="makeEditable($event)" @blur="stopEditing($event, 'service3_title')"
                                class="editable-hover text-2xl font-bold mb-3 tracking-tight" style="color: #0D1A63;" x-html="data.service3_title.value" :style="data.service3_title.style || ''"></h4>
                            <p @click="makeEditable($event)" @blur="stopEditing($event, 'service3_desc')"
                               class="editable-hover text-gray-600 leading-relaxed text-lg" x-html="data.service3_desc.value" :style="data.service3_desc.style || ''"></p>
                        </div>
                    </div>
                </div>
            </div>
        </section>

        <!-- About / Team Section -->
        <section class="py-24 bg-gray-50 overflow-hidden border-t border-gray-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 @click="makeEditable($event)" @blur="stopEditing($event, 'about_title')"
                        class="editable-hover text-4xl font-bold mb-4 tracking-tight" style="color: #0D1A63;"
                        x-html="data.about_title.value" :style="data.about_title.style || ''"></h2>
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'about_subtitle')"
                       class="editable-hover text-gray-500 text-lg max-w-2xl mx-auto"
                       x-html="data.about_subtitle.value" :style="data.about_subtitle.style || ''"></p>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
                    @foreach(['team1', 'team2', 'team3', 'team4'] as $key)
                    <div class="group relative bg-white rounded-3xl p-6 shadow-sm border border-gray-100 text-center hover:-translate-y-2 transition-all duration-300">
                        <div class="w-32 h-32 mx-auto rounded-full overflow-hidden bg-gray-100 mb-6 shadow-inner relative group/img border-4 bg-white" style="border-color: #0D1A63;">
                            <!-- Main Image -->
                            <template x-if="previews['{{ $key }}_img']">
                                <img :src="previews['{{ $key }}_img']" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previews['{{ $key }}_img'] && data.{{ $key }}_img && (data.{{ $key }}_img.image || data.{{ $key }}_img.value) && (data.{{ $key }}_img.image || data.{{ $key }}_img.value).startsWith('http')">
                                <img :src="data.{{ $key }}_img.image || data.{{ $key }}_img.value" class="w-full h-full object-cover">
                            </template>
                            <template x-if="!previews['{{ $key }}_img'] && (!data.{{ $key }}_img || (!data.{{ $key }}_img.image && !data.{{ $key }}_img.value))">
                                <div class="w-full h-full flex items-center justify-center bg-blue-50 text-blue-200">
                                    <svg class="w-12 h-12" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 9a3 3 0 100-6 3 3 0 000 6zm-7 9a7 7 0 1114 0H3z" clip-rule="evenodd"></path></svg>
                                </div>
                            </template>

                            <!-- Hover Image -->
                            <template x-if="previews['{{ $key }}_img_hover']">
                                <img :src="previews['{{ $key }}_img_hover']" class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover/img:opacity-100 transition-opacity duration-300 z-10 bg-white">
                            </template>
                            <template x-if="!previews['{{ $key }}_img_hover'] && data.{{ $key }}_img_hover && (data.{{ $key }}_img_hover.image || data.{{ $key }}_img_hover.value) && (data.{{ $key }}_img_hover.image || data.{{ $key }}_img_hover.value).startsWith('http')">
                                <img :src="data.{{ $key }}_img_hover.image || data.{{ $key }}_img_hover.value" 
                                     class="absolute inset-0 w-full h-full object-cover opacity-0 group-hover/img:opacity-100 transition-opacity duration-300 z-10 bg-white">
                            </template>
                            
                            <!-- Edit Overlay -->
                            <div class="absolute inset-0 bg-black/50 flex items-center justify-center opacity-0 group-hover/img:opacity-100 transition duration-200 z-20">
                                <button @click="openUploadModal('{{ $key }}_img')" class="text-white text-xs font-bold uppercase tracking-wider hover:text-blue-300">Change</button>
                            </div>
                        </div>

                        <h3 @click="makeEditable($event)" @blur="stopEditing($event, '{{ $key }}_name')"
                            class="editable-hover text-lg font-bold mb-1" style="color: #0D1A63;" x-text="data.{{ $key }}_name.value"></h3>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, '{{ $key }}_role')"
                           class="editable-hover text-blue-600 font-medium text-xs uppercase tracking-wide mb-4" x-text="data.{{ $key }}_role.value"></p>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, '{{ $key }}_desc')"
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
                    <h2 @click="makeEditable($event)" @blur="stopEditing($event, 'contact_title')"
                        class="editable-hover text-4xl font-bold tracking-tight mb-4" style="color: #0D1A63;"
                        x-html="data.contact_title.value" :style="data.contact_title.style || ''"></h2>
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'contact_subtitle')"
                       class="editable-hover text-gray-500 text-lg"
                       x-html="data.contact_subtitle.value" :style="data.contact_subtitle.style || ''"></p>
                </div>

                <div class="max-w-5xl mx-auto grid grid-cols-1 md:grid-cols-3 gap-12 text-center">
                    <!-- Email -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm group hover:-translate-y-2 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h4 @click="makeEditable($event)" @blur="stopEditing($event, 'contact_email_label')"
                            class="editable-hover text-xl font-bold mb-2" style="color: #0D1A63;" x-html="data.contact_email_label.value" :style="data.contact_email_label.style || ''"></h4>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'contact_email')"
                           class="editable-hover text-gray-500 font-medium" x-html="data.contact_email.value" :style="data.contact_email.style || ''"></p>
                    </div>
                    <!-- Phone -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm group hover:-translate-y-2 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                        </div>
                        <h4 @click="makeEditable($event)" @blur="stopEditing($event, 'contact_phone_label')"
                            class="editable-hover text-xl font-bold mb-2" style="color: #0D1A63;" x-html="data.contact_phone_label.value" :style="data.contact_phone_label.style || ''"></h4>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'contact_phone')"
                           class="editable-hover text-gray-500 font-medium" style="white-space: pre-line;" x-html="data.contact_phone.value" :style="data.contact_phone.style || ''"></p>
                    </div>
                    <!-- Location -->
                    <div class="p-8 bg-white rounded-3xl border border-gray-100 shadow-sm group hover:-translate-y-2 transition-all duration-300">
                        <div class="w-12 h-12 bg-gray-100 text-black rounded-xl flex items-center justify-center mx-auto mb-6 group-hover:bg-[#0D1A63] group-hover:text-white transition-all duration-300">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        </div>
                        <h4 @click="makeEditable($event)" @blur="stopEditing($event, 'contact_location_label')"
                            class="editable-hover text-xl font-bold mb-2" style="color: #0D1A63;" x-html="data.contact_location_label.value" :style="data.contact_location_label.style || ''"></h4>
                        <p @click="makeEditable($event)" @blur="stopEditing($event, 'contact_location')"
                           class="editable-hover text-gray-500 font-medium" x-html="data.contact_location.value" :style="data.contact_location.style || ''"></p>
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
                        <span @click="makeEditable($event)" @blur="stopEditing($event, 'footer_brand')"
                              class="editable-hover text-lg font-bold text-gray-700 tracking-wider" x-html="data.footer_brand.value" :style="data.footer_brand.style || ''"></span>
                    </div>
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'footer_copyright')"
                       class="editable-hover text-gray-500 text-sm mb-4" x-html="data.footer_copyright.value" :style="data.footer_copyright.style || ''"></p>
                    <p @click="makeEditable($event)" @blur="stopEditing($event, 'footer_devs')"
                       class="editable-hover text-sm font-medium text-gray-500 mt-2" x-html="data.footer_devs.value" :style="data.footer_devs.style || ''"></p>
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

                    <!-- Hover Effect Upload (3rd Option: Only shows if a primary photo exists) -->
                    <button x-show="currentUploadKey && currentUploadKey.includes('team') && !currentUploadKey.endsWith('_hover') && (previews[currentUploadKey] || (data[currentUploadKey] && (data[currentUploadKey].image || data[currentUploadKey].value)))"
                            @click="currentUploadKey = currentUploadKey.replace('_img', '') + '_img_hover'; $refs.bgFileInput.click(); showBgModal = false;" 
                            class="w-full flex items-center gap-3 px-4 py-3 rounded-xl border border-gray-200 hover:border-blue-400 hover:bg-blue-50 transition text-left group">
                        <div class="w-10 h-10 bg-pink-100 rounded-lg flex items-center justify-center text-pink-600 shrink-0 group-hover:bg-pink-200 transition-colors">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-gray-900 text-sm">Upload Hover Photo</div>
                            <div class="text-gray-400 text-xs">Shown when someone hovers over the photo</div>
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
    <!-- ================================ -->
    <!-- Image Crop/Adjust Modal          -->
    <!-- ================================ -->
    <div x-show="showCropModal" x-cloak class="fixed inset-0 z-[10000] flex items-center justify-center bg-black/80 backdrop-blur-sm" x-transition style="display: none;">
        <div class="bg-white rounded-2xl shadow-2xl overflow-hidden w-[95%] max-w-4xl flex flex-col max-h-[90vh]">
            
            <!-- Header -->
            <div class="px-6 py-4 border-b border-gray-100 flex justify-between items-center bg-white z-10">
                <div>
                    <h3 class="text-xl font-bold text-gray-900">Adjust Image</h3>
                    <p class="text-gray-500 text-sm">Drag to position • Scroll or set slider to zoom</p>
                </div>
                <button @click="closeCropModal" class="text-gray-400 hover:text-gray-600 transition p-2 rounded-full hover:bg-gray-100">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                </button>
            </div>

            <!-- Cropper Viewport -->
            <div class="relative flex-1 bg-slate-900 overflow-hidden cursor-grab active:cursor-grabbing group min-h-[400px]"
                 @mousedown="startDrag"
                 @mousemove="onDrag"
                 @mouseup="stopDrag"
                 @mouseleave="stopDrag"
                 @touchstart="startDrag"
                 @touchmove="onDrag"
                 @touchend="stopDrag"
                 @wheel.prevent="handleWheel"
                 x-ref="viewport">
                
                <!-- Checkerboard Pattern for Transparency -->
                <div class="absolute inset-0 opacity-20" style="background-image: radial-gradient(#4d4d4d 1px, transparent 1px); background-size: 20px 20px;"></div>

                <!-- Image Wrapper -->
                <div class="absolute inset-0 flex items-center justify-center pointer-events-none">
                     <div class="relative origin-center user-select-none transition-transform duration-75" :style="imageStyle">
                        <img :src="cropImageSrc" x-ref="cropImg" class="max-w-none shadow-2xl pointer-events-none select-none block" @load="initCrop">
                    </div>
                </div>

                <!-- Overlay/Mask -->
                <!-- The mask is essentially a giant border around a transparent hole -->
                <div class="absolute inset-0 pointer-events-none z-10 flex items-center justify-center">
                    <div class="relative" :class="cropShape === 'circle' ? 'w-64 h-64 rounded-full' : 'w-[90%] h-[70%] border-white/50 border-2'">
                        <!-- Darken outside -->
                        <div class="absolute -inset-[100vh] border-[100vh] border-black/60 scale-[2]" 
                             :class="cropShape === 'circle' ? 'rounded-[50%]' : ''"></div>
                        
                        <!-- Guidelines -->
                        <div class="absolute inset-0 border-2 border-white/80 shadow-sm" :class="cropShape === 'circle' ? 'rounded-full' : ''"></div>
                        
                        <!-- Grid Lines (Rule of Thirds) - Optional, mainly for rect -->
                        <div x-show="cropShape !== 'circle'" class="absolute inset-0 flex flex-col justify-between pointer-events-none opacity-30">
                            <div class="flex-1 border-b border-white"></div>
                            <div class="flex-1 border-b border-white"></div>
                            <div class="flex-1"></div>
                        </div>
                        <div x-show="cropShape !== 'circle'" class="absolute inset-0 flex flex-row justify-between pointer-events-none opacity-30">
                            <div class="flex-1 border-r border-white"></div>
                            <div class="flex-1 border-r border-white"></div>
                            <div class="flex-1"></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Controls -->
            <div class="px-6 py-4 bg-white border-t border-gray-100 z-10">
                <div class="flex items-center gap-4 mb-4">
                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path></svg>
                    <input type="range" class="w-full h-2 bg-gray-200 rounded-lg appearance-none cursor-pointer accent-blue-600 focus:outline-none focus:ring-2 focus:ring-blue-500/50"
                           :min="minScale" :max="maxScale" step="0.01" x-model.number="cropScale">
                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM13 10H7"></path></svg>
                </div>

                <div class="flex justify-end gap-3">
                    <button @click="closeCropModal" class="px-6 py-2.5 rounded-xl text-gray-600 font-bold hover:bg-gray-100 transition border border-gray-200 hover:border-gray-300">Cancel</button>
                    <button @click="applyCrop" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-2.5 rounded-xl font-bold shadow-lg shadow-blue-600/20 transition flex items-center gap-2 transform active:scale-95">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                        Apply Crop
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
                sensors_subtitle: { value: "Our system utilizes four high-precision sensors to capture every critical metric." },

                sensor1_title: { value: "pH Sensor" },
                sensor1_desc: { value: "Measures the acidity or alkalinity of the water to ensure a healthy environment for aquatic life." },
                sensor2_title: { value: "Turbidity" },
                sensor2_desc: { value: "Detects water clarity by measuring suspended particles, crucial for accurate quality assessment." },
                sensor3_title: { value: "TDS Sensor" },
                sensor3_desc: { value: "Monitors the concentration of dissolved substances, indicating the overall purity of the water." },
                sensor4_title: { value: "Temperature" },
                sensor4_desc: { value: "Tracks water temperature to prevent thermal stress and maintain optimal growth rates for fish." },

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
                team1_img_hover: { value: null },
                team2_name: { value: "Dannica J. Besinio" },
                team2_role: { value: "Documenter" },
                team2_desc: { value: "Ensures comprehensive documentation of system processes and user guides." },
                team2_img: { value: null },
                team2_img_hover: { value: null },
                team3_name: { value: "Joy Mae A. Samra" },
                team3_role: { value: "Documenter" },
                team3_desc: { value: "Focuses on research, technical writing, and system validation." },
                team3_img: { value: null },
                team3_img_hover: { value: null },
                team4_name: { value: "Jonas D. Parraño" },
                team4_role: { value: "System Analyst / Capstone Adviser" },
                team4_desc: { value: "Provides expert guidance on system architecture and project direction." },
                team4_img: { value: null },
                team4_img_hover: { value: null },

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
                
                // Toolbar State
                activeElement: null,
                currentFontSize: 16,
                isBold: false,
                isItalic: false,
                isUnderline: false,

                init() {
                    console.log('Landing Editor Initialized');
                    // Ensure all data items have a style property
                    for (const key in this.data) {
                        if (typeof this.data[key] === 'object' && !this.data[key].style) {
                            this.data[key].style = '';
                        }
                    }
                },

                format(cmd, value) {
                    if (!this.activeElement) return;
                    console.log('Formatting:', cmd, value); 
                    
                    if (cmd === 'increaseFontSize' || cmd === 'decreaseFontSize') {
                        // Direct Style Manipulation (Pixel Perfect)
                        const currentStyle = window.getComputedStyle(this.activeElement);
                        let currentSize = parseFloat(currentStyle.fontSize);
                        let newSize = cmd === 'increaseFontSize' ? currentSize + 4 : currentSize - 4; // Bigger steps
                        if (newSize < 8) newSize = 8;
                        this.activeElement.style.fontSize = newSize + 'px';
                        this.currentFontSize = Math.round(newSize);
                    } 
                    else if (cmd === 'justifyLeft' || cmd === 'justifyCenter' || cmd === 'justifyRight' || cmd === 'justifyFull') {
                         // Apply Alignment to Block (cleaner than execCommand for Headers)
                         let align = cmd.replace('justify', '').toLowerCase();
                         if (align === 'full') align = 'justify';
                         this.activeElement.style.textAlign = align;
                    }
                    else if (cmd === 'foreColor') {
                         document.execCommand('styleWithCSS', false, true);
                         document.execCommand('foreColor', false, value);
                    } else {
                        document.execCommand(cmd, false, value);
                    }
                    
                    this.activeElement.focus();
                    this.updateToolbarState();
                },

                updateToolbarState() {
                    if (!this.activeElement) return;
                    
                    // Update Font Size Display
                    const style = window.getComputedStyle(this.activeElement);
                    this.currentFontSize = Math.round(parseFloat(style.fontSize));

                    // Update Formatting Buttons
                    this.isBold = document.queryCommandState('bold');
                    this.isItalic = document.queryCommandState('italic');
                    this.isUnderline = document.queryCommandState('underline');
                },
                
                // Highlight Active Element
                setActive(el) {
                    if (this.activeElement) {
                        this.activeElement.style.outline = 'none';
                    }
                    this.activeElement = el;
                    this.activeElement.style.outline = '2px solid #3b82f6'; // Blue outline
                    this.updateToolbarState();
                },

                // Crop State
                showCropModal: false,
                cropImageSrc: null,
                cropScale: 1,
                minScale: 0.1,
                maxScale: 3,
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
                    
                    // Toolbar Support
                    this.setActive(el);
                    el.addEventListener('keyup', () => this.updateToolbarState());
                    el.addEventListener('mouseup', () => this.updateToolbarState());
                    el.addEventListener('click', () => this.updateToolbarState());
                },

                // Stop editing and sync the value back
                stopEditing(e, key) {
                    const el = e.target;
                    el.removeAttribute('contenteditable');
                    el.style.outline = 'none'; // Clear outline
                    
                    // Save innerHTML to preserve formatting (Bold, Italic, etc.)
                    this.data[key].value = el.innerHTML;
                    
                    // Save Block Styles (Font Size, Alignment)
                    if (this.data[key] && typeof this.data[key] === 'object') {
                        this.data[key].style = el.getAttribute('style') || '';
                    }

                    this.activeElement = null; // Hide Toolbar
                },



                // HTML Aware Editing (For Hero Title)
                makeEditableHtml(e) {
                    const el = e.target;
                    el.setAttribute('contenteditable', 'true');
                    el.focus();
                    
                    // Toolbar Support
                    this.setActive(el);
                    el.addEventListener('keyup', () => this.updateToolbarState());
                    el.addEventListener('mouseup', () => this.updateToolbarState());
                },
                
                stopEditingHtml(e, key) {
                    const el = e.target;
                    el.removeAttribute('contenteditable');
                    el.style.outline = 'none';
                    
                    // Save innerHTML to preserve spans/formatting
                    this.data[key].value = el.innerHTML;
                    
                    // Save Styles
                    if (this.data[key] && typeof this.data[key] === 'object') {
                        this.data[key].style = el.getAttribute('style') || '';
                    }

                    this.activeElement = null;
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
                            this.$nextTick(() => { this.showCropModal = true; });
                        };
                        reader.readAsDataURL(file);
                    }
                    // Reset input so same file can be selected again if cancelled
                    e.target.value = '';
                },

                initCrop() {
                    const img = this.$refs.cropImg;
                    const viewport = this.$refs.viewport;
                    
                    // Wait for layout
                    this.$nextTick(() => {
                        const vw = viewport.clientWidth;
                        const vh = viewport.clientHeight;
                        const iw = img.naturalWidth;
                        const ih = img.naturalHeight;

                        // Calculate fit scale (contain)
                        const scaleW = vw / iw;
                        const scaleH = vh / ih;
                        const fitScale = Math.min(scaleW, scaleH) * 0.8; // 0.8 to leave some room

                        this.minScale = fitScale * 0.5; // Allow zooming out a bit more than fit
                        this.maxScale = Math.max(fitScale * 5, 2); // Allow 5x zoom or at least 2x
                        
                        this.cropScale = fitScale;
                        this.cropX = 0;
                        this.cropY = 0;
                    });
                },

                startDrag(e) {
                    e.preventDefault();
                    this.isDragging = true;
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

                handleWheel(e) {
                    const delta = e.deltaY > 0 ? -0.1 : 0.1;
                    const newScale = Math.max(this.minScale, Math.min(this.maxScale, this.cropScale + delta * this.cropScale));
                    this.cropScale = newScale;
                },

                closeCropModal() {
                    this.showCropModal = false;
                    this.cropImageSrc = null;
                },

                applyCrop() {
                    const img = this.$refs.cropImg;
                    const canvas = document.createElement('canvas');
                    const ctx = canvas.getContext('2d');

                    // Circle shape target size
                    // For circle (team), we want square output. 
                    // For rect (hero), we want 16:9 or similar? 
                    // Let's stick to the visual aspect ratio.
                    
                    let targetWidth = 500;
                    let targetHeight = 500;
                    
                    if (this.cropShape !== 'circle') {
                        // Rect shape (Hero BG) - usually strict 16:9 is best for hero, or wide.
                        // Our visual guide is 90% w, 70% h.
                        // Let's measure the user's visual guide aspect ratio?
                        // It's dynamic in css.
                        // Let's just default to a standard high-res landscape for hero.
                        targetWidth = 1920;
                        targetHeight = 1080;
                    }

                    canvas.width = targetWidth;
                    canvas.height = targetHeight;

                    // 1. Calculate the Visual Metrics
                    const viewport = this.$refs.viewport;
                    const vRect = viewport.getBoundingClientRect();
                    const centerX = vRect.width / 2;
                    const centerY = vRect.height / 2;

                    // Locate the "Mask/Hole" in the viewport
                    // Circle: w-64 h-64 (16rem = 256px) centered.
                    // Rect: 90% w, 70% h centered.
                    let maskW, maskH;

                    if (this.cropShape === 'circle') {
                        maskW = 256; 
                        maskH = 256;
                    } else {
                        maskW = vRect.width * 0.9;
                        maskH = vRect.height * 0.7;
                    }

                    // 2. Draw Logic
                    // We want to draw the portion of the image that is visible under the Mask into the Canvas.
                    // Mapping: Mask TopLeft in Viewport -> Canvas(0,0)
                    //          Mask BottomRight in Viewport -> Canvas(W,H)
                    
                    // The image is at: ViewportCenter + (cropX, cropY)
                    // Image Scale: cropScale (visual scale)
                    
                    // Calculate Image Position relative to Mask TopLeft
                    // Mask TopLeft relative to Viewport = (Center - Mask/2)
                    const maskX = centerX - maskW/2;
                    const maskY = centerY - maskH/2;
                    
                    // Image Position (visual top-left of image element) relative to Viewport
                    // The img is centered in the div, and div is translated.
                    // Img Natural Width/Height * Scale
                    const natW = img.naturalWidth;
                    const natH = img.naturalHeight;
                    
                    // Visual Width/Height
                    const visW = natW * this.cropScale;
                    const visH = natH * this.cropScale;
                    
                    // Image Center relative to Viewport = Center + CropOffset
                    const imgCenterX = centerX + this.cropX;
                    const imgCenterY = centerY + this.cropY;
                    
                    // Image TopLeft relative to Viewport
                    const imgX = imgCenterX - visW/2;
                    const imgY = imgCenterY - visH/2;
                    
                    // 3. Offset into Image
                    // Where does the Mask start relative to the Image?
                    // deltaX = MaskX - ImgX
                    const deltaX = maskX - imgX;
                    const deltaY = maskY - imgY;
                    
                    // So we want to copy from (deltaX, deltaY) with width (maskW), height (maskH)
                    // FROM the SCALED image.
                    
                    // Converting effectively to SOURCE coordinates (Unscaled Image):
                    // sourceX = deltaX / scale
                    // sourceY = deltaY / scale
                    // sourceW = maskW / scale
                    // sourceH = maskH / scale
                    
                    const sX = deltaX / this.cropScale;
                    const sY = deltaY / this.cropScale;
                    const sW = maskW / this.cropScale;
                    const sH = maskH / this.cropScale;
                    
                    // Draw to Canvas
                    // Canvas size is fixed (targetWidth, targetHeight).
                    // We draw the source rect -> canvas rect.
                    
                    // Fill background (white/black?)
                    ctx.fillStyle = '#ffffff';
                    ctx.fillRect(0,0, targetWidth, targetHeight);
                    
                    ctx.drawImage(
                        img,
                        sX, sY, sW, sH,    // Source
                        0, 0, targetWidth, targetHeight // Dest
                    );
                    
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
                        // Check if key is related to images
                        const isImageKey = key === 'hero_bg' || key.endsWith('_img') || key.endsWith('_img_hover');

                        // Save text values (ALWAYS send text to keep them updated)
                        if (!isImageKey) {
                             formData.append(key, this.data[key].value);
                        }
                    }

                    // Explicitly handle Hero BG Change
                    if (this.heroBgFile) {
                        formData.append('hero_bg_file', this.heroBgFile);
                    } else if (this.heroBgPreview && this.heroBgPreview.startsWith('http')) {
                        // Only if it's a new URL (preview is set)
                        formData.append('hero_bg_url', this.heroBgPreview);
                    }

                    // Append ONLY New Image Files
                    for (const key in this.files) {
                        formData.append(key + '_file', this.files[key]);
                    }

                    // Append ONLY New Image URLs (if any were set via modal)
                    // We can track this by checking if previews exist but no file
                    for (const key in this.previews) {
                        if (key !== 'hero_bg' && !this.files[key] && this.previews[key].startsWith('http')) {
                             formData.append(key + '_url', this.previews[key]);
                        }
                    }

                    formData.append('_method', 'PUT');

                    fetch('{{ route('admin.landing.update') }}', {
                        method: 'POST',
                        headers: { 
                            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                            'Accept': 'application/json'
                        },
                        body: formData
                    })
                    .then(async res => {
                        if (res.ok) {
                            this.showSuccess = true;
                            setTimeout(() => window.location.reload(), 1000);
                        } else {
                            const error = await res.json().catch(() => ({ message: 'Unknown Server Error' }));
                            alert('Failed to save changes: ' + (error.message || 'Check Server Logs'));
                        }
                    })
                    .catch(err => { 
                        console.error(err); 
                        alert('Network Error: Could not connect to server.'); 
                    })
                    .finally(() => { this.saving = false; });
                }
            }
        }
    </script>
</x-app-layout>

