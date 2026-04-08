<?php

use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

test('registration screen can be rendered', function () {
    $response = $this->get('/register');

    $response->assertStatus(200);
});

test('new users are redirected to otp verification and receive otp email', function () {
    Mail::fake();

    $response = $this->post('/register', [
        'name' => 'Test User',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $user = User::where('email', 'test@example.com')->firstOrFail();

    $this->assertGuest();
    $response->assertRedirect(route('verify-otp', ['email' => $user->email], false));

    Mail::assertSent(OTPMail::class, fn (OTPMail $mail) => $mail->hasTo($user->email));

    expect($user->otp_code)->not->toBeNull();
    expect($user->otp_expires_at)->not->toBeNull();
});
