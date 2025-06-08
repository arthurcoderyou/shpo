<?php 
namespace App\Helpers;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\Reviewer;
use App\Models\ActivityLog;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;



class ProjectHelper
{
    // public static function submit()
    // {
    //     // your helper logic
    // }


 
 
 
    public static function submit_project($project_id){
 

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
 
        

        $projectTimer = ProjectTimer::first();
        $isProjectSubmissionAllowed = true;

        if ($projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();

            // Ensure open/close times are Carbon instances
            $openTime = Carbon::parse($projectTimer->project_submission_open_time);
            $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

                

            if ($currentTime->lt($openTime) || $currentTime->gt($closeTime)) {
                $isProjectSubmissionAllowed = false;
            } 
        }

 
        if (!$isProjectSubmissionAllowed) {
            // $openTime = ::first()->project_submission_open_time;
            // $closeTime = ProjectTimer::first()->project_submission_close_time;

            $openTime = Carbon::parse(ProjectTimer::first()->project_submission_open_time);
            $closeTime = Carbon::parse(ProjectTimer::first()->project_submission_close_time);


            $errorMessages[] = 'Project submission is currently restricted. Please try again between ' . $openTime->format('h:i A') . ' and ' . $closeTime->format('h:i A');

            // dd(isProjectSubmissionAllowed());
        }

        if (!empty($errorMessages)) {
            $message = 'The project cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            Alert::error('Error', $message);
            return redirect()->route('project.index');
        }



        // check if there are existing project reviewers 
        if(Reviewer::count() == 0){
            Alert::error('Error','Project reviewers are not added yet to the system');
            return redirect()->route('project.index');

        }


        
        $project = Project::find($project_id);

        // dd($project->project_documents->count());
        $attachmemts = $project->project_documents->count();



        
        // check if there are existing project documents as it is required 
        if($attachmemts == 0){
            Alert::error('Error','There must be atleast one project documents to the submitted project');
            return redirect()->route('project.index');

        }


        
         
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



        // dd("Projects had been submitted");
        // dd($project->project_documents);

        
        $submission_type = "";


        // if the project is a draft, create the default values
        if($project->status == "draft"){

            $submission_type = "submission";


            $project->status = "submitted";
            $project->allow_project_submission = false; // do not allow double submission until it is reviewed
            $project->updated_at = now();
            $project->save();
 
            



            if(!empty($project->project_documents) && count($project->project_documents) > 0){
 
                foreach($project->project_documents as $project_document){

                    // Fetch all reviewers in order that is part of the project document id 
                    $reviewers = Reviewer::orderBy('order')
                        ->where('document_type_id', $project_document->document_type_id)
                        ->get(); 
                    
                    foreach ($reviewers as $reviewer) {
                        ProjectReviewer::create([
                            'order' => $reviewer->order,
                            'review_status' => 'pending',
                            'project_id' => $project->id,
                            'user_id' => $reviewer->user_id,
                            'created_by' => auth()->id(),
                            'updated_by' => auth()->id(),
                            'reviewer_type' => 'document',
                            'project_document_id' => $project_document->id, // id of the connected document 

 
                        ]);

                        
                    }
                     

                }



                // ✅ Get the first project document
                $firstProjectDocument = $project->project_documents->first();

                if ($firstProjectDocument) {
                    // ✅ Get the first reviewer for this document (lowest order)
                    $firstReviewer = $firstProjectDocument->project_reviewers()
                        ->orderBy('order', 'asc')
                        ->first();

                    if ($firstReviewer) {
                        $firstReviewer->status = true; // mark as current/active
                        $firstReviewer->save();
                    }


                    // dd($firstReviewer->user->name);
                    ProjectHelper::notifyReviewersAndSubscribers($project, $firstReviewer, $submission_type);
                    


                }


                
            }

             

            // dd($project->project_reviewers);



            // // while status is true for project reviewer, this means that the project reviewer is the active/current reviewer o
            // $reviewer = ProjectReviewer::where('project_id', $project->id)
            //     ->where('review_status', 'pending') 
            //     ->orderBy('order', 'asc')
            //     ->first();


            // // update the first reviewer as the current reviewer
            // $reviewer->status = true;
            // $reviewer->save();


            



            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            // Review::create([
            //     'viewed' => true,
            //     'project_review' => 'The project had been submitted', // message for draft projects
            //     'project_id' => $project->id,
            //     'reviewer_id' =>  $project->created_by,
            //     'review_status' => 'submitted',
            //     'created_by' => $project->created_by,
            //     'updated_by' => $project->created_by,
            //     'response_time_hours' => $response_time_hours,
                
            // ]);


            




        }else{ // if not, get the current reviewer

            $submission_type = "re-submission";

            $project->status = "submitted";
            $project->allow_project_submission = false; // do not allow double submission until it is reviewed
            $project->updated_at = now();
            $project->save();



            if(!empty($project->project_documents) && count($project->project_documents) > 0){
 
                foreach($project->project_documents as $project_document){

                    // Fetch all reviewers in order that is part of the project document id 
                    $reviewers = Reviewer::orderBy('order')
                        ->where('document_type_id', $project_document->document_type_id)
                        ->get(); 
                    
                    foreach ($reviewers as $reviewer) {
                        // ProjectReviewer::create([
                        //     'order' => $reviewer->order,
                        //     'review_status' => 'pending',
                        //     'project_id' => $project->id,
                        //     'user_id' => $reviewer->user_id,
                        //     'created_by' => auth()->id(),
                        //     'updated_by' => auth()->id(),
                        //     'reviewer_type' => 'document',
                        //     'project_document_id' => $project_document->id, // id of the connected document 

 
                        // ]);

                        ProjectReviewer::firstOrCreate(
                            [
                                'project_id' => $project->id,
                                'user_id' => $reviewer->user_id,
                                'project_document_id' => $project_document->id,
                            ],
                            [
                                'order' => $reviewer->order,
                                'review_status' => 'pending',
                                'created_by' => auth()->id(),
                                'updated_by' => auth()->id(),
                                'reviewer_type' => 'document',
                            ]
                        );

                        /**
                         * 🔍 Why firstOrCreate?
                            It checks for an existing record with the specified unique keys.

                            If it doesn’t exist, it creates a new one using the second argument (attribute values).

                            Prevents duplicates and keeps the logic clean.
                         * 
                         */



                        
                    }
                     

                }



                // ✅ Get the first project document
                $firstProjectDocument = $project->project_documents->first();

                if ($firstProjectDocument) {
                    // ✅ Get the first reviewer for this document (lowest order)
                    $firstReviewer = $firstProjectDocument->project_reviewers()
                        ->orderBy('order', 'asc')
                        ->first();

                    if ($firstReviewer) {
                        $firstReviewer->status = true; // mark as current/active
                        $firstReviewer->save();
                    }


                    // dd($firstReviewer->user->name);
                    // ProjectHelper::notifyReviewersAndSubscribers($project, $firstReviewer, $submission_type);
                    


                }


                
            }




            $reviewer = $project->getCurrentReviewer();
            // dd($reviewer);


            $reviewer->review_status = "pending";
            $reviewer->save();


            ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);


            
            // submitting a project creates a review that the user had submitted the project
            // the condition is that the project creator id must be hte same to the reviewer id
            // Review::create([
            //     'viewed' => true,
            //     'project_review' => 'The project had been re-submitted', // message for not-draft projects
            //     'project_id' => $project->id,
            //     'reviewer_id' =>  $project->created_by,
            //     'review_status' => 're_submitted',
            //     'created_by' => $project->created_by,
            //     'updated_by' => $project->created_by,
            //     'response_time_hours' => $response_time_hours,
                
            // ]);

            /*
            // Send notification email to reviewer
            $user = User::find( $reviewer->user_id);
            if ($user) {
 
                try {
                    //send email notification
                    Notification::send($user, new ProjectReviewFollowupNotification($project, $reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewFollowupNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

 
                try {
                    //send notification to the database
                    Notification::send($user, new ProjectReviewFollowupNotificationDB($project, $reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewFollowupNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }


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



            }
                */




        }
        
        
        

