<?php

namespace App\Http\Controllers;

use App\Models\Reviewer;
use App\Models\DocumentType;
use Illuminate\Http\Request;
use RealRashid\SweetAlert\Facades\Alert;

class ReviewerController extends Controller
{
    // index
    public function index(){

        // dd(DocumentType::count());

        if(DocumentType::count() == 0){
            Alert::error('Error', "You must add document types first");
            return redirect()->route('document_type.index');
        }

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
