<?php

namespace App\Livewire\Admin\Reviewer;

use App\Models\User;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use App\Models\DocumentTypeReviewer;
use Illuminate\Support\Facades\Auth; 
use RealRashid\SweetAlert\Facades\Alert;

class ReviewerCreate extends Component
{

    public $user_id;
    public $order;
    public $status;


    public $users;

    public $document_types;
    public $reviewer_type = "document";

    public $document_type_id;


    public function mount(){
        // $this->users = User::get()->pluck('id','name')->toArray();

        $this->users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Reviewer');
        })->pluck('id', 'name')->toArray();

        $this->status = true;
        
        $this->document_types = DocumentType::all();


        // check the first document type
            // $this->document_type_id = DocumentType::first()->id ?? null;
            
            // document type adjustment
            $this->document_types = DocumentType::orderBy('order')->get();
                

            // check the first document type
            $this->document_type_id = DocumentType::first()->id ?? null;
            
            // check the get request if it has one 
            $this->document_type_id = request('document_type_id') ?? $this->document_type_id;
        // ./  document type adjustment
        
        // reviewer_type adjustment
            $this->reviewer_type = "document";
            
            // check the get request if it has reviewer_type 
            $this->reviewer_type = request('reviewer_type') ?? $this->reviewer_type;


            if($this->reviewer_type != "document"){
                $this->document_type_id = null;
            }
            
        // ./ reviewer_type adjustment

 

    }
 

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'user_id' => [
                'required',
                Rule::unique('reviewers', 'user_id')
                    ->where(function ($query) {
                    // Reviewer type is initial or final
                    if (in_array($this->reviewer_type, ['initial', 'final'])) {
                        return $query->where('reviewer_type', $this->reviewer_type);
                    }

                    // Reviewer type is document — must match both type and document_type_id
                    if ($this->reviewer_type === 'document') {
                        return $query
                            ->where('reviewer_type', 'document')
                            ->where('document_type_id', $this->document_type_id);
                    }

                    // fallback to avoid error
                    return $query->whereNull('id'); // guarantees no match
                }),

            ],
             
            'reviewer_type' => [
                'required',
            ],

            'document_type_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->reviewer_type) &&  $this->reviewer_type == "document") {
                        if (empty($value)) {
                            $fail('The document type field is required');
                        }
                    }
                },
            ],


            'order' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this document type',
            'document_type_id.required' => 'Document type is required',
        ]);






    }



    public function updatedReviewerType(){
        
        if ($this->reviewer_type !== 'document') {
            $this->document_type_id = null;
        }

        if ($this->reviewer_type == 'document') { 
            $this->document_type_id = DocumentType::first()->id ?? null;
        }
      
    }   

    /**
     * Handle save
     */
    public function save()
    {
        

        $this->validate([
            'user_id' => [
                'required',
                

                Rule::unique('reviewers', 'user_id')
                    ->where(function ($query) {
                        // Reviewer type is initial or final
                        if (in_array($this->reviewer_type, ['initial', 'final'])) {
                            return $query->where('reviewer_type', $this->reviewer_type);
                        }

                        // Reviewer type is document — must match both type and document_type_id
                        if ($this->reviewer_type === 'document') {
                            return $query
                                ->where('reviewer_type', 'document')
                                ->where('document_type_id', $this->document_type_id);
                        }

                        // fallback to avoid error
                        return $query->whereNull('id'); // guarantees no match
                    }),

            ],
            'reviewer_type' => [
                'required',
            ],

            'document_type_id' => [
                function ($attribute, $value, $fail) {
                    if (!empty($this->reviewer_type) &&  $this->reviewer_type == "document") {
                        if (empty($value)) {
                            $fail('The document type field is required');
                        }
                    }
                },
            ],
            'order' => [
                'required',
            ],
            'status' => [
                'required',
            ],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this document type',
            'document_type_id.required' => 'Document type is required',
        ]);

         
        if ($this->order === 'top') {
            if ($this->reviewer_type === 'document') {
                // Move document reviewers with the same document_type_id up by 1
                Reviewer::where('reviewer_type', 'document')
                    ->where('document_type_id', $this->document_type_id)
                    ->increment('order');
            } else {
                // Move initial or final reviewers of the same type up by 1 (no document_type_id needed)
                Reviewer::where('reviewer_type', $this->reviewer_type)
                    ->whereNull('document_type_id')
                    ->increment('order');
            }
        
            // Insert the new reviewer at the top (order = 1)
            Reviewer::create([
                'order' => 1,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'reviewer_type' => $this->reviewer_type,
                'document_type_id' => $this->reviewer_type === 'document' ? $this->document_type_id : null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        } elseif ($this->order === 'end') {
            $lastOrder = 0;
        
            if ($this->reviewer_type === 'document') {
                // Count document reviewers with the same document_type_id
                $lastOrder = Reviewer::where('reviewer_type', 'document')
                    ->where('document_type_id', $this->document_type_id)
                    ->count();
            } else {
                // Count initial or final reviewers of the same type (no document_type_id)
                $lastOrder = Reviewer::where('reviewer_type', $this->reviewer_type)
                    ->whereNull('document_type_id')
                    ->count();
            }
        
            // Insert the new reviewer at the last order + 1
            Reviewer::create([
                'order' => $lastOrder + 1,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'reviewer_type' => $this->reviewer_type,
                'document_type_id' => $this->reviewer_type === 'document' ? $this->document_type_id : null,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        }

         
 
     
        Alert::success('Success','Reviewer added successfully');
        return redirect()->route('reviewer.index',[
            'document_type_id' => $this->document_type_id,
            'reviewer_type' => $this->reviewer_type
        ]);
    }

     



    public function render()
    {
        return view('livewire.admin.reviewer.reviewer-create');
    }
}
