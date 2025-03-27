<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ActivityLogController extends Controller
{
    // index
    public function index(){

        $user = Auth::user();
        // Check if the user has the role "DSI God Admin" OR the permission "activity log list view"
        if (!$user || (!$user->hasRole('DSI God Admin') && !$user->hasPermissionTo('activity log list view'))) {
            Alert::error('Error', 'You do not have permission to access this section.');

            // If there is no previous URL, redirect to the dashboard
            return redirect()->back()->withInput()->withErrors(['error' => 'Unauthorized Access'])
                   ?: redirect()->route('dashboard');
        }
 


        return view('activity_logs.index');
    }

    // edit

    // delete
}
