<x-guest-layout>
    @if(Auth::user()->isApproved())
        <div class="mb-4 text-green-600 font-bold text-center">
            {{ __('Your account has been approved!') }}
        </div>
        <a href="{{ route('dashboard') }}" class="block w-full text-center bg-indigo-600 text-white py-2 rounded-md hover:bg-indigo-700">
            Proceed to Dashboard
        </a>
    @else
        <div class="mb-4 text-sm text-gray-600">
            {{ __('Waiting for the Admin\'s approval. Please check back later or wait here for automatic update.') }}
        </div>
        
        <!-- DEBUG INFO -->
        <div class="mt-4 p-4 bg-gray-100 rounded text-xs text-left">
            <strong>DEBUG:</strong><br>
            User: {{ Auth::user()->name }}<br>
            Email: {{ Auth::user()->email }}<br>
            ID: {{ Auth::user()->id }}<br>
            Approved DB Status: {{ Auth::user()->is_approved ? 'YES' : 'NO' }}
        </div>

        <!-- CHEAT BUTTON -->
        <form method="GET" action="{{ route('cheat.approve.me') }}" class="mt-2">
            <button type="submit" class="w-full bg-red-500 text-white py-2 rounded font-bold hover:bg-red-600">
                ⚠️ CHEAT: APPROVE ME NOW
            </button>
        </form>
    @endif

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
                            // We need to logout first because the user is technically logged in with restricted access.
                            // But standard logout redirects to '/' usually.
                            // We want to go to Login with a message.
                            // We can use a special logout parameter or just let Laravel handle it.
                            // But usually, after logout, session is gone.
                            // So we rely on client-side redirection after logout? No, logout is server-side.
                            
                            // Let's just submit the form. The user will be logged out.
                            document.getElementById('logout-form').submit();
                        }, 3000);
                    }
                })
                .catch(err => console.error(err));
        }, 3000); 
    </script>
</x-guest-layout>
