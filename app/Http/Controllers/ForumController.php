<?php

namespace App\Http\Controllers;

use App\Models\Forum;
use Illuminate\Http\Request;

class ForumController extends Controller
{
    
    public function index()
    {
        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('system access global admin');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification

        return view('admin.forum.index');
    }

    public function create(){
        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('system access global admin');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification

        return view('admin.forum.create');
    }


    public function edit($id){
        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('system access global admin');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification

        $forum = Forum::findOrFail($id);
        return view('admin.forum.edit',[
            'forum' => $forum,
        ]);


    }

 



}
