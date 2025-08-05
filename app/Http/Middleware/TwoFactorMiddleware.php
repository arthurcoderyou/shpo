<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class TwoFactorMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // Check if the user is authenticated
        if (Auth::check()) {

            $user = Auth::user();

            // If the user has the permission system access admin or system access global admin, skip 2FA
            if ($user->can('system access admin') ) {
                return $next($request);
            }

             // If the user has the role system access admin or system access global admin, skip 2FA
             if ($user->can('system access global admin') ) {
                return $next($request);
            }
 
            // If the user does not have 2FA verified, redirect to verification page
            if (!session()->has('2fa_verified')) {
                return redirect()->route('2fa.verify');
            }
        }

        return $next($request);

    }
}
