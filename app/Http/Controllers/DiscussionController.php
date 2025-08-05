<?php

namespace App\Http\Controllers;

use App\Models\Discussion;
use Illuminate\Http\Request;

class DiscussionController extends Controller
{
    public function index()
    {
        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('project discussion list view');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification



        // Code to display a list of discussions
        return view('admin.discussion.index');
    }

    public function create()
    {

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('project discussion create');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification


        // Code to show the form for creating a new discussion
        return view('admin.discussion.create');



    }
 
    public function edit($id)
    {

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('project discussion edit');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification


        // Code to show the form for editing a specific discussion

        $discussion = Discussion::findOrFail($id);
        return view('admin.discussion.edit', compact('discussion'));

    }

    
}
