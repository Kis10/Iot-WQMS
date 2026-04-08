<?php

namespace App\Http\Controllers\Auth;

use App\Events\UserRegisteredForOtp;
use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:'.User::class],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $registrationEvent = new UserRegisteredForOtp($user);
        event($registrationEvent);

        $status = $registrationEvent->otpSent
            ? 'A verification code has been sent to your email. Please enter it below to activate your account.'
            : 'Your account was created, but email delivery is delayed. Please tap "Resend Code" on the verification page.';

        return redirect()->route('verify-otp', ['email' => $user->email])
            ->with('status', $status);
    }
}
