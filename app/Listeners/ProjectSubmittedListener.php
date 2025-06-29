<?php

namespace App\Listeners;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\ProjectReviewer;
use App\Events\ProjectSubmitted;
use Illuminate\Support\Facades\Auth;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;

class ProjectSubmittedListener  implements ShouldQueue
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(ProjectSubmitted $event): void
    {



        /*
        

        // handle the project submission
        // this is on project that are not on que and to be submitted

  

        
        // if the project is a draft or on_que, create the default values
        if($project->status == "draft" || $project->status == "on_que"){



            // $project = Project::find($project_id);
            
            $project->status = "submitted";
            $project->allow_project_submission = false; // do not allow double submission until it is reviewed
            $project->updated_at = now();
            $project->save();

            $response_time_hours = 0;
            
            // Update the response time 

                // Ensure updated_at is after created_at
                if ($project->updated_at && now()->greaterThan($project->updated_at)) {
                    // Calculate time difference in hours
                    // $response_time_hours = $this->project->updated_at->diffInHours(now()); 

                    $response_time_hours = $project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
                    
                }
    
            // Update the response time 



            // setting up the project reviewers

                // project initial reviewers 
                    // Fetch all initial reviewers in order
                    $initial_reviewers = Reviewer::orderBy('order')
                        ->where('reviewer_type', 'initial')
                        ->get();

  
                    foreach ($initial_reviewers as $initial_reviewer) {
                        $projectInitialReviewer = ProjectReviewer::create([
                            'order' => $initial_reviewer->order,
                            'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                            'review_status' => 'pending',
                            'project_id' => $project->id,
                            'user_id' => $initial_reviewer->user_id, // the reviewer
                            'reviewer_type' => $initial_reviewer->reviewer_type, // reviewer type || INITIAL , DOCUMENT, FINAL
                            'project_document_id' => null, // initial reviewer is not connected to a document

                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]);
        

                    }

                // ./ project initial reviewers


                // project document reviewers
                    //get the project documents
                    if(!empty($project->project_documents)){

                        // fetch all project documents
                        foreach($project->project_documents as $project_document){
                            
                    

                            // Fetch all reviewers in order
                            $document_reviewers = Reviewer::orderBy('order')
                                ->where('document_type_id', $project_document->document_type_id)
                                ->where('reviewer_type','document')
                                ->get();


                            foreach ($document_reviewers as $document_reviewer) {
                                $projectDocumentReviewer = ProjectReviewer::create([
                                    'order' => $document_reviewer->order,
                                    'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                                    'review_status' => 'pending',
                                    'project_id' => $project->id,
                                    'user_id' => $document_reviewer->user_id, // the reviewer
                                    'reviewer_type' => $document_reviewer->reviewer_type, // reviewer type || INITIAL , DOCUMENT, FINAL
                                    'project_document_id' => $document_reviewer->reviewer_type === 'document' ? $project_document->id : null, // pertaining to the actual PROJECT project_document -> id
                                    // THE RPOJECT project_document_id is NOT the document_type id but the PROJECT project_document_id

                                    'created_by' => auth()->id(),
                                    'updated_by' => auth()->id(),
                                ]);
                
  
                            }




                        }

                    }
                // ./ project document reviewers


                // project final reviewers 
                    // Fetch all final reviewers in order
                    $final_reviewers = Reviewer::orderBy('order')
                        ->where('reviewer_type', 'final')
                        ->get();

  
                    foreach ($final_reviewers as $final_reviewer) {
                        $projectInitialReviewer = ProjectReviewer::create([
                            'order' => $final_reviewer->order,
                            'status' => false, /// true or false, tells if the reviewer is the active reviewer or not
                            'review_status' => 'pending',
                            'project_id' => $project->id,
                            'user_id' => $final_reviewer->user_id, // the reviewer
                            'reviewer_type' => $final_reviewer->reviewer_type, // reviewer type || INITIAL , DOCUMENT, FINAL
                            'project_document_id' => null, // final reviewer is not connected to a document

                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                        ]);
        

                    }

                // ./ project final reviewers





            // ./ setting up the project reviewers


            

            
            
            // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
            $first_reviewer = ProjectReviewer::where('project_id', $project->id)
                ->where('review_status', 'pending') 
                ->where('reviewer_type', 'initial')
                ->orderBy('order', 'asc')
                ->first();
 


            // update the first reviewer as the current reviewer
            $first_reviewer->status = true;
            $first_reviewer->save();

             

            // Send notification email to reviewer
            $user = User::find( $first_reviewer->user_id);
            if ($user) {
                Notification::send($user, new ProjectReviewNotification($project, $first_reviewer));

                //send notification to the database
                Notification::send($user, new ProjectReviewNotificationDB($project, $first_reviewer));
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
                            // 
                            //  * Message type : 
                            //  * @case('project_submitted')
                            //         @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                            //         @break

                            //     @case('project_reviewed')
                            //         @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                            //         @break

                            //     @case('project_resubmitted')
                            //         @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                            //         @break

                            //     @case('project_reviewers_updated')
                            //         @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                            //         @break

                            //     @default
                            //         @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                            // 


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
                                 
                                //  * Message type : 
                                //  * @case('project_submitted')
                                //         @php $message = "A new project, <strong>{$project->name}</strong>, has been submitted for review. Stay tuned for updates."; @endphp
                                //         @break

                                //     @case('project_reviewed')
                                //         @php $message = "The project <strong>{$project->name}</strong> has been reviewed. Check out the latest status."; @endphp
                                //         @break

                                //     @case('project_resubmitted')
                                //         @php $message = "The project <strong>{$project->name}</strong> has been updated and resubmitted for review."; @endphp
                                //         @break

                                //     @case('project_reviewers_updated')
                                //         @php $message = "The list of reviewers for the project <strong>{$project->name}</strong> has been updated."; @endphp
                                //         @break

                                //     @default
                                //         @php $message = "There is an important update regarding the project <strong>{$project->name}</strong>."; @endphp
                                 


                            }
                            


                        }
                    } 
                // ./ update the subscribers 



            }







        }
        */


 
        $project = Project::find($event->projectId);
        $user = User::find($event->authId);

        ActivityLog::create([
            'created_by' => $event->authId,
            'log_username' => $user->name,
            'log_action' =>   $event->message,
            'project_id' => $project->id,
        ]);
    }
}
