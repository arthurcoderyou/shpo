<?php

namespace App\Livewire\Admin\ProjectTimer;

use Carbon\Carbon;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActivityLog;
use App\Models\ProjectTimer;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;

class ProjectTimerEdit extends Component
{
    public $submitter_response_duration_type = null;
    public $submitter_response_duration = null; 
    public $reviewer_response_duration = null;
    public $reviewer_response_duration_type = null;  

    public $project_submission_open_time = null;
    public $project_submission_close_time = null;
    public $project_submission_restrict_by_time = null;
    public $message_on_open_close_time = null;
    


    public $project_timer;



    public function mount(){


        //get the first record from the project_timers
        $project_timer = ProjectTimer::first();
        

        if(!empty($project_timer)){

            
            // if there is an existing record
            $this->project_timer = $project_timer;
            $this->submitter_response_duration_type = $project_timer->submitter_response_duration_type ;
            $this->submitter_response_duration = $project_timer->submitter_response_duration ;
            $this->reviewer_response_duration = $project_timer->reviewer_response_duration ;
            $this->reviewer_response_duration_type = $project_timer->reviewer_response_duration_type ;  

            $this->project_submission_open_time = $project_timer->project_submission_open_time ;
            $this->project_submission_close_time = $project_timer->project_submission_close_time ;
            $this->message_on_open_close_time = $project_timer->message_on_open_close_time ;
            $this->project_submission_restrict_by_time = $project_timer->project_submission_restrict_by_time ;  
            
        }



    }



    public function apply_to_all(){

 
        // Get all projects where status is not 'approved'
        $projects = Project::where('status', '!=', 'approved')->get();

        //get the project_timer record
        $project_timer = ProjectTimer::first();


        if(empty($project_timer)){
            Alert::error('Danger','Project timer had not been updated. Please update project timers first');
            return redirect()->route('project_timer.index');
        }


        foreach ($projects as $project) {
             
            $project->submitter_response_duration_type = $project_timer->submitter_response_duration_type;
            $project->submitter_response_duration = $project_timer->submitter_response_duration;
            $project->submitter_due_date = Project::calculateDueDate( $project->updated_at, $project_timer->submitter_response_duration_type, $project_timer->submitter_response_duration);
            $project->reviewer_response_duration = $project_timer->reviewer_response_duration;
            $project->reviewer_response_duration_type = $project_timer->reviewer_response_duration_type;
            $project->reviewer_due_date = Project::calculateDueDate( $project->updated_at, $project_timer->reviewer_response_duration_type, $project_timer->reviewer_response_duration);
            // $project->updated_at = now();
            $project->updated_by = Auth::user()->id;
            $project->save();
        }

        Alert::success('Success','Project timer applied to all successfully');
        return redirect()->route('project_timer.index');

    }



    




    public function save(){
        $this->validate([
            'submitter_response_duration' => [
                'required',
                'integer', 
            ],
            'submitter_response_duration_type' => [
                'required',
                'in:day,week,month'
            ],
            'reviewer_response_duration' => [
                'required',
                'integer', 
            ],
            'reviewer_response_duration_type' => [
                'required',
                'in:day,week,month'
            ],

        ]);

        $project_timer = ProjectTimer::first();


        if(empty( $project_timer)){
            $project_timer = new ProjectTimer();
            $project_timer->created_by = Auth::user()->id;
        } 
        //save
        $project_timer->submitter_response_duration = $this->submitter_response_duration;
        $project_timer->submitter_response_duration_type = $this->submitter_response_duration_type;
        $project_timer->reviewer_response_duration = $this->reviewer_response_duration;
        $project_timer->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        $project_timer->updated_by = Auth::user()->id;
        
        $project_timer->save();

        ActivityLog::create([
            'log_action' => "Project timer  updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project timer updated successfully');
        return redirect()->route('project_timer.index');
        

    }


    public function render()
    {
        return view('livewire.admin.project-timer.project-timer-edit');
    }
}
