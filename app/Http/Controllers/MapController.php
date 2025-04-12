<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class MapController extends Controller
{
    // index
    public function index(){
        return view('admin.map.index');


    }

    // open layer list
    public function open_layer_list(){
        return view('map.openlayer.index');
    }

    
}
