<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class ReviewController extends Controller
{
    // index
    public function index(){

        return view('admin.review.index');
    }


}
