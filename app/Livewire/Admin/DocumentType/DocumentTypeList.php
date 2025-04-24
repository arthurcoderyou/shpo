<?php

namespace App\Livewire\Admin\DocumentType;

use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use Livewire\WithPagination;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class DocumentTypeList extends Component
{
 
    use WithPagination;

    public $search = '';
    public $sort_by = '';
    public $record_count = 10;

    public $selected_records = [];
    public $selectAll = false;

    public $count = 0;

    public $file;

    public $lastOrder;


    protected $listeners = [
        'documentTypeCreated' => '$refresh',
        'documentTypeUpdated' => '$refresh',
        'documentTypeDeleted' => '$refresh',
    ];


    public function mount(){ 
    }

 

    // Method to delete selected records
    public function deleteSelected()
    {
        DocumentType::whereIn('id', $this->selected_records)->delete(); // Delete the selected records


        $this->selected_records = []; // Clear selected records


        // ActivityLog::create([
        //     'log_action' => "Document type list deleted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // Alert::success('Success','Selected Document types deleted successfully');
        // return redirect()->route('document_type.index');
    }

    // This method is called automatically when selected_records is updated
    public function updateSelectedCount()
    {
        // Update the count when checkboxes are checked or unchecked
        $this->count = count($this->selected_records);
    }

    public function toggleSelectAll()
    {
        if ($this->selectAll) {
            $this->selected_records = DocumentType::pluck('id')->toArray(); // Select all records
        } else {
            $this->selected_records = []; // Deselect all
        }

        $this->count = count($this->selected_records);
    }
 

    public function delete($id){
        $document_type = DocumentType::find($id);


        $document_type->delete();
 
        // ActivityLog::create([
        //     'log_action' => "Global Project reviewer '".$document_type->name."' on list deleted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // Alert::success('Success','Document type deleted successfully');
        // return redirect()->route('document_type.index');

    }

    public function getDocumentTypesProperty()
    {
        $query = DocumentType::select('document_types.*');

        if (!empty($this->search)) {
            $query = $query->where(function ($q) {
                $q->whereHas('creator', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
                })
                ->orWhereHas('updator', function ($q2) {
                    $q2->where('name', 'like', "%{$this->search}%")
                    ->orWhere('email', 'like', "%{$this->search}%");
                });
            });
        }

        switch ($this->sort_by) {
            case "Name A - Z":
                $query = $query->orderBy('name', 'ASC');
                break;
            case "Name Z - A":
                $query = $query->orderBy('name', 'DESC');
                break;
            case "Latest Added":
                $query = $query->orderBy('document_types.created_at', 'DESC');
                break;
            case "Oldest Added":
                $query = $query->orderBy('document_types.created_at', 'ASC');
                break;
            case "Latest Updated":
                $query = $query->orderBy('document_types.updated_at', 'DESC');
                break;
            case "Oldest Updated":
                $query = $query->orderBy('document_types.updated_at', 'ASC');
                break;
            default:
                $query = $query->orderBy('document_types.updated_at', 'DESC');
        }

        return $query->paginate($this->record_count);
    }

    public function render()
    {

         

 


        return view('livewire.admin.document-type.document-type-list',[
            'document_types' => $this->document_types 
        ]);
    }
    // public function render()
    // {
    //     return view('livewire.admin.document-type.document-type-list');
    // }
}
