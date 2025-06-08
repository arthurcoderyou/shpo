<?php

namespace App\Livewire\Admin\ProjectReviewer;

use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Validation\Rule;

class ProjectReviewerCreate extends Component
{

    public $user_id;
    public $order;
    public $status;


    public $users;
    public $project;


    public $project_documents; 

    public $project_document_id;

    public $reviewer_type = "initial";


    public function mount($id){
        $this->project = Project::find($id);

        // $this->users = User::get()->pluck('id','name')->toArray();

        $this->users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Reviewer');
        })->pluck('id', 'name')->toArray();

        $this->status = true;

        // document type adjustment
        $this->project_documents = $this->project->project_documents;

        // check the first document type
            $this->project_document_id = $this->project->project_documents->first()->id ?? null;
                

                
            // check the get request if it has one 
            $this->project_document_id = request('project_document_id') ?? $this->project_document_id;
        // ./  document type adjustment
        
        // reviewer_type adjustment
            $this->reviewer_type = "initial";
            
            // check the get request if it has reviewer_type 
            $this->reviewer_type = request('reviewer_type') ?? $this->reviewer_type;


            if($this->reviewer_type != "document"){
                $this->project_document_id = null;
            }
            
        // ./ reviewer_type adjustment





    }


    public function updatedReviewerType(){
        
        if ($this->reviewer_type !== 'document') {
            $this->project_document_id = null;
        }

        if ($this->reviewer_type == 'document') { 
            $this->project_document_id = $this->project->project_documents->first()->id ?? null;
        }
      
    }   

    

    public function updated($fields)
    {
        $this->validateOnly($fields, [
            'user_id' => [
                'required',
                Rule::unique('project_reviewers', 'user_id')
                    ->where(function ($query) {
                        $query->where('project_id', $this->project->id);

                        // If reviewer_type is 'document', also consider project_document_id
                        if ($this->reviewer_type === 'document' && $this->project_document_id) {
                            $query->where('reviewer_type', 'document')
                                ->where('project_document_id', $this->project_document_id);
                        } else {
                            $query->where('reviewer_type', $this->reviewer_type)
                                ->whereNull('project_document_id');
                        }
                    }),
            ],
            'order' => ['required'],
            'status' => ['required'],
            'reviewer_type' => ['required'],
            'project_document_id' => [
                Rule::requiredIf(fn () => $this->reviewer_type === 'document'),
            ],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this project with the same type and document',
            'reviewer_type.required' => 'Reviewer type is required',
            'project_document_id.required' => 'Project document is required when reviewer type is document',
        ]);
    }


    /**
     * Handle an incoming registration request.
     */
    public function save()
    {
        $this->validate([
            'user_id' => [
                'required',
                Rule::unique('project_reviewers', 'user_id')
                    ->where(function ($query) {
                        $query->where('project_id', $this->project->id);

                        // If reviewer_type is 'document', also consider project_document_id
                        if ($this->reviewer_type === 'document' && $this->project_document_id) {
                            $query->where('reviewer_type', 'document')
                                ->where('project_document_id', $this->project_document_id);
                        } else {
                            $query->where('reviewer_type', $this->reviewer_type);
                        }
                    }),
            ],
            'order' => ['required'],
            'status' => ['required'],
            'reviewer_type' => ['required'],
            'project_document_id' => [
                Rule::requiredIf(fn () => $this->reviewer_type === 'document'),
            ],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this project with the same type and document',
            'reviewer_type.required' => 'Reviewer type is required',
            'project_document_id.required' => 'Project document is required when reviewer type is document',
        ]);


         
        if ($this->order === 'top') {
            if ($this->reviewer_type === 'document') {
                // Move document reviewers with the same document_type_id up by 1
                ProjectReviewer::where('reviewer_type', 'document')
                    ->where('project_id', $this->project->id)
                    ->where('project_document_id', $this->project_document_id)
                    ->increment('order');
            } else {
                // Move initial or final reviewers of the same type up by 1 (no document_type_id needed)
                ProjectReviewer::where('reviewer_type', $this->reviewer_type)
                    ->where('project_id', $this->project->id)
                    ->whereNull('project_document_id')
                    ->increment('order');
            }

            // Insert the new reviewer at the top (order = 1)
            ProjectReviewer::create([ 
                'order' => 1,
                'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                'project_id' => $this->project->id,
                'user_id' => $this->user_id,
                'project_document_id' => $this->project_document_id,
                'reviewer_type' => $this->reviewer_type,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'review_status' => 'pending',
            ]);


            $this->resetOrder($this->project_document_id, $this->reviewer_type);

        } elseif ($this->order === 'end') {



             
            $lastOrder = 0;
        
            if ($this->reviewer_type === 'document') {
                // Count document reviewers with the same document_type_id
                $lastOrder = ProjectReviewer::where('reviewer_type', 'document')
                    ->where('project_id', $this->project->id)
                    ->where('project_document_id', $this->project_document_id)
                    ->count();
            } else {
                // Count initial or final reviewers of the same type (no document_type_id)
                $lastOrder = ProjectReviewer::where('reviewer_type', $this->reviewer_type)
                    ->where('project_id', $this->project->id)
                    ->whereNull('project_document_id')
                    ->count();
            }



            // Insert the new reviewer at the last order + 1
            ProjectReviewer::create([
                'order' => $lastOrder + 1, 
                'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                'project_id' => $this->project->id,
                'project_document_id' => $this->project_document_id,
                'reviewer_type' => $this->reviewer_type,
                'user_id' => $this->user_id,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'review_status' => 'pending',
            ]);


            $this->resetOrder($this->project_document_id, $this->reviewer_type);

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

        Alert::success('Success','Project reviewer added successfully');
        // return redirect()->route('project.reviewer.index',['project' => $this->project->id]);

        // Alert::success('Success','Reviewer added successfully');
        return redirect()->route('project.reviewer.index',[
            'project' => $this->project->id,
            'project_document_id' => $this->project_document_id,
            'reviewer_type' => $this->reviewer_type
        ]);

    }


    public function resetOrder($project_document_id = null, $reviewer_type)
    {
        $reviewers = ProjectReviewer::where('project_id',$this->project->id)
            ->where('reviewer_type', $reviewer_type)
            ->when($reviewer_type === 'document', function ($query) use ($project_document_id) {
                return $query->where('project_document_id', $project_document_id);
            }, function ($query) {
                return $query->whereNull('project_document_id');
            })
            ->orderBy('order', 'ASC')
            ->get();
    
        foreach ($reviewers as $index => $reviewer) {
            $reviewer->order = $index + 1;
            $reviewer->save();
        }
    }


    public function render()
    {
        return view('livewire.admin.project-reviewer.project-reviewer-create');
    }
}
