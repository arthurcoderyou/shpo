<?php

namespace App\Http\Controllers;

use App\Models\Setting;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    // index
    public function index(){
        return view('admin.setting.index');
    }

   
    // edit
    public function edit($id){
        $setting = Setting::findOrFail($id);

        return view('admin.setting.edit',[
            'setting' => $setting 
        ]);
    }
 

    // manager
    public function manager(){
        return view('admin.setting.manager');
    }

    // manager_edit
    public function manager_edit($id){
        $setting = Setting::findOrFail($id);

        return view('admin.setting.manager_edit',[
            'setting' => $setting,
        ]);
    }

}
