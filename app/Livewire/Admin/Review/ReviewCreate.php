<?php

namespace App\Livewire\Admin\Review;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;

use App\Models\ActivityLog;
use Livewire\WithFileUploads;
use App\Helpers\ProjectHelper;
use Illuminate\Validation\Rule;
use App\Models\ReviewAttachments;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;

class ReviewCreate extends Component
{

    use WithFileUploads;

    public $project_review;
    public $project;

    public $attachments = []; // Initialize with one phone field 

    public $shpo_number;
    public $submitter_response_duration;
    public $submitter_response_duration_type;
    public $submitter_due_date;


    public $reviewer_response_duration;
    public $reviewer_response_duration_type;  

    public $reviewer_due_date;

    
    public function mount($id){
        $this->project = Project::findOrFail($id);

        // Generate the project number
        if(empty($this->project->shpo_number)){ 
            $this->shpo_number = Project::generateProjectNumber(rand(10, 99));
        }

        $this->submitter_due_date = $this->project->submitter_due_date;
        $this->submitter_response_duration_type = $this->project->submitter_response_duration_type;
        $this->submitter_response_duration = $this->project->submitter_response_duration;

        $this->reviewer_due_date = $this->project->reviewer_due_date;
        $this->reviewer_response_duration = $this->project->reviewer_response_duration;
        $this->reviewer_response_duration_type = $this->project->reviewer_response_duration_type; 



    }


    public function updated(){
        $this->updateDueDate();

    }
    public function updateDueDate(){
              
        $this->submitter_due_date = Project::calculateDueDate($this->project->updated_at,$this->submitter_response_duration_type, $this->submitter_response_duration );
        $this->reviewer_due_date = Project::calculateDueDate($this->project->updated_at,$this->reviewer_response_duration_type, $this->reviewer_response_duration );
    }


    public function update_project(){

        $this->validate([
            'shpo_number' => [
                'required',
                'string',
                Rule::unique('projects', 'shpo_number'), // Ensure shpo_number is unique
            ]
        ],[
            'The shpo number has already been taken. Please enter other combinations of shpo number '
        ]);

        $project = Project::findOrFail($this->project->id);

        $project->shpo_number = $this->shpo_number;
        


        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        // after updating the project, update the due date timers
        $project->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;  
        // $project->submitter_due_date = Project::calculateDueDate(now(),$project->submitter_response_duration_type, $project->submitter_response_duration );
        $project->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
 
        $project->save();

        ActivityLog::create([
            'log_action' => "Project SHPO number on \"".$project->name."\" updated ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
            'project_id' => $project->id,
        ]);

        Alert::success('Success', "Project SHPO number on \"".$project->name."\" updated. You can now submit a review");
        return redirect()->route('project.review',['project' => $this->project->id]);

    }



