<?php

namespace App\Livewire\Admin\DocumentType;

use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DocumentTypeEdit extends Component
{
    public $document_type_id;

    public string $name;

    public function mount($id){
        $document_type = DocumentType::findOrFail($id);
        $this->name = $document_type->name;
        $this->document_type_id = $document_type->id;

    }

    public function save(){
        $this->validate([
            'name' => 'required',
        ]);




        $document_type = DocumentType::findOrFail($this->document_type_id);
        $document_type->name = $this->name;
        // $document_type->created_by = Auth::user()->id; 
        $document_type->updated_at = now();
        $document_type->updated_by = Auth::user()->id; 
        $document_type->save();

        ActivityLog::create([
            'log_action' => "Document type '".$document_type->name."' updated",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Document type updated successfully');
        return redirect()->route('document_type.index');
    }


    public function render()
    {
        // return view('livewire.admin.document-type.document-type-create');
        return view('livewire.admin.document-type.document-type-edit');
    }

    // public function render()
    // {
    //     return view('livewire.admin.document-type.document-type-edit');
    // }
}
