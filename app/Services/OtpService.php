<?php

namespace App\Services;

use App\Mail\OTPMail;
use App\Models\User;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Throwable;

class OtpService
{
    /**
     * Generate a fresh OTP code, persist it, and send it immediately by email.
     */
    public function issueAndSend(User $user, int $ttlMinutes = 2): bool
    {
        $otp = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);

        $user->otp_code = $otp;
        $user->otp_expires_at = now()->addMinutes($ttlMinutes);
        $user->save();

        try {
            Mail::mailer(config('mail.otp_mailer', 'smtp'))
                ->to($user->email)
                ->send(new OTPMail($otp));

            return true;
        } catch (Throwable $e) {
            Log::error('OTP email delivery failed.', [
                'user_id' => $user->id,
                'email' => $user->email,
                'message' => $e->getMessage(),
            ]);

            report($e);

            return false;
        }
    }
}
