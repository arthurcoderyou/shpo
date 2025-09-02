<?php

namespace App\Livewire\Admin\DocumentType;

use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DocumentTypeCreate extends Component
{


    protected $listeners = ['documentTypeCreated' => '$refresh'];
    
    public string $name;

    public function save(){
        $this->validate([
            'name' => 'required|unique:document_types,name',
        ]);

        $document_type = new DocumentType();
        $document_type->name = $this->name;
        $document_type->order = DocumentType::max('order') ? DocumentType::max('order') + 1 : 1;
        $document_type->created_by = Auth::user()->id; 
        $document_type->updated_by = Auth::user()->id; 
        $document_type->save();

        // ActivityLog::create([
        //     'log_action' => "Document type '".$document_type->name."' created",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        
        $this->reset(['name']);


        Alert::success('Success','Document type created successfully');
        return redirect()->route('document_type.index');
    }


    public function render()
    {
        return view('livewire.admin.document-type.document-type-create');
    }
}
