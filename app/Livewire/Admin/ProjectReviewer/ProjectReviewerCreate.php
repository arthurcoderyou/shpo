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

    public function mount($id){
        $this->project = Project::find($id);

        // $this->users = User::get()->pluck('id','name')->toArray();

        $this->users = User::whereHas('roles', function ($query) {
            $query->where('name', 'Reviewer');
        })->pluck('id', 'name')->toArray();

        $this->status = true;
    }

    public function updated($fields){
         

        $this->validateOnly($fields,[
            'user_id' => [
                'required',
                Rule::unique('project_reviewers', 'user_id')
                    ->where(function ($query) {
                        return $query->where('project_id', $this->project->id);
                    }),
            ],
            'order' => ['required'],
            'status' => ['required'],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this project',
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
                        return $query->where('project_id', $this->project->id);
                    }),
            ],
            'order' => ['required'],
            'status' => ['required'],
        ], [
            'user_id.required' => 'User is required',
            'user_id.unique' => 'User is already added for this project',
        ]);


         
        if ($this->order === 'top') {
            ProjectReviewer::where('project_id', $this->project->id)->increment('order');

            // Insert the new reviewer at the top (order = 1)
            ProjectReviewer::create([ 
                'order' => 1,
                'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                'project_id' => $this->project->id,
                'user_id' => $this->user_id,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'review_status' => 'pending',
            ]);
        } elseif ($this->order === 'end') {
            // Get the last order number
            $lastOrder = ProjectReviewer::where('project_id',$this->project->id)->max('order') ?? 0;

            // Insert the new reviewer at the last order + 1
            ProjectReviewer::create([
                'order' => $lastOrder + 1, 
                'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                'project_id' => $this->project->id,
                'user_id' => $this->user_id,
                'created_by' => Auth::user()->id,
                'updated_by' => Auth::user()->id,
                'review_status' => 'pending',
            ]);
        } 

        // //save
        // Reviewer::create([
        //     'user_id' => $this->user_id,
        //     'order' => $this->order,
        //     'status' => $this->status,
        // ]);

        $user = User::find($this->user_id);

        ActivityLog::create([
            'log_action' => "Reviewer \"".$user->name."\" added ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project reviewer added successfully');
        return redirect()->route('project.reviewer.index',['project' => $this->project->id]);
    }


    public function render()
    {
        return view('livewire.admin.project-reviewer.project-reviewer-create');
    }
}
