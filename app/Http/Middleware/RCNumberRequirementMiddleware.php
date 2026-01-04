<?php

namespace App\Http\Middleware;

use Closure;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Symfony\Component\HttpFoundation\Response;

class RCNumberRequirementMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, ...$rolesPermissions): Response
    {

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
        if ($user->hasPermissionTo('system access admin') || $user->hasPermissionTo('system access global admin')) {
            return $next($request);
        }


        // Get the project from the route (project/{project})
        $project_id = $request->route('project'); // Uses route model binding


        $project = Project::find($project_id);

        // dd($project);

        // dd($project);
        // check if there is no project to check, just proceed
        if ( empty($project) ) {
            return $next($request);
        }

        // check if the project has rc number and let it proceed
        if ( !empty($project->rc_number) ) {
            return $next($request);
        }
 
        // Alert and redirect
        Alert::error('Error', 'You must first submit your project and wait for the evaluation of the admin.');

        $redirect = url()->previous() !== url()->current()
            ? redirect()->back()
            : redirect()->route('project.show',['project' => $project->id]);

        return $redirect->withInput()->withErrors([
            'error' => 'You must first submit your project and wait for the evaluation of the admin.',
        ]);



        // return $next($request);
    }
}
