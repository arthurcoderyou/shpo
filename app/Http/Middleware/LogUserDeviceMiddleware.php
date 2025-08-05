<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response; 
use App\Models\UserDeviceLog;
use Illuminate\Support\Facades\Auth;
use Jenssegers\Agent\Agent;

class LogUserDeviceMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {

        if (Auth::check()) {
            $user = Auth::user();
            $agent = new Agent();

            $data = [
                'user_id' => $user->id,
                'ip_address' => $request->ip(),
                'browser' => $agent->browser(),
                'device' => $agent->device(),
                'platform' => $agent->platform(),
                'user_agent' => $request->header('User-Agent'),
                'location' => request()->header('X-Forwarded-For') ?? request()->ip(),
            ];

            // Check if the device is already logged
            $deviceLog = UserDeviceLog::firstOrCreate($data);

            // If the user has the permission system access admin or system access global admin, skip 2FA
            if ($user->can('system access admin') ) {
                session()->put('2fa_verified', true);
                return $next($request);
            }

             // If the user has the role Admin or system access global admin, skip 2FA
             if ($user->can('system access global admin') ) {

                session()->put('2fa_verified', true);
                return $next($request);
            }


            // ✅ **Check if 2FA verification is required**
            if (!$deviceLog->trusted && !session()->has('2fa_verified')) {
                return redirect()->route('2fa.verify');
            }

            // ✅ **If device is trusted, store 2FA verification in session**
            if ($deviceLog->trusted) {
                session()->put('2fa_verified', true);
            } 

        }

        
        return $next($request);
    }
}
