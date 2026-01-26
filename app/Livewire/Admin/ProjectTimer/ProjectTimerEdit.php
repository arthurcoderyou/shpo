<?php

namespace App\Livewire\Admin\ProjectTimer;

use App\Helpers\SystemNotificationHelpers\ProjectTimerNotificationHelper;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Project;
use Livewire\Component;
use App\Models\ActiveDays;
use App\Models\ActivityLog;
use App\Models\ProjectTimer;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Events\ProjectTimer\ProjectTimerLogEvent;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Notifications\ProjectTimerUpdatedNotification;
use App\Notifications\ProjectTimerUpdatedNotificationDB;
use App\Helpers\ActivityLogHelpers\ProjectTimerLogHelper;

class ProjectTimerEdit extends Component
{
    
    public $submitter_response_duration = null; 
    public $submitter_response_duration_type = 'day';
    public $reviewer_response_duration = null;
    public $reviewer_response_duration_type = 'day';  

    public $project_submission_open_time;
    public $project_submission_close_time;
    public $project_submission_restrict_by_time;
    public $message_on_open_close_time;
    


    public $project_timer;

    public $DaysOfTheWeek;

    public $days;

    // protected $listeners = ['projectTimerUpdated' => '$refresh'];
    protected $listeners = ['projectTimerEvent' => 'loadData'];
    

    public function resetFilters(){

        $this->loadData();

        $this->days = [];
        $this->days = ActiveDays::all()->keyBy('id')->toArray();

    }


    public function mount(){
        // dd("Here");

        // // get the first record from the project_timers
        // $this->loadProjectTimer();

        // $this->loadDaysOfTheWeek(); /// loads the days of the week in realtime

        // $this->days = ActiveDays::all()->keyBy('id')->toArray();

        // if(!empty($this->project_timer)){

            
        //     // if there is an existing record 
        //     $this->submitter_response_duration_type = $this->project_timer->submitter_response_duration_type ;
        //     $this->submitter_response_duration = $this->project_timer->submitter_response_duration ;
        //     $this->reviewer_response_duration = $this->project_timer->reviewer_response_duration ;
        //     $this->reviewer_response_duration_type = $this->project_timer->reviewer_response_duration_type ;  

        //     $this->project_submission_open_time = Carbon::createFromFormat('H:i:s', $this->project_timer->project_submission_open_time)->format('h:i A');
        //     $this->project_submission_close_time = Carbon::createFromFormat('H:i:s', $this->project_timer->project_submission_close_time)->format('h:i A');


        //     $this->message_on_open_close_time = $this->project_timer->message_on_open_close_time ;
        //     $this->project_submission_restrict_by_time = filter_var($this->project_timer->project_submission_restrict_by_time, FILTER_VALIDATE_BOOLEAN); 

        //     // dd($this->project_submission_open_time);
        //     $this->validateCloseTime();


        // }

        $this->loadData();

    }



    public function loadData()
    {
        $this->loadProjectTimer();

        if (!empty($this->project_timer)) {
            $this->submitter_response_duration_type = $this->project_timer->submitter_response_duration_type;
            $this->submitter_response_duration = $this->project_timer->submitter_response_duration;
            $this->reviewer_response_duration = $this->project_timer->reviewer_response_duration;
            $this->reviewer_response_duration_type = $this->project_timer->reviewer_response_duration_type;

            $this->project_submission_open_time = Carbon::createFromFormat('H:i:s', $this->project_timer->project_submission_open_time)->format('h:i A');
            $this->project_submission_close_time = Carbon::createFromFormat('H:i:s', $this->project_timer->project_submission_close_time)->format('h:i A');

            $this->message_on_open_close_time = $this->project_timer->message_on_open_close_time;
            $this->project_submission_restrict_by_time = filter_var($this->project_timer->project_submission_restrict_by_time, FILTER_VALIDATE_BOOLEAN);

            $this->validateCloseTime();
        }

        $this->loadDaysOfTheWeek();
        $this->days = ActiveDays::all()->keyBy('id')->toArray();

        $this->getAllDaysActiveProperty();
    }


    public function loadProjectTimer(){
        $this->project_timer = ProjectTimer::first();
    }



    // loader of the days in the week in realtime
    public function loadDaysOfTheWeek()
    {
        $this->DaysOfTheWeek = ActiveDays::all();
    }


    public function getAllDaysActiveProperty()
    {
        return count(array_filter($this->days, function($day) {
            return $day['is_active'];
        })) === count($this->days);
    }

    public function selectAll($checked)
    {
        foreach ($this->days as $id => $day) {
            $this->days[$id]['is_active'] = $checked;
            // ActiveDays::find($id)->update(['is_active' => $checked]);
        }
    }

    public function updatedDays()
    {
        foreach ($this->days as $id => $day) {
            // ActiveDays::find($id)->update(['is_active' => $day['is_active']]);
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
                        $this->message_on_open_close_time = "Project submissions are accepted between {$this->project_submission_open_time} and {$this->project_submission_close_time}, but submissions outside this time frame will be considered and put on que. It will be automatically submitted on the next working day";
                    } else {
                        // $this->message_on_open_close_time = "Project submissions are checked between {$this->project_submission_open_time} and {$this->project_submission_close_time}".
                        $this->message_on_open_close_time = "Project submissions are checked between {$this->project_submission_open_time} and {$this->project_submission_close_time}";
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


        

        // dd("here");



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

        // dd($this->all());
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


        // ✅ Update ActiveDays only here
        foreach ($this->days as $id => $day) {
            $model = ActiveDays::find($id);
        
            if ($model && $model->is_active != $day['is_active']) {
                $model->update([
                    'is_active' => $day['is_active']
                ]);
            }
        }

        
 


        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = ProjectTimerLogHelper::getActivityMessage('updated', $authId);

            // get the route
            $route = ProjectTimerLogHelper::getRoute('updated');

            // log the event 
            event(new ProjectTimerLogEvent(
                $message ,
                $authId, 


            ));
    
            /** send system notifications to users */
                
                ProjectTimerNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications

        



 
        

        
        return redirect()->route('project_timer.index')
            ->with('alert.success',$message);
            ;
        

    }


    public function render()
    {
        return view('livewire.admin.project-timer.project-timer-edit');
    }
}