    // saving and submission of review 
    public function save($status){

        // get the project reviewer instance of the current user reviewer
        $project_reviewer = $this->project->getReview();

        // dd($project_reviewer);


        // dd($status);


        $this->validate([
            'project_review' => [
                'required',
            ]
        ]);

 

        //add to the review model
        $review = new Review();
        $review->project_review = $this->project_review;
        $review->project_id = $this->project->id;
        $review->project_status = $this->project->status;
        $review->project_document_id = $project_reviewer->project_document_id; // the project reviewer reviewing project document will be associated to its review
        $review->reviewer_id = Auth::user()->id;

        /** Update the review time */

            // Ensure updated_at is after created_at
            if ($this->project->updated_at && now()->greaterThan($this->project->updated_at)) {
                // Calculate time difference in hours
                // $review->review_time_hours = $this->project->updated_at->diffInHours(now()); 
                $review->review_time_hours = $this->project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
            }
 
        /** ./ Update the review time */


        $review->review_status = $status;
        $review->created_by = Auth::user()->id;
        $review->updated_by = Auth::user()->id;
        $review->created_at = now();
        $review->updated_at = now();
        $review->save();


        if (!empty($this->attachments)) {
            foreach ($this->attachments as $file) {
        
                // Generate a unique file name
                $fileName = Carbon::now()->timestamp . '-' . $review->id . '-' . uniqid() . '.' . $file['extension'];
        
                // Move the file manually from temporary storage
                $sourcePath = $file['path'];
                $destinationPath = storage_path("app/public/uploads/review_attachments/{$fileName}");
        
                // Ensure the directory exists
                if (!file_exists(dirname($destinationPath))) {
                    mkdir(dirname($destinationPath), 0777, true);
                }
        
                // Move the file to the destination
                if (file_exists($sourcePath)) {
                    rename($sourcePath, $destinationPath);
                } else {
                    // Log or handle the error (file might not exist at the temporary path)
                    continue;
                }
        
                // Save to the database
                ReviewAttachments::create([
                    'attachment' => $fileName,
                    'review_id' => $review->id,
                    'created_by' => Auth::user()->id,
                    'updated_by' => Auth::user()->id,
                ]);
            }
        }



         


       

        // dd($project_reviewer);

        $project_reviewer->review_status = $status;

        if($status == "approved"){
            $project_reviewer->status = false; 
        }


        $project_reviewer->updated_at = now();
        $project_reviewer->updated_by = Auth::user()->id;
        $project_reviewer->save();
 

        // Send notification email to reviewer 
        $user = User::findOrFail($this->project->creator->id);// insert the project submitter and creator
        $project = Project::where('id', $this->project->id)->first();

        $project->allow_project_submission = true; 

        //update the next reviewer
        if($status == "approved"){
            $project->allow_project_submission = false;
        }

        $project->updated_at = now(); 



        $project->reviewer_response_duration = $this->reviewer_response_duration;
        $project->reviewer_response_duration_type = $this->reviewer_response_duration_type;
        // after updating the project, update the due date timers
        $project->reviewer_due_date = Project::calculateDueDate(now(),$this->reviewer_response_duration_type, $this->reviewer_response_duration );


        $project->submitter_response_duration = $this->submitter_response_duration;
        $project->submitter_response_duration_type = $this->submitter_response_duration_type;  
        // $project->submitter_due_date = Project::calculateDueDate(now(),$project->submitter_response_duration_type, $project->submitter_response_duration );
        $project->submitter_due_date = Project::calculateDueDate(now(),$this->submitter_response_duration_type, $this->submitter_response_duration );
        

        $project->save();

 

        if ($user) {

            // notification to send to creator of the project about the reviewer review had been submitted . It is also an eamil notification
            $creator = User::where('id',$this->project->creator->id)->first();
            ProjectHelper::sendForProjectCreatorReviewerReviewNotification($creator, $project, $review);

            
            
        }

        
        //update the next reviewer
        if($status == "approved"){

            $next_project_reviewer = $project->getNextReviewer();

            if(!empty($next_project_reviewer)){ // check if there are next reviewers
                $next_project_reviewer->status = true;
                $next_project_reviewer->save();


                // add to the review 
                $review->next_reviewer_name = $next_project_reviewer->user->name;
                $review->save();


                // notify that reviewer that he is the next in line
                // Send notification email to the next reviewer
                $next_project_reviewer_user = User::where('id', $next_project_reviewer->user_id)->first();
                if ($next_project_reviewer_user) {

                    // notification to send to the reviewer about project review notification
                    ProjectHelper::sendForReviewersProjectReviewNotification($next_project_reviewer_user,$project,  $next_project_reviewer);

                }

            }else{ // if there are no more reviewers, meaning the project is completed

                $project->status = "approved";
                $project->save();



            }

           


        }


        ActivityLog::create([
            'log_action' => "Project review on \"".$project->name."\" submitted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);


 

        // update the subscribers 

            //message for the subscribers 
            $message = "The project '".$project->name."' had been rejected by reviewer '".Auth::user()->name."'";
    
            if($status == "approved"){
                
                $message = "The project '".$project->name."' had been approved by reviewer '".Auth::user()->name."'";
            }

            ProjectHelper::sendForProjectSubscribersProjectSubscribersNotification($project,$message,"project_reviewed");
            
 
        // ./ update the subscribers 



        

        Alert::success('Success','Project review submitted successfully');
        return redirect()->route('project.review',['project' => $this->project->id]);



    }


    public function render()
    {
        return view('livewire.admin.review.review-create');
    }
}
