<?php

namespace App\Livewire\Admin\Project;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Livewire\Component;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use Livewire\WithFileUploads;
use App\Models\ProjectReviewer;
use App\Models\ProjectAttachments;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;

class ProjectShow extends Component
{
    use WithFileUploads;
    public string $name = '';
    public string $description = '';
    public string $federal_agency = ''; 
    public $type;
    public $shpo_number;
    public $project_number;

    public $attachments = []; // Initialize with one phone field
    public $uploadedFiles = []; // Store file names

    public $existingFiles = [];

    public $project_id;

    public $user;

    public $status;


    public $enable_submit = true;

    public $project_reviewer;

    public $project;

    public $latitude;
    public $longitude;
    public $location;

    public $location_directions = [];

 
    public $selectedUsers;
    public function mount($id){

        $project = Project::find($id);
        $this->project = $project;

        $this->project_id = $project->id;

        $this->name = $project->name;
        $this->description = $project->description;
        $this->federal_agency = $project->federal_agency;
        $this->type = $project->type;

        $this->shpo_number = $project->shpo_number;

        $this->project_number = $project->project_number;

        $this->status = $project->getStatus();
        
        $this->user = $project->updator;
        // Load existing attachments

        // if(!empty($project->attachments)){
        //     $this->existingFiles = $project->attachments->map(function ($attachment) {
        //         return [
        //             'id' => $attachment->id,
        //             'name' => basename($attachment->attachment), // File name
        //             'path' => asset('storage/uploads/project_attachments/' . $attachment->attachment), // Public URL
        //         ];
        //     })->toArray();


        //     // dd($this->existingFiles);
        // }

        if (!empty($project->project_documents)) {
            $this->existingFiles = $project->project_documents
                ->sortByDesc('created_at') // Ensure newest files appear first
                ->groupBy(function ($document) {
                    return $document->created_at->format('M d, Y h:i A'); // Group by date
                })
                // ->map(function ($attachments) {
                //     return $attachments->map(function ($attachment) {
                //         return [
                //             'id' => $attachment->id,
                //             'name' => basename($attachment->attachment), // File name
                //             'path' => asset('storage/uploads/project_attachments/' . $attachment->attachment), // Public URL
                //         ];
                //     })->toArray();
                // })
                
                ->toArray();
        }



        if($project->status == "submitted"){
            $this->enable_submit = false;
        }


        if(!empty($project->getCurrentReviewer())){
            $this->project_reviewer = $project->getCurrentReviewer();

        }



        /**default is Guam coordinates */
        $this->latitude = $project->latitude ?? 13.4443; 
        $this->longitude = $project->longitude ?? 144.7937;
        $this->location = $project->location ?? "Guam";
        

        $this->location_directions[] =   Project::select(
                    'latitude', 'longitude'
                )
                ->where('id', $id) 
                ->get()
                ->toArray();

                
            
        if(!empty($project->project_subscribers)){
            foreach($project->project_subscribers as $subscriber){
                $this->selectedUsers[] = ['id' => $subscriber->user->id, 'name' => $subscriber->user->name];
            }
            
        }


    }


    public function updated($fields){
        $this->validateOnly($fields,[
            'name' => [
                'required',
                'string', 
            ],
            'description' => [
                'required'
            ],
            'federal_agency' => [
                'required'
            ]

        ]);
    }



    public function removeUploadedAttachment(int $id){

        // dd($id, gettype($id)); // Check the actual value and type
        // dd($id);
        // Find the attachment record
        $attachment = ProjectAttachments::find($id);

        if (!$attachment) {
            session()->flash('error', 'Attachment not found.');
            return;
        }

        // Construct the full file path
        $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

        // Check if the file exists in storage and delete it
        if (Storage::exists($filePath)) {
            Storage::delete($filePath);
        }

        // Delete the record from the database
        $attachment->delete();


        Alert::success('Success','Project attachment deleted successfully');
        return redirect()->route('project.edit',['project' => $attachment->project_id]);


    }

    
    
