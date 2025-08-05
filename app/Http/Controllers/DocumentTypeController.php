<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DocumentTypeController extends Controller
{
    //
    public function index(){

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('document type list view');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification

        
        return view('admin.document_type.index');
    }

    public function create(){

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('document type create');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification
    }

    public function edit($id){

        // // Permission verification 
              
        //     $auth = authorizeWithAdminOverrideForController('document type edit');

        //     if ($auth !== true) {
        //         return $auth; // This returns a redirect to dashboard
        //     }
 
        // // ./ Permission verification


        $document_type = DocumentType::findOrFail($id);

        return view('admin.document_type.edit',compact('document_type'));


    }

    


}
