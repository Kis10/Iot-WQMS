<?php

namespace App\Listeners;

use App\Events\UserRegisteredForOtp;
use App\Models\User;
use App\Services\OtpService;

class SendOtpAfterRegistration
{
    public function __construct(private readonly OtpService $otpService)
    {
    }

    /**
     * Handle the event.
     */
    public function handle(UserRegisteredForOtp $event): void
    {
        $user = $event->user;

        if (! $user instanceof User || $user->hasVerifiedEmail()) {
            return;
        }

        $event->otpSent = $this->otpService->issueAndSend($user, 2);
    }
}
