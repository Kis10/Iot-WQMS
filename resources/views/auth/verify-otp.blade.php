<x-guest-layout>
    <div class="mb-8 text-center">
        <div class="inline-flex items-center justify-center w-16 h-16 bg-blue-50 text-blue-600 rounded-2xl mb-6 shadow-sm">
            <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
        </div>
        <h2 class="text-3xl font-extrabold text-gray-900 tracking-tight mb-2">Verify Your Email</h2>
        <p class="text-gray-500 text-sm">We've sent a 6-digit verification code to <br><span class="font-bold text-gray-900">{{ $email }}</span></p>
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('verify-otp.submit') }}" class="space-y-6">
        @csrf
        <input type="hidden" name="email" value="{{ $email }}">

        <div>
            <x-input-label for="otp" :value="__('Verification Code')" class="text-xs font-bold uppercase tracking-widest text-gray-400 mb-2" />
            <x-text-input id="otp" 
                         type="text" 
                         name="otp" 
                         required 
                         autofocus 
                         class="block w-full text-center text-3xl font-bold tracking-[0.5em] py-4 bg-gray-50 border-gray-200 focus:border-blue-500 focus:ring-blue-500 rounded-2xl transition" 
                         placeholder="000000"
                         maxlength="6"
                         oninput="this.value = this.value.replace(/[^0-9]/g, '')"
            />
            <x-input-error :messages="$errors->get('otp')" class="mt-2" />
            <x-input-error :messages="$errors->get('email')" class="mt-2" />
        </div>

        <div class="flex flex-col gap-4">
            <x-primary-button class="w-full justify-center py-4 bg-blue-600 hover:bg-blue-700 text-white rounded-2xl font-bold shadow-lg shadow-blue-200 transition transform active:scale-95">
                {{ __('Verify Account') }}
            </x-primary-button>
        </div>
    </form>

    <div class="mt-8 pt-6 border-t border-gray-100 text-center" x-data="{ 
        timer: 120, 
        canResend: false,
        init() {
            let interval = setInterval(() => {
                if (this.timer > 0) {
                    this.timer--;
                } else {
                    this.canResend = true;
                    clearInterval(interval);
                }
            }, 1000);
        }
    }">
        <form method="POST" action="{{ route('verify-otp.resend') }}">
            @csrf
            <input type="hidden" name="email" value="{{ $email }}">
            <p class="text-sm text-gray-500">
                Didn't receive the code? 
                <button type="submit" 
                        x-bind:disabled="!canResend"
                        x-bind:class="canResend ? 'text-blue-600 hover:text-blue-700 underline' : 'text-gray-400 cursor-not-allowed'"
                        class="font-bold transition decoration-2 underline-offset-4">
                    Resend Code
                </button>
            </p>
            <p x-show="!canResend" class="text-xs text-gray-400 mt-2">
                You can resend in <span class="font-bold" x-text="Math.floor(timer / 60) + ':' + (timer % 60).toString().padStart(2, '0')"></span>
            </p>
        </form>
        
        <div class="mt-6">
            <a href="{{ route('register') }}" class="text-xs font-bold text-gray-400 hover:text-gray-600 transition uppercase tracking-widest">
                Back to Registration
            </a>
        </div>
    </div>
</x-guest-layout>
