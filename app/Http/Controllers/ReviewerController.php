<?php

namespace App\Http\Controllers;

use App\Models\Reviewer;
use Illuminate\Http\Request;

class ReviewerController extends Controller
{
    // index
    public function index(){
        return view('admin.reviewer.index');
    }

    // // create
    // public function create(){
    //     return view('admin.reviewer.create');
    // }

    // // edit
    // public function edit($id){

    //     $reviewer = Reviewer::findOrFail($id);
    //     return view('admin.reviewer.edit',compact('reviewer'));
    // }

     
    


}
