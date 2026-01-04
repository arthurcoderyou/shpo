<?php

namespace App\Http\Controllers;

use App\Models\User;
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


        // Check if at least one user has the "system access admin" permission
        $hasAdmin = User::whereHas('permissions', function ($q) {
            $q->whereIn('name', [
                'system access global admin',
                'system access admin',
            ]);
        })->get();

        if (! $hasAdmin) {
            return redirect()->route('user.index')->with('alert.error', 
                'Save cannot proceed because the system detected that there are no users with the required administrator permissions.'
            );
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
