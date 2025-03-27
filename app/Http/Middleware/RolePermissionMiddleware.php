<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RolePermissionMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$rolesPermissions): Response
    {

        $user = auth()->user();

        if (!$user) {
            abort(403, 'Unauthorized');
        }

        // Separate roles and permissions
        $roles = [];
        $permissions = [];

        foreach ($rolesPermissions as $item) {
            if (str_starts_with($item, 'role:')) {
                $roles[] = substr($item, 5);
            } elseif (str_starts_with($item, 'permission:')) {
                $permissions[] = substr($item, 11);
            }
        }

        // Check if the user has any of the specified roles or permissions
        if ($user->hasAnyRole($roles) || $user->hasAnyPermission($permissions)) {
            return $next($request);
        }

        abort(403, 'Unauthorized');


    }
}
