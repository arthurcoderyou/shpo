<?php 

use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\Project;
use App\Models\ProjectReviewer;
use App\Models\ProjectTimer;
use App\Models\Review;
use App\Models\Reviewer;
use App\Models\User;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Notification;
use RealRashid\SweetAlert\Facades\Alert;


 

if (!function_exists('greet_user')) {
    function greet_user($name) {
        return "Hello, " . ucfirst($name) . "!";
    }
}

if (!function_exists('submit_project')) {
    function submit_project($project_id){





        $errors = checkProjectRequirements();
        $errorMessages = [];

        foreach ($errors as $key => $error) {
            if ($error) {
                switch ($key) {
                    case 'response_duration':
                        $errorMessages[] = 'Response duration settings are not yet configured.';
                        break;
                    case 'project_submission_times':
                        $errorMessages[] = 'Project submission times are not set.';
                        break;
                    case 'no_reviewers':
                        $errorMessages[] = 'No reviewers have been set.';
                        break;
                    case 'no_document_types':
                        $errorMessages[] = 'Document types have not been added.';
                        break;
                }
            }
        }


        // if (!$this->isProjectSubmissionAllowed()) {
        //     $openTime = ProjectTimer::first()->project_submission_open_time;
        //     $closeTime = ProjectTimer::first()->project_submission_close_time;

        //     $errorMessages[] = 'Project submission is currently restricted. Please try again between ' . $openTime->format('h:i A') . ' and ' . $closeTime->format('h:i A');
        // }

        if (!empty($errorMessages)) {
            $message = 'The project cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            Alert::error('Error', $message);
            return redirect()->route('project.index');
        }



        // // check if there are existing project reviewers 
        // if(Reviewer::count() == 0){
        //     Alert::error('Error','Project reviewers are not added yet to the system');
        //     return redirect()->route('project.index');

        // }


        
        $project = Project::find($project_id);
        dd($project);

        $response_time_hours = 0;

        /** Update the response time */

            // Ensure updated_at is after created_at
            if ( $project->updated_at && now()->greaterThan( $project->updated_at)) {
                // Calculate time difference in hours
                // $response_time_hours = $this->project->updated_at->diffInHours(now()); 
                $response_time_hours = $project->updated_at->diffInSeconds(now()) / 3600; // shows hours in decimal
            }
 
        /** ./ Update the response time */


        // dd($project);


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
                //send email notification
                Notification::send($user, new ProjectReviewNotification($project, $reviewer));
                //send notification to the database
                Notification::send($user, new ProjectReviewNotificationDB($project, $reviewer));



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



            }





        }else{ // if not, get the current reviewer

            $reviewer = $project->getCurrentReviewer();
            $reviewer->review_status = "pending";
            $reviewer->save();


            
            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            Review::create([
                'viewed' => true,
                'project_review' => 'The project had been re-submitted', // message for not-draft projects
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
                //send email notification
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
        
        
        $project->status = "submitted";
        $project->allow_project_submission = false; // do not allow double submission until it is reviewed
        $project->updated_at = now();
        $project->save();



        


        ActivityLog::create([
            'log_action' => "Project \"".$project->name."\" submitted ",
            'log_username' => Auth::user()->name,
            'created_by' => Auth::user()->id,
        ]);

        Alert::success('Success','Project submitted successfully');
        return redirect()->route('project.index');


    }

}

if (!function_exists('checkProjectRequirements')) {
    /** Project Submission restriction  */
    function checkProjectRequirements()
    {
         
        $projectTimer = ProjectTimer::first();

        // DocumentTypes that don't have any reviewers
        $documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();

        // Check if all document types have at least one reviewer
        $allDocumentTypesHaveReviewers = empty($documentTypesWithoutReviewers);

        // Check if there are reviewers by type
        $hasInitialReviewers = Reviewer::where('reviewer_type', 'initial')->exists();
        $hasFinalReviewers = Reviewer::where('reviewer_type', 'final')->exists();


        
    
        return [
            'response_duration' => !$projectTimer || (
                !$projectTimer->submitter_response_duration_type ||
                !$projectTimer->submitter_response_duration ||
                !$projectTimer->reviewer_response_duration ||
                !$projectTimer->reviewer_response_duration_type
            ),
            'project_submission_times' => !$projectTimer || (
                !$projectTimer->project_submission_open_time ||
                !$projectTimer->project_submission_close_time ||
                !$projectTimer->message_on_open_close_time
            ),
            'no_reviewers' => Reviewer::count() === 0,
            'no_document_types' => DocumentType::count() === 0, // Add a new error condition
            'document_types_missing_reviewers' => !$allDocumentTypesHaveReviewers,
            'no_initial_reviewers' => !$hasInitialReviewers,
            'no_final_reviewers' => !$hasFinalReviewers,
        ];
    }
}

if (!function_exists('isProjectSubmissionAllowed')) {

    /**Check if project is within open and close hours */
    function isProjectSubmissionAllowed()
    {
        $projectTimer = ProjectTimer::first();

        if ($projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();
            $openTime = $projectTimer->project_submission_open_time;
            $closeTime = $projectTimer->project_submission_close_time;

            if ($currentTime < $openTime || $currentTime > $closeTime) {
                return false;
            }
        }

        return true;
    }

}


if (!function_exists('getUser')) {

    /**Check if project is within open and close hours */
    function getUser($user_id)
    {
        $user = User::find($user_id);
 

        return $user ?? null;
    }

}