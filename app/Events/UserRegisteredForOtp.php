<?php

namespace App\Events;

use App\Models\User;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserRegisteredForOtp
{
    use Dispatchable, SerializesModels;

    public bool $otpSent = false;

    public function __construct(public User $user)
    {
    }
}
