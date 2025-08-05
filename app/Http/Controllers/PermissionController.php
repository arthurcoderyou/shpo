<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;

class PermissionController extends Controller
{
    // index
    public function index(){
        

        return view('admin.permission.index');
    }

    // create
    public function create(){

         

        return view('admin.permission.create');
    }

    // edit
    public function edit($id){

        

        $permission = Permission::findOrFail($id);
        return view('admin.permission.edit',compact('permission'));
    }

}
