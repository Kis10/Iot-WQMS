<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Waiting for the Admin\'s approval. Please check back later or wait here for automatic update.') }}
    </div>

    <!-- Alert Container (Hidden by default) -->
    <div id="approved-modal" class="fixed inset-0 flex items-center justify-center bg-gray-900 bg-opacity-50 hidden z-50">
        <div class="bg-white p-8 rounded-lg shadow-xl text-center max-w-sm mx-auto">
            <div class="mx-auto flex items-center justify-center h-12 w-12 rounded-full bg-green-100 mb-4">
                <svg class="h-6 w-6 text-green-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                </svg>
            </div>
            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-2">Congrats!</h3>
            <p class="text-sm text-gray-500 mb-6">You had been approved! Redirecting to login...</p>
        </div>
    </div>

    <div class="mt-4 flex items-center justify-between">
        <form method="POST" action="{{ route('logout') }}" id="logout-form">
            @csrf
            <button type="submit" class="underline text-sm text-gray-600 hover:text-gray-900 rounded-md focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-indigo-500">
                {{ __('Log Out') }}
            </button>
        </form>
    </div>

    <script>
        setInterval(() => {
            fetch("{{ route('approval.check') }}")
                .then(res => res.json())
                .then(data => {
                    if (data.approved) {
                        // Show Modal
                        document.getElementById('approved-modal').classList.remove('hidden');
                        
                        // Wait 3 seconds then Logout and Redirect
                        setTimeout(() => {
                            document.getElementById('logout-form').submit();
                        }, 3000);
                    }
                })
                .catch(err => console.error(err));
        }, 3000); 
    </script>
</x-guest-layout>


