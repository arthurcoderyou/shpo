<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ForcePasswordUpdateController extends Controller
{
    public function index(){
        return view('auth.force_password_update');

    }
}
