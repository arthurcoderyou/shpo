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

    public $project_submission_open_time;
    public $project_submission_close_time;
    public $project_submission_restrict_by_time;
    public $message_on_open_close_time;
    


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

            $this->project_submission_open_time = Carbon::createFromFormat('H:i:s', $project_timer->project_submission_open_time)->format('h:i A');
            $this->project_submission_close_time = Carbon::createFromFormat('H:i:s', $project_timer->project_submission_close_time)->format('h:i A');


            $this->message_on_open_close_time = $project_timer->message_on_open_close_time ;
            $this->project_submission_restrict_by_time = filter_var($project_timer->project_submission_restrict_by_time, FILTER_VALIDATE_BOOLEAN); 

            // dd($this->project_submission_open_time);
            $this->validateCloseTime();


        }

        

    }

    public function updatedProjectSubmissionOpenTime($value)
    {
        $this->validateCloseTime();
    }

    public function updatedProjectSubmissionCloseTime($value)
    {
        $this->validateCloseTime();
    }

    public function updatedProjectSubmissionRestrictByTime($value)
    {

        $this->project_submission_restrict_by_time = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        

        $this->validateCloseTime();
    }


    private function validateCloseTime()
    {
        $this->resetErrorBag(['project_submission_open_time', 'project_submission_close_time', 'message_on_open_close_time']);

        if (empty($this->project_submission_open_time)) {
            $this->addError('project_submission_open_time', 'Open time is required.');
        }

        if (empty($this->project_submission_close_time)) {
            $this->addError('project_submission_close_time', 'Close time is required.');
        }

        if (!empty($this->project_submission_open_time) && !empty($this->project_submission_close_time)) {
            try {
                $openTime = Carbon::createFromFormat('g:i A', $this->project_submission_open_time);
                $closeTime = Carbon::createFromFormat('g:i A', $this->project_submission_close_time);

                if ($closeTime < $openTime) {
                    $this->addError('project_submission_close_time', 'Close time must not be before open time.');
                } else {
                    if ($this->project_submission_restrict_by_time) {
                        $this->message_on_open_close_time = "Project submissions are strictly accepted between {$this->project_submission_open_time} and {$this->project_submission_close_time}. Submissions outside this time frame will not be accepted.";
                    } else {
                        $this->message_on_open_close_time = "Project submissions are accepted between {$this->project_submission_open_time} and {$this->project_submission_close_time}, but submissions outside this time frame may still be considered.";
                    }
                }
            } catch (\Exception $e) {
                if (!$this->getErrorBag()->has('project_submission_open_time')) {
                    $this->addError('project_submission_open_time', 'Invalid open time format. Please use HH:MM AM/PM.');
                }
                
                if (!$this->getErrorBag()->has('project_submission_close_time')) {
                    $this->addError('project_submission_close_time', 'Invalid close time format. Please use HH:MM AM/PM.');
                }
            }
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
            'project_submission_open_time' => [
                'required',
            ],
            'project_submission_close_time' => [
                'required',
            ],
            'project_submission_restrict_by_time' => [
                'required',
                'boolean',
            ],
            'message_on_open_close_time' => [
                'required', 
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
        $project_timer->project_submission_open_time =  Carbon::createFromFormat('g:i A', $this->project_submission_open_time)->format('H:i:s');
        $project_timer->project_submission_close_time =  Carbon::createFromFormat('g:i A', $this->project_submission_close_time)->format('H:i:s');
        $project_timer->project_submission_restrict_by_time = $this->project_submission_restrict_by_time;
        $project_timer->message_on_open_close_time = $this->message_on_open_close_time;
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