    public function submit_project($project_id){
        
        $project = Project::find($project_id);
         
        $project->status = "submitted";
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->updated_at = now();
        $project->save();

        $response_time_hours = 0;
        
        /** Update the response time */

            // Ensure updated_at is after created_at
            if ($this->project->updated_at && now()->greaterThan($project->updated_at)) {
                // Calculate time difference in hours
                // $response_time_hours = $this->project->updated_at->diffInHours(now()); 

                $response_time_hours = $project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
                
            }
 
        /** ./ Update the response time */


        
        // if the project is a draft, create the default values
        if($project->status == "draft"){
            // Fetch all reviewers in order
            $reviewers = Reviewer::orderBy('order')->get();

            foreach ($reviewers as $reviewer) {
                $projectReviewer = ProjectReviewer::create([
                    'order' => $reviewer->order,
                    'review_status' => 'pending',
                    'project_id' => $project->id,
                    'user_id' => $reviewer->user_id,
                    'created_by' => auth()->id(),
                    'updated_by' => auth()->id(),
                ]);

                
            }
            
            // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
            $reviewer = ProjectReviewer::where('project_id', $project->id)
                ->where('review_status', 'pending') 
                ->orderBy('order', 'asc')
                ->first();


            // update the first reviewer as the current reviewer
            $reviewer->status = true;
            $reviewer->save();

            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been submitted', // message for draft projects
                'project_id' => $project->id,
                'reviewer_id' =>  $project->created_by,
                'review_status' => 'submitted',
                'created_by' => $project->created_by,
                'updated_by' => $project->created_by,
                'response_time_hours' => $response_time_hours,
                
            ]);

            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
                Notification::send($user, new ProjectReviewNotification($project, $reviewer));

                //send notification to the database
                Notification::send($user, new ProjectReviewNotificationDB($project, $reviewer));
            }


            // update the subscribers 

                //message for the subscribers 
                $message = "The project '".$project->name."' had been submitted by '".Auth::user()->name."'";
        

                if(!empty($project->project_subscribers)){

                    $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                    foreach($project->project_subscribers as $subcriber){

                        // subscriber user 
                        $sub_user = User::where('id',$subcriber->user_id)->first();

                        if(!empty($sub_user)){
                            // notify the next reviewer
                            Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_submitted',$message ));
                            /**
                             * Message type : 
                             * @case('project_submitted')
                                    @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                    @break

                                @case('project_reviewed')
                                    @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                    @break

                                @case('project_resubmitted')
                                    @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                    @break

                                @case('project_reviewers_updated')
                                    @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                    @break

                                @default
                                    @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                            */


                        }
                        


                    }
                } 
            // ./ update the subscribers 



        }else{ // if not, get the current reviewer

            $reviewer = $project->getCurrentReviewer();
            $reviewer->review_status = "pending";
            $reviewer->save();

            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been re-submitted', // message for draft projects
                'project_id' => $project->id,
                'reviewer_id' =>  $project->created_by,
                'review_status' => 're_submitted',
                'created_by' => $project->created_by,
                'updated_by' => $project->created_by,
                'response_time_hours' => $response_time_hours,
            ]);


            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
                Notification::send($user, new ProjectReviewFollowupNotification($project, $reviewer));

                //send notification to the database
                Notification::send($user, new ProjectReviewFollowupNotificationDB($project, $reviewer));


                // update the subscribers 

                    //message for the subscribers 
                    $message = "The project '".$project->name."' had been re-submitted by '".Auth::user()->name."'";
            

                    if(!empty($project->project_subscribers)){

                        $sub_project = Project::where('id',$project->id)->first(); // get the project to be used for notification

                        foreach($project->project_subscribers as $subcriber){

                            // subscriber user 
                            $sub_user = User::where('id',$subcriber->user_id)->first();

                            if(!empty($sub_user)){
                                // notify the next reviewer
                                Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $sub_project,'project_resubmitted',$message ));
                                /**
                                 * Message type : 
                                 * @case('project_submitted')
                                        @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                        @break

                                    @case('project_reviewed')
                                        @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                        @break

                                    @case('project_resubmitted')
                                        @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                        @break

                                    @case('project_reviewers_updated')
                                        @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                        @break

                                    @default
                                        @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                                */


                            }
                            


                        }
                    } 
                // ./ update the subscribers 



            }







        }



        
        


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" submitted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project submitted successfully');
        return redirect()->route('project.index');


    }




    public function render()
    {
        return view('livewire.admin.project.project-show');
    }
}
