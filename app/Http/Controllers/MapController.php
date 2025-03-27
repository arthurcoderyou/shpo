<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    // index
    public function index(){
        return view('admin.map.index');


    }
    
}
