<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class LogUserLogout
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
    public function handle(Logout $event): void
    {
        if ($event->user) {
            $user = $event->user;
            
            // Find the last login record without a logout time
            $activity = \App\Models\LoginActivity::where('user_id', $user->id)
                ->whereNull('logout_at')
                ->latest('login_at')
                ->first();

            if ($activity) {
                $now = now();
                $activity->update([
                    'logout_at' => $now,
                    'duration_minutes' => (int) $activity->login_at->diffInMinutes($now),
                ]);
            }
            
            // Clear Online Status
            cache()->forget('user-is-online-' . $user->id);
        }
    }
}
