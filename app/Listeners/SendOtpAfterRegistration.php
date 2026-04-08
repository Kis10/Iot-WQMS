<?php

namespace App\Listeners;

use App\Events\UserRegisteredForOtp;
use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Support\Facades\Mail;

class SendOtpAfterRegistration
{
    /**
     * Handle the event.
     */
    public function handle(UserRegisteredForOtp $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || $user->hasVerifiedEmail()) {
            return;
        }

        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes(2);
        $user->save();

        Mail::to($user->email)->send(new OTPMail($otp));
    }
}
