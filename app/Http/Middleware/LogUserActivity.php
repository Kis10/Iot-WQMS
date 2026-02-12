<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class LogUserActivity
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check()) {
            $url = $request->path();
            $method = $request->method();

            // Ignore spammy routes (Ajax polling etc)
            if (!str_contains($url, 'chart-data') && 
                !str_contains($url, 'ably') && 
                !str_contains($url, 'refresh') &&
                !str_contains($url, 'livewire')) { // Livewire polling if used
                
                $activity = "Visited " . $url;
                if ($method === 'POST') {
                    $activity = "Submitted form or Action on " . $url;
                }
                if ($method === 'DELETE') {
                    $activity = "Deleted item on " . $url;
                }

                \App\Models\UserActivity::create([
                    'user_id' => Auth::id(),
                    'activity' => $activity,
                    'url' => $request->fullUrl(),
                    'method' => $method,
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent(),
                ]);
            }
        }

        return $next($request);
    }
}
