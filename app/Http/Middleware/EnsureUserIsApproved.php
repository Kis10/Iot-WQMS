<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class EnsureUserIsApproved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (Auth::check() && !Auth::user()->isApproved()) {
            
            // Allow access to logout and the 'waiting for approval' page itself
            if ($request->routeIs('approval.wait') || $request->routeIs('logout') || $request->routeIs('approval.check')) {
                return $next($request);
            }

            // Redirect to "Waiting for Approval" page
            return redirect()->route('approval.wait');
        }

        return $next($request);
    }
}
