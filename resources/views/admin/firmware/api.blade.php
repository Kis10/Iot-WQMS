<x-app-layout>
    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <h2 class="text-2xl font-bold mb-6 text-gray-800">ESP32 API Configuration</h2>
            
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg max-w-2xl">
                <div class="p-6 text-gray-900">
                    
                    @if(session('success'))
                        <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded relative mb-4" role="alert">
                            <span class="block sm:inline">{{ session('success') }}</span>
                        </div>
                    @endif

                    <form method="POST" action="{{ route('admin.firmware.api.update') }}">
                        @csrf
                        @method('PUT')

                        <div class="mb-4">
                            <label for="server_name" class="block text-gray-700 text-sm font-bold mb-2">Base Server URL:</label>
                            <input type="text" name="server_name" id="server_name" value="{{ $serverName }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="mb-4">
                            <label for="device_token" class="block text-gray-700 text-sm font-bold mb-2">Device Token:</label>
                            <input type="text" name="device_token" id="device_token" value="{{ $deviceToken }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>
                        
                        <div class="mb-6">
                            <label for="device_id" class="block text-gray-700 text-sm font-bold mb-2">Device ID:</label>
                            <input type="text" name="device_id" id="device_id" value="{{ $deviceID }}" class="shadow appearance-none border rounded w-full py-2 px-3 text-gray-700 leading-tight focus:outline-none focus:shadow-outline">
                        </div>

                        <div class="flex items-center justify-between">
                            <button type="submit" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                                Save API Configuration
                            </button>
                        </div>
                    </form>

                    <div class="mt-8 p-4 bg-yellow-50 border border-yellow-200 rounded text-sm text-yellow-800">
                        <strong>Note:</strong> Updating these values will modify the <code class="bg-yellow-100 px-1">firmware/aquasense.ino</code> file on the server. You will need to re-compile and upload the sketch to your ESP32 for changes to take effect.
                    </div>

                </div>
            </div>
        </div>
    </div>
</x-app-layout>
