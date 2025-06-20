<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Events\UserActivity;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class UserController extends Controller
{
     /**
     * Display a listing of the resource.
     */
    public function index()
    {
        // Permission verification 
            $user = Auth::user();
            // Check if the user has the role "DSI God Admin" OR the permission "activity log list view"
            if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('user list view'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->back()->withInput()->withErrors(['error' => 'Unauthorized Access'])
                    ?: redirect()->route('dashboard');
            }
        // ./ Permission verification


        return view('admin.user.index');
    }


     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
        // Permission verification 
            $user = Auth::user(); 
            if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('user create'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->back()->withInput()->withErrors(['error' => 'Unauthorized Access'])
                    ?: redirect()->route('dashboard');
            }
        // ./ Permission verification


        return view('admin.user.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {

        // Permission verification 
            $user = Auth::user(); 
            if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('user edit'))) {
                Alert::error('Error', 'You do not have permission to access this section.');

                // If there is no previous URL, redirect to the dashboard
                return redirect()->back()->withInput()->withErrors(['error' => 'Unauthorized Access'])
                    ?: redirect()->route('dashboard');
            }
        // ./ Permission verification

        $user = User::findOrFail($id);

        return view('admin.user.edit',['user' => $user]);
    }



    public function userActivity(Request $request)
    {
        $userId = $request->input('userId');
        event(new UserActivity($userId));
    }


}
