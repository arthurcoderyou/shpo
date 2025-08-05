<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionWithAlertMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,  ...$rolesPermissions): Response
    {
        // return $next($request);


        $user = Auth::user();

        if (!$user) {
            Alert::error('Error', 'You must be logged in.');
            return redirect()->route('login');
        }

        $roles = [];
        $permissions = [];

        foreach ($rolesPermissions as $item) {
            if (str_starts_with($item, 'role:')) {
                $roles[] = substr($item, 5);
            } elseif (str_starts_with($item, 'permission:')) {
                $permissions[] = substr($item, 11);
            }
        }

        // Allow access if user has any of the roles or permissions
        if ($user->hasAnyRole($roles) || $user->hasAnyPermission($permissions) || $user->hasPermissionTo('system access global admin')) {
            return $next($request);
        }

        // Alert and redirect
        Alert::error('Error', 'You do not have permission to access this section.');

        $redirect = url()->previous() !== url()->current()
            ? redirect()->back()
            : redirect()->route('dashboard');

        return $redirect->withInput()->withErrors([
            'error' => 'Unauthorized Access',
        ]);

        
    }
}
