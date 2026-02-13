<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Edit Landing Page Content') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                <div class="p-6 text-gray-900">
                    
                    @if (session('status'))
                        <div class="mb-4 font-medium text-sm text-green-600">
                            {{ session('status') }}
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.landing.update') }}" enctype="multipart/form-data" class="space-y-8">
                        @csrf
                        @method('PUT')

                        <!-- Hero Section -->
                        <div class="border-b pb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Hero Section (Top Area)</h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <!-- Hero Title -->
                                <div>
                                    <x-input-label for="hero_title" :value="__('Main Title (Supports HTML)')" />
                                    <textarea id="hero_title" name="hero_title" rows="3" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('hero_title', $contents['hero_title']->value ?? '') }}</textarea>
                                    <p class="text-xs text-gray-500 mt-1">Use &lt;br&gt; for line breaks. Use &lt;span class="gradient-text"&gt;Text&lt;/span&gt; for blue color.</p>
                                </div>

                                <!-- Hero Subtitle -->
                                <div>
                                    <x-input-label for="hero_subtitle" :value="__('Subtitle')" />
                                    <textarea id="hero_subtitle" name="hero_subtitle" rows="2" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('hero_subtitle', $contents['hero_subtitle']->value ?? '') }}</textarea>
                                </div>

                                <!-- Background Image -->
                                <div>
                                    <x-input-label :value="__('Background Image')" />
                                    
                                    <div class="mt-2 flex items-center gap-4">
                                        @if(isset($contents['hero_bg']->image) && $contents['hero_bg']->image)
                                            <div class="w-32 h-20 bg-gray-200 rounded overflow-hidden">
                                                <img src="{{ asset($contents['hero_bg']->image) }}" class="w-full h-full object-cover">
                                            </div>
                                        @else
                                            <div class="w-32 h-20 bg-gray-100 rounded flex items-center justify-center text-xs text-gray-400 border">No Image</div>
                                        @endif
                                        
                                        <div class="flex-1">
                                            <label class="block text-sm font-medium text-gray-700">Upload New File</label>
                                            <input type="file" name="hero_bg_file" class="mt-1 block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-indigo-50 file:text-indigo-700 hover:file:bg-indigo-100">
                                            
                                            <div class="mt-2">
                                                <span class="text-xs text-gray-500 uppercase font-bold tracking-wider">OR</span>
                                            </div>

                                            <label class="block text-sm font-medium text-gray-700 mt-2">Use Image URL</label>
                                            <input type="text" name="hero_bg_url" placeholder="https://example.com/image.jpg" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Mission Section -->
                        <div class="border-b pb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Mission Section</h3>
                            
                            <div class="grid grid-cols-1 gap-6">
                                <div>
                                    <x-input-label for="mission_badge" :value="__('Badge Text')" />
                                    <x-text-input id="mission_badge" name="mission_badge" type="text" class="mt-1 block w-full" :value="old('mission_badge', $contents['mission_badge']->value ?? 'OUR MISSION')" />
                                </div>
                                <div>
                                    <x-input-label for="mission_title" :value="__('Title')" />
                                    <x-text-input id="mission_title" name="mission_title" type="text" class="mt-1 block w-full" :value="old('mission_title', $contents['mission_title']->value ?? '')" />
                                </div>
                                <div>
                                    <x-input-label for="mission_text" :value="__('Description')" />
                                    <textarea id="mission_text" name="mission_text" rows="4" class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:border-indigo-500 focus:ring-indigo-500">{{ old('mission_text', $contents['mission_text']->value ?? '') }}</textarea>
                                </div>
                            </div>
                        </div>

                         <!-- Services Section -->
                        <div class="border-b pb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Services Section</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="services_title" :value="__('Title')" />
                                    <x-text-input id="services_title" name="services_title" class="mt-1 block w-full" :value="$contents['services_title']->value ?? ''" />
                                </div>
                                <div>
                                    <x-input-label for="services_subtitle" :value="__('Subtitle')" />
                                    <x-text-input id="services_subtitle" name="services_subtitle" class="mt-1 block w-full" :value="$contents['services_subtitle']->value ?? ''" />
                                </div>
                            </div>
                        </div>

                        <!-- Contact Section -->
                        <div class="border-b pb-8">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Contact Section</h3>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <x-input-label for="contact_title" :value="__('Title')" />
                                    <x-text-input id="contact_title" name="contact_title" class="mt-1 block w-full" :value="$contents['contact_title']->value ?? ''" />
                                </div>
                                <div>
                                    <x-input-label for="contact_subtitle" :value="__('Subtitle')" />
                                    <x-text-input id="contact_subtitle" name="contact_subtitle" class="mt-1 block w-full" :value="$contents['contact_subtitle']->value ?? ''" />
                                </div>
                            </div>
                        </div>

                         <!-- Footer -->
                        <div class="">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Footer</h3>
                            <div>
                                <x-input-label for="footer_devs" :value="__('Developer Credit')" />
                                <x-text-input id="footer_devs" name="footer_devs" class="mt-1 block w-full" :value="$contents['footer_devs']->value ?? ''" />
                            </div>
                        </div>

                        <div class="flex items-center gap-4">
                            <x-primary-button>{{ __('Save Changes') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
