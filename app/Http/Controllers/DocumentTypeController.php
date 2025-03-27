<?php

namespace App\Http\Controllers;

use App\Models\DocumentType;
use Illuminate\Http\Request;

class DocumentTypeController extends Controller
{
    //
    public function index(){
        return view('admin.document_type.index');
    }

    public function create(){


    }

    public function edit($id){

        $document_type = DocumentType::findOrFail($id);

        return view('admin.document_type.edit',compact('document_type'));


    }

    


}
