<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Spatie\Permission\Models\Role;

class RoleController extends Controller
{
    // index
    public function index(){
        return view('admin.role.index');
    }

    // create
    public function create(){
        return view('admin.role.create');
    }

    // edit
    public function edit($id){

        $role = Role::findOrFail($id);
        return view('admin.role.edit',compact('role'));
    }

    // add_permissions
    public function add_permissions($id){
        $role = Role::findOrFail($id);
        return view('admin.role.add_permissions',compact('role'));

    }




}
