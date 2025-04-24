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

    public $document_type_id;


    public function mount(){
        // $this->users = User::get()->pluck('id','name')->toArray();

        $this->users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Reviewer');
        })->pluck('id', 'name')->toArray();

        $this->status = true;
        
        $this->document_types = DocumentType::all();


        // check the first document type
        $this->document_type_id = DocumentType::first()->id ?? null;
        
        // check the get request if it has one 
        $this->document_type_id = request('document_type_id') ?? $this->document_type_id;




    }

    // public function updated($fields)
    // {
    //     $reviewer = Reviewer::where('user_id', $this->user_id)->first();

    //     $this->validateOnly($fields, [
    //         'user_id' => [
    //             'required',
    //             function ($attribute, $value, $fail) use ($reviewer) {
    //                 if ($reviewer) {
    //                     $exists = DocumentTypeReviewer::where('document_type_id', $this->document_type_id)
    //                         ->where('reviewer_id', $reviewer->id)
    //                         ->exists();

    //                     if ($exists) {
    //                         $fail('User is already assigned as a reviewer for this document type.');
    //                     }
    //                 }
    //             },
    //         ],
    //         'document_type_id' => 'required',
    //         'order' => [
    //             'required',
    //         ],
    //         'status' => [
    //             'required',
    //         ],
    //     ], [
    //         'user_id.required' => 'User is required',
    //         'document_type_id.required' => 'Document type is required',
    //     ]);
    // }


    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'user_id' => [
                'required',
                Rule::unique('reviewers', 'user_id')
                    ->where(function ($query) {
                        return $query->where('document_type_id', $this->document_type_id);
                    }),
            ],
            'document_type_id' => [
                'required',
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
                        return $query->where('document_type_id', $this->document_type_id);
                    }),
            ],
            'document_type_id' => [
                'required',
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
            // Move all existing reviewers up by 1
            Reviewer::where('document_type_id',$this->document_type_id)->increment('order');

            // Insert the new reviewer at the top (order = 1)
            Reviewer::create([
                'order' => 1,
                'status' => $this->status,
                'document_type_id' => $this->document_type_id,
                'user_id' => $this->user_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        } elseif ($this->order === 'end') {
            // Get the last order number
            $lastOrder = 0;
            $lastOrder = Reviewer::where('document_type_id',$this->document_type_id)->count() ?? 0;

            // Insert the new reviewer at the last order + 1
            Reviewer::create([
                'order' => $lastOrder + 1,
                'status' => $this->status,
                'user_id' => $this->user_id,
                'document_type_id' => $this->document_type_id,
                'created_by' => auth()->id(),
                'updated_by' => auth()->id(),
            ]);
        } 

        // //save
        // Reviewer::create([
        //     'user_id' => $this->user_id,
        //     'order' => $this->order,
        //     'status' => $this->status,
        // ]);

        $user = User::find($this->user_id);

        // ActivityLog::create([
        //     'log_action' => "Reviewer \"".$user->name."\" added ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        Alert::success('Success','Reviewer added successfully');
        return redirect()->route('reviewer.index',[
            'document_type_id' => $this->document_type_id
        ]);
    }

     

    // public function save()
    // {
    //     // Check if the user is already in Reviewer table
    //     $this->validate([
    //         'user_id' => [
    //             'required',
    //             // Rule::unique('reviewers', 'user_id'),
    //         ],
    //         'document_type_id' => 'required',
    //         'status' => 'required',
    //         'order' => 'required',
    //     ], [
    //         'user_id.required' => 'User is required',
    //         'user_id.unique' => 'User is already added globally',
    //         'document_type_id.required' => 'Document type is required',
    //     ]);

    //     // Create the reviewer (globally)
    //     $reviewer = Reviewer::create([
    //         'user_id' => $this->user_id,
    //         'status' => $this->status,
    //         'order' => 0, // temp placeholder
    //         'created_by' => auth()->id(),
    //         'updated_by' => auth()->id(),
    //     ]);

    //     // Now check if document_type_id exists and needs relationship
    //     if ($this->document_type_id) {
    //         // Check if this reviewer already existsx  for the document type
    //         $alreadyExists = DocumentTypeReviewer::where('document_type_id', $this->document_type_id)
    //             ->where('reviewer_id', $reviewer->id)
    //             ->exists();

    //         if ($alreadyExists) {
    //             Alert::error('Error', 'User is already added for this document type.');
    //             return;
    //         }

    //         // Manage order
    //         if ($this->order === 'top') {
    //             DocumentTypeReviewer::where('document_type_id', $this->document_type_id)->increment('review_order');

    //             DocumentTypeReviewer::create([
    //                 'document_type_id' => $this->document_type_id,
    //                 'reviewer_id' => $reviewer->id,
    //                 'review_order' => 1,
    //             ]);
    //         } elseif ($this->order === 'end') {
    //             $lastOrder = DocumentTypeReviewer::where('document_type_id', $this->document_type_id)->max('review_order') ?? 0;

    //             DocumentTypeReviewer::create([
    //                 'document_type_id' => $this->document_type_id,
    //                 'reviewer_id' => $reviewer->id,
    //                 'review_order' => $lastOrder + 1,
    //             ]);
    //         }
    //     } else {
    //         // Global order management
    //         if ($this->order === 'top') {
    //             Reviewer::query()->increment('order');
    //             $reviewer->update(['order' => 1]);
    //         } elseif ($this->order === 'end') {
    //             $lastOrder = Reviewer::max('order') ?? 0;
    //             $reviewer->update(['order' => $lastOrder + 1]);
    //         }
    //     }

    //     ActivityLog::create([
    //         'log_action' => "Reviewer \"{$reviewer->user->name}\" added",
    //         'log_username' => Auth::user()->name,
    //         'created_by' => Auth::id(),
    //     ]);

    //     Alert::success('Success', 'Reviewer added successfully');
    //     return redirect()->route('reviewer.index',[
    //         'document_type_id' => $this->document_type_id
    //     ]);
    // }





    public function render()
    {
        return view('livewire.admin.reviewer.reviewer-create');
    }
}
