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
         


        return view('admin.user.index');
    }


     /**
     * Show the form for creating a new resource.
     */
    public function create()
    {
         
        
         


        return view('admin.user.create');
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(int $id)
    {

       


        $user = User::findOrFail($id);

        return view('admin.user.edit',['user' => $user]);
    }


    /**
     * Show the form for editing the user role
     */
    public function edit_role(int $id)
    { 
        $user = User::findOrFail($id);

        return view('admin.user.edit_role',['user' => $user]);
    }



    public function userActivity(Request $request)
    {
        $userId = $request->input('userId');
        event(new UserActivity($userId));
    }


}
