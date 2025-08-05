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
 
        

        // Permission verification 
              
            // $auth = authorizeWithAdminOverrideForController('activity log list view');

            // if ($auth !== true) {
            //     return $auth; // This returns a redirect to dashboard
            // }
 
        // ./ Permission verification



        return view('activity_logs.index');
    }

    // edit

    // delete
}