        // if($submission_type = "submission")
        try {
            event(new \App\Events\ProjectSubmitted($project, $submission_type));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to dispatch ProjectSubmitted event: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
            
        


        // ActivityLog::create([
        //     'log_action' => "Project \"".$project->name."\" submitted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);

        // try {
        //     event(new \App\Events\ProjectSubmitted($project));
        // } catch (\Throwable $e) {
        //     // Log the error without interrupting the flow
        //     Log::error('Failed to dispatch ProjectSubmitted event: ' . $e->getMessage(), [
        //         'project_id' => $project->id,
        //         'trace' => $e->getTraceAsString(),
        //     ]);
        // }


        Alert::success('Success','Project submitted successfully');
        return redirect()->route('project.index');


    }
 
 
    /** Project Submission restriction  */
    public static function checkProjectRequirements()
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
 

    /**Check if project is within open and close hours */
    public static function isProjectSubmissionAllowed()
    {
        $projectTimer = ProjectTimer::first();

        // if ($projectTimer->project_submission_restrict_by_time) {
        //     $currentTime = now();
        //     $openTime = $projectTimer->project_submission_open_time;
        //     $closeTime = $projectTimer->project_submission_close_time;

        //     if ($currentTime < $openTime || $currentTime > $closeTime) {
        //         return false;
        //     }
        // }

        if ($projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();

            // Ensure open/close times are Carbon instances
            $openTime = Carbon::parse($projectTimer->project_submission_open_time);
            $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

            dd($openTime);

            if ($currentTime->lt($openTime) || $currentTime->gt($closeTime)) {
                return false;
            }
        }



        return true;
    }




