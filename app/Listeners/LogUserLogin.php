<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogin
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(Login $event): void
    {
        $user = $event->user;
        $ip = request()->ip();
        $userAgent = request()->userAgent();

        \App\Models\LoginActivity::create([
            'user_id' => $user->id,
            'ip_address' => $ip,
            'user_agent' => $userAgent,
            'login_at' => now(),
        ]);
        
        // Also update Last Seen (Online Status)
        // We can use Cache for this
        cache(['user-is-online-' . $user->id => true], now()->addMinutes(5));
    }
}