    public static function notifyReviewersAndSubscribers($project, $reviewer, $submission_type = null)
    {

        # $submission_type
        # submission        
        # re-submission

        $user = User::find($reviewer->user_id);

        if ($user) {

            $message = "";

            if($submission_type == "submission"){

                try {
                    // Send email and DB notification to reviewer
                    Notification::send($user, new ProjectReviewNotification($project, $reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                try {
                    // Send   DB notification to reviewer
                    Notification::send($user, new ProjectReviewNotificationDB($project, $reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewNotificationDB notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
                
                // Message for the subscribers
                $message = "The project '" . $project->name . "' has been submitted by '" . Auth::user()->name . "'";

            }elseif($submission_type == "re-submission"){

                try {
                    //send email notification
                    Notification::send($user, new ProjectReviewFollowupNotification($project, $reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewFollowupNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

 
                try {
                    //send notification to the database
                    Notification::send($user, new ProjectReviewFollowupNotificationDB($project, $reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewFollowupNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }


                //message for the subscribers 
                $message = "The project '".$project->name."' had been re-submitted by '".Auth::user()->name."'";
            


            }

            
            

           

            if (!empty($project->project_subscribers)) {
                $project = Project::where('id',$project->id)->first();

                foreach ($project->project_subscribers as $subscriber) {
                    $subUser = User::where('id',$subscriber->user_id)->first();
                    

                    if ($subUser) {

                        try {

                            if($submission_type == "submission"){
                                Notification::send($subUser, new ProjectSubscribersNotification(
                                    $subUser,
                                    $project,
                                    'project_submitted',
                                    $message
                                ));

                            }elseif($submission_type == "re-submission"){
                                Notification::send($subUser, new ProjectSubscribersNotification($subUser, $project,'project_resubmitted',$message ));

                            }
                                 

                        } catch (\Throwable $e) {
                            // Log the error without interrupting the flow
                            Log::error('Failed to send ProjectSubscribersNotification notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

 
                    }
                }
            }





        }





    }



    // notify creator, project reviewers and project subscribers 
    public static function notifyReviewersAndSubscribersOnProjectReviewerUpdate($project, $project_document_id, $reviewer_type)
    {

        //get all the project reviewers of the project within that project document id
        $project_reviewers = ProjectReviewer::where('project_document_id', $project_document_id)
            ->where('project_id',$project->id)
            ->where('reviewer_type', $reviewer_type)->get();
        // dd($project_reviewers);



        // notify the creator about the project reviewer update 
        $creator = User::where('id',$project->created_by)->first();
 
        if ($creator) {
             
            try {
                // Send email  to creator about the project reviewer update 
                Notification::send($creator, new ProjectReviewerUpdatedNotification($project, $creator));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send ProjectReviewerUpdatedNotification notification: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
 
            try {
                // Send DB notification to creator about the project reviewer update 
                Notification::send($creator, new ProjectReviewerUpdatedNotificationDB($project, $creator));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send ProjectReviewerUpdatedNotificationDB notification: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


        }
 

        

 

        if(!empty($project_reviewers)){ // check if there are project reviewers 

            foreach($project_reviewers as $reviewer){
               

                $user = User::where('id',$reviewer->user_id)->first();

                if ($user) {


                    try {
                        // Send email   notification to reviewer about the project reviewer update 
                        Notification::send($user, new ProjectReviewerUpdatedNotification($project, $user));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to send ProjectReviewerUpdatedNotification notification: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    try {
                        // Send DB notification to reviewer about the project reviewer update 
                        Notification::send($user, new ProjectReviewerUpdatedNotificationDB($project, $user));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to send ProjectReviewerUpdatedNotificationDB notification: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    
                    $current_reviewer = $project->getCurrentReviewer();

                    // if reviewer is the current reviewer, send a review notification   
                    if ($reviewer->id ==  $current_reviewer->id) {

                        try {
                             // Send email   notification to reviewer
                            Notification::send($user, new ProjectReviewNotification($project, $reviewer));
                        } catch (\Throwable $e) {
                            // Log the error without interrupting the flow
                            Log::error('Failed to send ProjectReviewNotification notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

                        try {
                             // Send   DB notification to reviewer
                            Notification::send($user, new ProjectReviewNotificationDB($project, $reviewer));
                        } catch (\Throwable $e) {
                            // Log the error without interrupting the flow
                            Log::error('Failed to send ProjectReviewNotificationDB notification: ' . $e->getMessage(), [
                                'project_id' => $project->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }

                       
                        

                        

                        
                    }
                     


 
                }

 
                 
            }

                

        }
            
        // // Message for the subscribers
        $message = "The project '" . $project->name . "' has updated project reviewers ";

        if (!empty($project->project_subscribers)) {
            foreach ($project->project_subscribers as $subscriber) {
                $subUser = User::where('id',$subscriber->user_id)->first();

                if ($subUser) {


                    try {
                        // Send   email notification to reviewer
                        Notification::send($subUser, new ProjectSubscribersNotification(
                            $subUser,
                            $project,
                            'project_reviewers_updated',
                            $message
                        ));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to send ProjectSubscribersNotification notification: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    


                }
            }
        }





    }


 


    // notification to send to creator of the project about the reviewer review had been submitted . It is also an eamil notification
    public static function sendForProjectCreatorReviewerReviewNotification(User $user,Project $project, Review $review){
 
        try {
            //this is to notify the creator of the project that his project had been reviewed
            Notification::send($user, new ReviewerReviewNotification($project, $review));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send ReviewerReviewNotification notification: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        try {
            //notify the database
            Notification::send($user, new ReviewerReviewNotificationDB($project, $review));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send ReviewerReviewNotificationDB notification: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
        

        

    }




    // notification to send to the reviewer about project review notification
    public static function sendForReviewersProjectReviewNotification(User $reviewer_user, Project $project, ProjectReviewer $reviewer){

        try {
            // notify the next reviewer
        Notification::send($reviewer_user, new ProjectReviewNotification($project, $reviewer));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send ProjectReviewNotification notification: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        try {
           //send notification to the database
            Notification::send($reviewer_user, new ProjectReviewNotificationDB($project, $reviewer));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send ProjectReviewNotificationDB notification: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }

        

    }



    // notification to send to project subscribers about the project update 
    public static function sendForProjectSubscribersProjectSubscribersNotification(Project $project, $message, $project_status ){
        if(!empty($project->project_subscribers)){
            foreach($project->project_subscribers as $subcriber){

                // subscriber user 
                $sub_user = User::where('id',$subcriber->user_id)->first();

                if(!empty($sub_user)){

                    try {
                        // notify the next reviewer
                        Notification::send($sub_user, new ProjectSubscribersNotification($sub_user, $project,$project_status,$message )); 
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to send ProjectSubscribersNotification notification: ' . $e->getMessage(), [
                            'project_id' => $project->id,
                            'trace' => $e->getTraceAsString(),
                        ]);
                    }

                    
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

    }

}
