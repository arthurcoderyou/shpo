<?php 
namespace App\Helpers;
use Carbon\Carbon;
use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\Reviewer;
use App\Models\ActiveDays;
use App\Models\ActivityLog;
use Illuminate\Support\Arr;
use App\Models\DocumentType;
use App\Models\ProjectTimer;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Events\Project\ProjectLogEvent;
use App\Helpers\ProjectReviewerHelpers;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Helpers\ActivityLogHelpers\ProjectLogHelper;
use App\Notifications\ProjectOpenReviewNotification;
use App\Helpers\ActivityLogHelpers\ActivityLogHelper;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectOpenReviewNotificationDB;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Helpers\ActivityLogHelpers\ProjectTimerLogHelper;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectCompleteApprovalNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;
use App\Helpers\SystemNotificationHelpers\ProjectNotificationHelper;



class ProjectHelper
{
    // public static function submit()
    // {
    //     // your helper logic
    // }


 
 
 
    public static function submit_project($project_id, $override_on_que_submission = false){
 

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
        $queuedForNextDay = false;
        $errorMessages = [];

        if ($projectTimer && $projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();
            $currentDay = $currentTime->format('l'); // e.g. "Monday"

            // Ensure times are Carbon instances
            $openTime = Carbon::parse($projectTimer->project_submission_open_time);
            $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

            // Check if today is active
            $isDayActive = ActiveDays::where('day', $currentDay)
                            ->where('is_active', true)
                            ->exists();

            // Check if within time
            $isWithinTime = $currentTime->between($openTime, $closeTime);

            // Set flag
            if (!($isDayActive && $isWithinTime)) {
                $isProjectSubmissionAllowed = false;
                $queuedForNextDay = true;
            }
        }

        

        $project = Project::find($project_id);
        


        if (!empty($errorMessages)) {
            $message = 'The project cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            Alert::error('Error', $message);


            if($project->created_by == Auth::id()){ 
                return redirect()->route('project.index');

            }else{ 

                // return redirect()->route('project.index');
                return ProjectHelper::returnHomeRouteBasedOnProject($project);



            }


            
        }



        // check if there are existing project reviewers 
        if(Reviewer::count() == 0){
            Alert::error('Error','Project reviewers are not added yet to the system');

            if($project->created_by == Auth::id()){ 
                return redirect()->route('project.index');

            }else{ 

                // return redirect()->route('project.index');
                return ProjectHelper::returnHomeRouteBasedOnProject($project);

            }

             

        }


        
        

        // dd($project->project_documents->count());
        $attachmemts = $project->project_documents->count();



        
        // check if there are existing project documents as it is required 
        if($attachmemts == 0){
            Alert::error('Error','There must be atleast one project documents to the submitted project'); 

             if($project->created_by == Auth::id()){ 
                return redirect()->route('project.index');

            }else{ 

                // return redirect()->route('project.index');
                return ProjectHelper::returnHomeRouteBasedOnProject($project);

            }



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


        
        // if override admin que submit is true
        if($override_on_que_submission){


            $submission_type = "submission";


            // override submit the project 
            $project->status = "submitted";
            $project->allow_project_submission = false; // do not allow double submission until it is reviewed
            
            $project->updated_at = now();
            $project->last_submitted_at = now();
            $project->last_submitted_by = Auth::user()->id;
            $project->save();


            ProjectHelper::updateDocumentsAndAttachments($project);

        }else{


           

            // SUBMISSION OF NEW PROJECTS 
            // if the project is a draft, create the default values
            if($project->status == "draft"){

                $submission_type = "submission";



                // check if project submission restriction is on
                // que the project for submission if the project is on queue
                if (!$isProjectSubmissionAllowed && $queuedForNextDay) {
                    // Find the next available active day
                    $daysOfWeek = [
                        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                    ];

                    $nextAvailableDay = null;
                    for ($i = 1; $i <= 7; $i++) {
                        $nextDay = now()->addDays($i)->format('l');
                        $isNextActive = ActiveDays::where('day', $nextDay)
                            ->where('is_active', true)
                            ->exists();
                        if ($isNextActive) {
                            $nextAvailableDay = $nextDay;
                            break;
                        }
                    }

                    $formattedOpen = Carbon::parse($projectTimer->project_submission_open_time)->format('h:i A');
                    $formattedClose = Carbon::parse($projectTimer->project_submission_close_time)->format('h:i A');

                    $message = "Project submission is currently restricted. Your project has been queued and will be submitted automatically on $nextAvailableDay between $formattedOpen and $formattedClose.";

                    // update project info
                    $project->status = "on_que";
                    $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                    
                    $project->updated_at = now();
                    $project->last_submitted_at = now();
                    $project->last_submitted_by = Auth::user()->id;
                    $project->save();

                    ProjectHelper::updateDocumentsAndAttachments($project);


                    Alert::info('Project Notice',$message);

                    return ProjectHelper::returnHomeRouteBasedOnProject($project);


                }else{
                // else, submit normally
                    $project->status = "submitted";
                    $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                    
                    $project->updated_at = now();
                    $project->last_submitted_at = now();
                    $project->last_submitted_by = Auth::user()->id;
                    $project->save();


                    ProjectHelper::updateDocumentsAndAttachments($project);

                }

    
                
                // ProjectHelper::setProjectReviewers($project,$submission_type);

                // $reviewer = $project->getNextReviewer();

                // ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

            // RESUBMISSION OF PROJECTS
            }else{ // if not, get the current reviewer


                // condition for resubmission
                $errorMessage = ProjectHelper::getResubmissionErrorMessage($project);

                if ($errorMessage) {
                    Alert::error('Error', $errorMessage);

                    return redirect()->route('project.index');
                }



                // dd("Resubmission is good");


                // update the current project reviewer 
                // for resubmission, make the current reviewer review_status into pending;

                $current_reviewer = $project->getCurrentReviewer();
                $current_reviewer->review_status = "pending";
                $current_reviewer->save();



                $submission_type = "re-submission";
                
                $project->status = "submitted";
                $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                
                $project->updated_at = now();
                $project->last_submitted_at = now();
                $project->last_submitted_by = Auth::user()->id;
                $project->save();

                ProjectHelper::updateDocumentsAndAttachments($project);
    

            }

        
        
        }


        



 
        ProjectHelper::setProjectReviewers($project,$submission_type);


        // $reviewer = $project->getCurrentReviewer(); // get the current reviewer


        // ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

        foreach($project->project_documents as $project_document){

            $reviewer =     Project::getCurrentReviewerByProjectDocument($project_document->id);

            // ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

            // notify creator, project reviewers and project subscribers 
            ProjectHelper::notifyRevs_Subs_on_RevUpd($project, $project_document->id, 'document');

            if($reviewer->slot_type == "person"){// if the current reviewer is a person
                $reviewer_user = User::find($reviewer->user_id); 
                ProjectHelper::sendForReviewersProjectReviewNotification($reviewer_user,$project, $reviewer);

            }else if($reviewer->slot_type == "open"){

                ProjectHelper::sendForReviewersOpenProjectReviewNotification($project, $reviewer);
                

            }
             

        }



            
        // if($submission_type = "submission")
        try {
            event(new \App\Events\ProjectSubmitted($project, $submission_type,$project->created_by));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to dispatch ProjectSubmitted event: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
            
        

        Alert::success('Success','Project submitted successfully');

        if($project->created_by == auth()->user()->id){
            return redirect()->route('project.index');
            
        }else{
            // return redirect()->route('project.index');
            return ProjectHelper::returnHomeRouteBasedOnProject($project);
        } 
            
            
        

    }


    // submit project for rc evaluation 
    public static function submit_project_for_rc_evaluation($project_id, $override_on_que_submission = false){
 
        // dd($project_id);

 

        $errors = checkProjectRequirements();
        $errorMessages = [];

        $authId = Auth::user()->id ?? null;

        foreach ($errors as $key => $error) {
            if ($error) {
                switch ($key) {
                    case 'response_duration':
                        $errorMessages[] = ProjectTimerLogHelper::getActivityMessage('response_duration',$authId) ;
                        break;
                    case 'project_submission_times':
                        $errorMessages[] =  ProjectTimerLogHelper::getActivityMessage('project_submission_times',$authId);
                        break;
                    case 'no_reviewers':
                        $errorMessages[] = ProjectTimerLogHelper::getActivityMessage('no_reviewers',$authId);
                        break;
                    case 'no_document_types':
                        $errorMessages[] = ProjectTimerLogHelper::getActivityMessage('no_document_types',$authId);
                        break;
                }
            }
        }
  

        $projectTimer = ProjectTimer::first();
        $isProjectSubmissionAllowed = true;
        $queuedForNextDay = false;
        $errorMessages = [];

        if ($projectTimer && $projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();
            $currentDay = $currentTime->format('l'); // e.g. "Monday"

            // Ensure times are Carbon instances
            $openTime = Carbon::parse($projectTimer->project_submission_open_time);
            $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

            // Check if today is active
            $isDayActive = ActiveDays::where('day', $currentDay)
                            ->where('is_active', true)
                            ->exists();

            // Check if within time
            $isWithinTime = $currentTime->between($openTime, $closeTime);

            // Set flag
            if (!($isDayActive && $isWithinTime)) {
                $isProjectSubmissionAllowed = false;
                $queuedForNextDay = true;
            }
        }

        

        $project = Project::find($project_id);
        


        if (!empty($errorMessages)) {
            $message = 'The project cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            // Alert::error('Error', $message);




            // if($project->created_by == Auth::id()){ 
            //     return redirect()->route('project.index');

            // }else{ 

            //     // return redirect()->route('project.index');
            //     return ProjectHelper::returnHomeRouteBasedOnProject($project);



            // }

            return redirect()->route('project.show',['project' => $project->id])
                ->with('alert.error',$message)
                ;


            
        }



        // check if there are existing project reviewers 
        if(Reviewer::count() == 0){
            $message = 'Project reviewers are not added yet to the system';
            // Alert::error('Error','Project reviewers are not added yet to the system');

            // if($project->created_by == Auth::id()){ 
            //     return redirect()->route('project.index');

            // }else{ 

            //     // return redirect()->route('project.index');
            //     return ProjectHelper::returnHomeRouteBasedOnProject($project);

            // }

            return redirect()->route('project.show',['project' => $project->id])
                 ->with('alert.error',$message)
            ;


             

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

        $authId = auth()->user()->id;
        

        // get the message from the helper 
        $message = ProjectLogHelper::getActivityMessage('submitted', $project->id, $authId);

        
        // if override admin que submit is true
        if($override_on_que_submission){

            // dd("Here");
            $submission_type = "submission";


            // override submit the project 
            $project->status = "submitted";
            $project->allow_project_submission = false; // do not allow double submission until it is reviewed
            
            $project->updated_at = now();
            $project->last_submitted_at = now();
            $project->last_submitted_by = Auth::user()->id;
            $project->save();


            // logging and system notifications
                $authId = Auth::check() ? Auth::id() : null;

                // get the message from the helper 
                $message = ProjectLogHelper::getActivityMessage('force-submit', $project->id, $authId);

                // get the route
                $route = ProjectLogHelper::getRoute('force-submit', $project->id);

                // log the event 
                event(new ProjectLogEvent(
                    $message ,
                    $authId, 
                    $project->id,

                ));
        
                /** send system notifications to users */
                    
                    ProjectNotificationHelper::sendSystemNotification(
                        message: $message,
                        route: $route 
                    );

                /** ./ send system notifications to users */
            // ./ logging and system notifications

            ProjectHelper::updateDocumentsAndAttachments($project);

        }else{


           

            // SUBMISSION OF NEW PROJECTS 
            // if the project is a draft, create the default values
            if($project->status == "draft"){

                $submission_type = "submission";



                // check if project submission restriction is on
                // que the project for submission if the project is on queue
                if (!$isProjectSubmissionAllowed && $queuedForNextDay) {
                    // Find the next available active day
                    $daysOfWeek = [
                        'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                    ];

                    $nextAvailableDay = null;
                    for ($i = 1; $i <= 7; $i++) {
                        $nextDay = now()->addDays($i)->format('l');
                        $isNextActive = ActiveDays::where('day', $nextDay)
                            ->where('is_active', true)
                            ->exists();
                        if ($isNextActive) {
                            $nextAvailableDay = $nextDay;
                            break;
                        }
                    }

                    $formattedOpen = Carbon::parse($projectTimer->project_submission_open_time)->format('h:i A');
                    $formattedClose = Carbon::parse($projectTimer->project_submission_close_time)->format('h:i A');

                    $message = "Project submission is currently restricted. Your project has been queued and will be submitted automatically on $nextAvailableDay between $formattedOpen and $formattedClose.";


                    // dd("Timer detectected");

                    // update project info
                    $project->status = "on_que";
                    $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                    
                    $project->updated_at = now();
                    $project->last_submitted_at = now();
                    $project->last_submitted_by = Auth::user()->id;
                    $project->save();

                    ProjectHelper::updateDocumentsAndAttachments($project);


                    // Alert::info('Project Notice',$message);

                    

                    // logging and system notifications
                        $authId = Auth::check() ? Auth::id() : null;

                        // get the message from the helper 
                        $message = ProjectLogHelper::getActivityMessage('on-que', $project->id, $authId);

                        // get the route
                        $route = ProjectLogHelper::getRoute('on-que', $project->id);

                        // log the event 
                        event(new ProjectLogEvent(
                            $message ,
                            $authId, 
                            $project->id,

                        ));
                
                        /** send system notifications to users */
                            
                            ProjectNotificationHelper::sendSystemNotification(
                                message: $message,
                                route: $route 
                            );

                        /** ./ send system notifications to users */
                    // ./ logging and system notifications



                    // return ProjectHelper::returnHomeRouteBasedOnProject($project);

                    return redirect()->route('project.show',[
                        'project' => $project->id,
                    ])
                    ->with('alert.info',$message)
                    ;

                }else{

                    // dd("Timer not detectected");

                    // else, submit normally
                    $project->status = "submitted";
                    $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                    
                    $project->updated_at = now();
                    $project->last_submitted_at = now();
                    $project->last_submitted_by = Auth::user()->id;
                    $project->save();


                    ProjectHelper::updateDocumentsAndAttachments($project);

                }

    
                
                // ProjectHelper::setProjectReviewers($project,$submission_type);

                // $reviewer = $project->getNextReviewer();

                // ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

             
            } 

        
        
        }

        // dd("Passed all");
        

        $submission_type = "initial_submission";

 
        ProjectReviewerHelpers::setProjectReviewer($project,$submission_type);
 

        

        // when override submission, use the project creator 
        if($override_on_que_submission){
            $authId = $project->created_by;
        }
        // if($submission_type = "submission")
        try {
            event(new \App\Events\Project\Submitted($project->id,$authId ,true,true));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to dispatch Submitted event: ' . $e->getMessage(), [
                'project_id' => $project->id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
            
        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // // get the message from the helper 
            // $message = ProjectLogHelper::getActivityMessage('on-que', $project->id, $authId);

            // // get the route
            $route = ProjectLogHelper::getRoute('submitted', $project->id);

            // log the event 
            event(new ProjectLogEvent(
                $message ,
                $authId, 
                $project->id,

            ));
    
            /** send system notifications to users */
                
                ProjectNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications



        // send notification to the current reviewer
        ProjectReviewerHelpers::sendProjectReviewNotificationToReviewer($project,$submission_type,$override_on_que_submission );


        // Alert::success('Success','Project submitted successfully');

        // if($project->created_by == $authId){
        //     return redirect()->route('project.index');
            
        // }else{
        //     // return redirect()->route('project.index');
        //     return ProjectHelper::returnHomeRouteBasedOnProject($project);
        // } 



        return redirect()->route('project.show',[
            'project' => $project->id,
        ])
        ->with('alert.success',$message);
        ;
            
        

    }




    // STILL IN DEVELOPMENT 
    // this is connected to the project submission of project reviewers
    public static function submit_project_update($project_id){
 

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
 
        

        // $projectTimer = ProjectTimer::first();
        // $isProjectSubmissionAllowed = true;

        // if ($projectTimer->project_submission_restrict_by_time) {
        //     $currentTime = now();

        //     // Ensure open/close times are Carbon instances
        //     $openTime = Carbon::parse($projectTimer->project_submission_open_time);
        //     $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

                

        //     if ($currentTime->lt($openTime) || $currentTime->gt($closeTime)) {
        //         $isProjectSubmissionAllowed = false;
        //     } 
        // }

 
        // if (!$isProjectSubmissionAllowed) {
        //     // $openTime = ::first()->project_submission_open_time;
        //     // $closeTime = ProjectTimer::first()->project_submission_close_time;

        //     $openTime = Carbon::parse(ProjectTimer::first()->project_submission_open_time);
        //     $closeTime = Carbon::parse(ProjectTimer::first()->project_submission_close_time);


        //     $errorMessages[] = 'Project submission is currently restricted. Please try again between ' . $openTime->format('h:i A') . ' and ' . $closeTime->format('h:i A');

        //     // dd(isProjectSubmissionAllowed());
        // }




        $projectTimer = ProjectTimer::first();
        $isProjectSubmissionAllowed = true;
        $queuedForNextDay = false;
        $errorMessages = [];

        if ($projectTimer && $projectTimer->project_submission_restrict_by_time) {
            $currentTime = now();
            $currentDay = $currentTime->format('l'); // e.g. "Monday"

            // Ensure times are Carbon instances
            $openTime = Carbon::parse($projectTimer->project_submission_open_time);
            $closeTime = Carbon::parse($projectTimer->project_submission_close_time);

            // Check if today is active
            $isDayActive = ActiveDays::where('day', $currentDay)
                            ->where('is_active', true)
                            ->exists();

            // Check if within time
            $isWithinTime = $currentTime->between($openTime, $closeTime);

            // Set flag
            if (!($isDayActive && $isWithinTime)) {
                $isProjectSubmissionAllowed = false;
                $queuedForNextDay = true;
            }
        }

        

        $project = Project::find($project_id);
        


        if (!empty($errorMessages)) {
            $message = 'The project cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            Alert::error('Error', $message);


            return redirect()->route('reviewer.index');


            
        }



        // check if there are existing project reviewers 
        if(Reviewer::count() == 0){
            Alert::error('Error','Project reviewers are not added yet to the system');
 
            return redirect()->route('reviewer.index');
 
             

        }
  
        $submission_type = "";


        // if the project is a draft, create the default values
        if($project->status == "draft"){

            $submission_type = "submission";



            // check if project submission restriction is on
            // que the project for submission if the project is on queue
            if (!$isProjectSubmissionAllowed && $queuedForNextDay) {
                // Find the next available active day
                $daysOfWeek = [
                    'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'
                ];

                $nextAvailableDay = null;
                for ($i = 1; $i <= 7; $i++) {
                    $nextDay = now()->addDays($i)->format('l');
                    $isNextActive = ActiveDays::where('day', $nextDay)
                        ->where('is_active', true)
                        ->exists();
                    if ($isNextActive) {
                        $nextAvailableDay = $nextDay;
                        break;
                    }
                }

                $formattedOpen = Carbon::parse($projectTimer->project_submission_open_time)->format('h:i A');
                $formattedClose = Carbon::parse($projectTimer->project_submission_close_time)->format('h:i A');

                $message = "Project submission is currently restricted. Your project has been queued and will be submitted automatically on $nextAvailableDay between $formattedOpen and $formattedClose.";

                // update project info
                $project->status = "on_que";
                $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                
                $project->updated_at = now();
                $project->last_submitted_at = now();
                $project->last_submitted_by = Auth::user()->id;
                $project->save();

                ProjectHelper::updateDocumentsAndAttachments($project);


                Alert::info('Project Notice',$message);

                return ProjectHelper::returnHomeRouteBasedOnProject($project);


            }else{
            // else, submit normally
                $project->status = "submitted";
                $project->allow_project_submission = false; // do not allow double submission until it is reviewed
                
                $project->updated_at = now();
                $project->last_submitted_at = now();
                $project->last_submitted_by = Auth::user()->id;
                $project->save();


                ProjectHelper::updateDocumentsAndAttachments($project);

            }

 
            
            // ProjectHelper::setProjectReviewers($project,$submission_type);

            // $reviewer = $project->getNextReviewer();

            // ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);


        }else{ // if not, get the current reviewer


            // condition for resubmission
            $errorMessage = ProjectHelper::getResubmissionErrorMessage($project);

            if ($errorMessage) {
                Alert::error('Error', $errorMessage);

                return redirect()->route('project.index');
            }



            // dd("Resubmission is good");


            // update the current project reviewer 
            // for resubmission, make the current reviewer review_status into pending;

            $current_reviewer = $project->getCurrentReviewer();
            $current_reviewer->review_status = "pending";
            $current_reviewer->save();



            $submission_type = "re-submission";
            
            $project->status = "submitted";
            $project->allow_project_submission = false; // do not allow double submission until it is reviewed
            
            $project->updated_at = now();
            $project->last_submitted_at = now();
            $project->last_submitted_by = Auth::user()->id;
            $project->save();

            ProjectHelper::updateDocumentsAndAttachments($project);
 

        }

        if($project->status != "on_que"){
            ProjectHelper::setProjectReviewers($project,$submission_type);


            $reviewer = $project->getCurrentReviewer(); // get the current reviewer


            ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

             
            // if($submission_type = "submission")
            try {
                event(new \App\Events\ProjectSubmitted($project, $submission_type,$project->created_by));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch ProjectSubmitted event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }
                
            

            Alert::success('Success','Project submitted successfully');

            if($project->created_by == auth()->user()->id){
                return redirect()->route('project.index');
                
            }else{
                // return redirect()->route('project.index');
                return ProjectHelper::returnHomeRouteBasedOnProject($project);
            }
        }
            
            
        

    }

    // ./ STILL IN DEVELOPMENT 




 

    public static function updateDocumentsAndAttachments(Project $project){

        // Assume $project is the current project you're submitting
        foreach ($project->project_documents as $projectDocument) {
            // Update the project_document
            $projectDocument->update([
                'last_submitted_at' => $project->last_submitted_at,
                'last_submitted_by' => $project->last_submitted_by,
            ]);

            // Update only the new attachments
            $projectDocument->project_attachments()
                ->whereNull('last_submitted_at')
                ->whereNull('last_submitted_by')
                ->update([
                    'last_submitted_at' => $project->last_submitted_at,
                    'last_submitted_by' => $project->last_submitted_by,
                ]);
        }

    }



    // check for current project document being reviewed and set the active current project 
    public static function setProjectReviewers(Project $project, $submission_type){


        if($submission_type == "submission"){ // first time submission

            // // set the initial reviewers 
            //     // Fetch all initial reviewers in order 
            //     $initial_reviewers = Reviewer::orderBy('order')
            //         ->where('reviewer_type', 'initial')
            //         ->get(); 
                        
            //     // set the inital reviewers for the project
            //     foreach( $initial_reviewers as $initial_reviewer){
            //         ProjectReviewer::create([
            //             'order' => $initial_reviewer->order,
            //             'review_status' => 'pending',
            //             'project_id' => $project->id,
            //             'user_id' => $initial_reviewer->user_id  ?? null,
            //             'created_by' => $project->created_by ?? auth()->id(),
            //             'updated_by' => $project->created_by ?? auth()->id(),
            //             'reviewer_type' => 'initial',  
            //         ]);
                    
            //     }
            // // ./ set the initial reviewers

            // set the document reviewers
                // check for the project documents
                if(!empty($project->project_documents) &&  $project->project_documents->count() > 0){
    
                    foreach($project->project_documents as $project_document){

                        // Fetch all reviewers in order that is part of the project document id 
                        $reviewers = Reviewer::orderBy('order')
                            ->where('document_type_id', $project_document->document_type_id)
                            ->get(); 
                        
                        // create the project reviewers 
                        foreach ($reviewers as $reviewer) {
                            ProjectReviewer::create([
                                'order' => $reviewer->order,
                                'review_status' => 'pending',
                                'project_id' => $project->id,
                                'user_id' => $reviewer->user_id  ?? null,
                                'created_by' => $project->created_by ?? auth()->id(),
                                'updated_by' => $project->created_by ?? auth()->id(),
                                'reviewer_type' => 'document',
                                'project_document_id' => $project_document->id, // id of the connected document 

                                'slot_type' => $reviewer->slot_type  ?? null,
                                'slot_role' => $reviewer->slot_role  ?? null,
                            ]);

                            
                        }


                        Project::resetCurrentProjectDocumentReviewersByDocument($project_document->document_type_id,$project->id); 
                            

                    }
                    
                }
            // ./ set the document reviewers 



            // // set the final reviewers 
            //     // Fetch all reviewers in order that is part of the project document id 
            //     $final_reviewers = Reviewer::orderBy('order')
            //         ->where('reviewer_type', 'final')
            //         ->get(); 
                        

            //     foreach( $final_reviewers as $final_reviewer){
            //         ProjectReviewer::create([
            //             'order' => $final_reviewer->order,
            //             'review_status' => 'pending',
            //             'project_id' => $project->id,
            //             'user_id' => $final_reviewer->user_id ?? null,
            //             'created_by' => $project->created_by ?? auth()->id(),
            //             'updated_by' => $project->created_by ?? auth()->id(),
            //             'reviewer_type' => 'final',  
            //         ]);
                    
            //     }
            // // ./ set the final reviewers



            // $project->resetCurrentProjectDocumentReviewers();
            

        }else{ // re-submission


            if(!empty($project->project_documents) && $project->project_documents->count() > 0){
 
                foreach($project->project_documents as $project_document){

  
                    // Fetch all reviewers in order that is part of the project document id 
                    $reviewers = Reviewer::orderBy('order')
                        ->where('document_type_id', $project_document->document_type_id)
                        ->get(); 
                    
                    foreach ($reviewers as $reviewer) {
                         

                        ProjectReviewer::firstOrCreate(
                            [
                                'project_id' => $project->id,
                                'user_id' => $reviewer->user_id,
                                'project_document_id' => $project_document->id,
                            ],
                            [
                                'order' => $reviewer->order,
                                'review_status' => 'pending',
                                'created_by' => $project->created_by ?? auth()->id(),
                                'updated_by' => $project->created_by ?? auth()->id(),
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

 
            }
            

            $project->resetCurrentProjectDocumentReviewers();



        }
 


    }

 





    // public static function getResubmissionErrorMessage(Project $project): ?string
    // {
    //     $result = self::canResubmit($project);

    //     if ($result['status']) {
    //         return null; // No error
    //     }

    //     return match ($result['reason']) {
    //         'project_updated' => 'Please make sure your updated changes are saved before resubmitting.',
    //         'new_document' => 'Add a new document before resubmitting.',
    //         'new_attachment' => 'Upload new attachments to existing documents before resubmitting.',
    //         'no_update' => 'You must update the project or upload new documents/attachments before resubmitting.',
    //         'never_submitted' => 'This project has never been submitted. Please proceed to submit.',
    //         default => 'Resubmission is not allowed at this time.',
    //     };
    // }



    // public static function canResubmit(Project $project): array
    // {
    //     $lastSubmitted = Carbon::parse($project->last_submitted_at);
    //     $current_reviewer = $project->getCurrentReviewer();

    //     $last_review = ProjectReviewer::getLastSubmitterReview($project, $current_reviewer); 

    //     // dd($last_review);


    //     if (!$lastSubmitted) {
    //         return ['status' => true, 'reason' => 'never_submitted'];
    //     }

    //     if ( $project->updated_at->gt($lastSubmitted->gt($lastSubmitted->addMinutes(2))) ) {
    //         return ['status' => true, 'reason' => 'project_updated'];
    //     }

    //     if ($project->project_documents()->where('created_at', '>', $lastSubmitted)->exists()) {
    //         return ['status' => true, 'reason' => 'new_document'];
    //     }

    //     if (
    //         $project->project_documents()->whereHas('project_attachments', function ($query) use ($lastSubmitted) {
    //             $query->where('created_at', '>', $lastSubmitted);
    //         })->exists()
    //     ) {
    //         return ['status' => true, 'reason' => 'new_attachment'];
    //     }

    //     // If none of the above
    //     return ['status' => false, 'reason' => 'no_update'];
    // }


    public static function getResubmissionErrorMessage(Project $project): ?string
    {
        $result = self::canResubmit($project);

        if (!empty($result['status'])) {
            return null; // ok to resubmit
        }

        $reason = $result['reason'] ?? 'default';

        switch ($reason) {
            case 'never_submitted':
                return 'This project has never been submitted. Please proceed to submit.';

            case 'project_required':
                return 'Please update the project details and save your changes before resubmitting.';

            case 'docs_required':
                $required_names = $result['required_document_type_names'] ?? [];
                $missing_names = $result['missing_document_type_names'] ?? [];

                if (!empty($required_names)) {
                    $message = 'Please add at least one new document for the following type(s): ' . implode(', ', $required_names) . '.';

                    if(!empty($missing_names)){
                        $message = 'Missing document(s): ' . implode(', ', $missing_names) . '.';

                    }

                    return $message;
                }
                 
                return 'Please add at least one new required document before resubmitting.';

            case 'attachments_required':
                $required_documents = $result['required_project_document_names'] ?? [];
                $missing_documents = $result['missing_project_document_names'] ?? [];

                $message = "";
                if (!empty($required_documents)) {
                    $message = 'Please add at least one new attachment for the following project document(s): ' . implode(', ', $required_documents) . '.';
 

                    if(!empty($missing_documents)){
                        $message = 'Not updated document(s): ' . implode(', ', $missing_documents) . '.';
                    }

                    return $message;
                }
                return 'Please upload at least one new attachment to the project documents before resubmitting.';

            case 'no_update':
                return 'You must update the project or upload new documents/attachments before resubmitting.';

            default:
                return 'Resubmission is not allowed at this time.';
        }
    }


    public static function canResubmit(Project $project): array
    {
        // If project has never been submitted
        if (empty($project->last_submitted_at)) {
            return ['status' => false, 'reason' => 'never_submitted'];
        }

        $lastSubmitted = Carbon::parse($project->last_submitted_at);
        $currentReviewer = $project->getCurrentReviewer(); // ProjectReviewer instance
        $lastReview = $currentReviewer
            ? ProjectReviewer::getLastSubmitterReview($project, $currentReviewer)
            : null;

        // If no prior review found, fall back to generic checks since user is trying to resubmit
        // "Updated project" means updated after last submission (with a 2-minute grace window)
        $grace = $lastSubmitted->copy()->addMinutes(2);
        $projectUpdated = $project->updated_at?->gt($grace) ?? false;

        $newDocExists = $project->project_documents()
            ->where('created_at', '>', $lastSubmitted)
            ->exists();

        $newAttachmentExists = $project->project_documents()
            ->whereHas('project_attachments', function ($q) use ($lastSubmitted) {
                $q->where('created_at', '>', $lastSubmitted);
            })
            ->exists();

        // If there was a review that explicitly required things, enforce those
        if ($lastReview) {
            $reviewCutoff = $lastReview->created_at ?? $lastSubmitted; // use review time as the requirement baseline

            // 1) Requires project update?
            if ($lastReview->requires_project_update) {
                $projectUpdatedSinceReview = $project->updated_at?->gt($reviewCutoff) ?? false;
                if (!$projectUpdatedSinceReview) {
                    return ['status' => false, 'reason' => 'project_required'];
                }
            }

            // 2) Requires document update?
            if ($lastReview->requires_document_update) {
                // Gather required document type IDs from the review
                $requiredTypeIds = $lastReview->required_document_updates()
                    ->pluck('document_type_id')
                    ->filter()
                    ->unique()
                    ->values();

                    // dd( $requiredTypeIds  );

                // If specific types were specified, enforce adding at least one new doc of those types
                if ($requiredTypeIds->isNotEmpty()) {
                     

                    // Collect which required types actually have a new doc since the cutoff
                    $addedTypeIds = $project->project_documents()
                        ->where('created_at', '>', $reviewCutoff)
                        ->whereIn('document_type_id', $requiredTypeIds)
                        ->pluck('document_type_id')
                        ->filter()
                        ->unique()
                        ->values();

                    // Figure out what's still missing
                    $missingTypeIds = $requiredTypeIds->diff($addedTypeIds);

                    if ($missingTypeIds->isNotEmpty()) {
                        return [
                            'status' => false,
                            'reason' => 'docs_required', // all specified types must be present
                            'required_document_type_names' => DocumentType::whereIn('id', $requiredTypeIds)->pluck('name')->all(),
                            'missing_document_type_names'  => DocumentType::whereIn('id', $missingTypeIds)->pluck('name')->all(),
                        ];
                    }

                    // All required types satisfied; continue...


                } else {
                    // No types specified; require at least one new document of any type
                    $hasNewDocSinceReview = $project->project_documents()
                        ->where('created_at', '>', $reviewCutoff)
                        ->exists();

                    if (!$hasNewDocSinceReview) {
                        return ['status' => false, 'reason' => 'docs_required'];
                    }
                }
            }

            // 3) Requires attachment update?
            if ($lastReview->requires_attachment_update) {


                // Gather required project documents for attachment update from the review
                $requiredProjectDocumentIds = $lastReview->required_attachment_updates()
                    ->pluck('project_document_id')
                    ->filter()
                    ->unique()
                    ->values();

 

                // if specific project documents are specified, enforce adding updated project attachments that is required to be updated for each project document
                if($requiredProjectDocumentIds->isNotEmpty()){


                    // $hasNewRequiredDocAttachUpdate = $project->attachments()
                    //     ->where('created_at', '>', $reviewCutoff)
                    //     ->whereIn('project_document_id', $requiredProjectDocumentIds)
                    //     ->exists();
 

                    // if (!$hasNewRequiredDocAttachUpdate) {
                    //     $names = DocumentType::whereIn('id', $requiredProjectDocumentTypeIds)->pluck('name')->all();
                    //     return [
                    //         'status' => false,
                    //         'reason' => 'attachments_required',
                    //         'required_document_type_names' => $names,
                    //     ];
                    // }


                    // Collect which required project document actually have a new attachment since the cutoff
                    $updatedProjectDocumentIds = $project->attachments()
                        ->where('created_at', '>', $reviewCutoff)
                        ->whereIn('project_document_id', $requiredProjectDocumentIds)
                        ->pluck('project_document_id')
                        ->filter()
                        ->unique()
                        ->values();

                    // Figure out what's still missing
                    $missingProjectDocumentIds = $requiredProjectDocumentIds->diff($updatedProjectDocumentIds);

                    if ($missingProjectDocumentIds->isNotEmpty()) {
                        $required_document_updates_names = [];
                        $missing_document_updates_names = [];

                        foreach($requiredProjectDocumentIds as $key => $required_document_id){
                            $project_document = ProjectDocument::find($required_document_id);
                            if(!empty($project_document->document_type->name)){
                                $required_document_updates_names[] = $project_document->document_type->name;
                            }
                             
                        }

                        foreach($missingProjectDocumentIds as $key => $missing_document_id){
                            $project_document = ProjectDocument::find($missing_document_id);
                            if(!empty($project_document->document_type->name)){
                                $missing_document_updates_names[] = $project_document->document_type->name;
                            }
                             
                        }


                        return [
                            'status' => false,
                            'reason' => 'attachments_required', // all specified types must be present
                            'required_project_document_names' => $required_document_updates_names,
                            'missing_project_document_names'  => $missing_document_updates_names,
                        ];
                    }

                    // All required project document attachments satisfied; continue...




                }else{

                    $hasNewAttachmentSinceReview = $project->project_documents()
                        ->whereHas('project_attachments', function ($q) use ($reviewCutoff) {
                            $q->where('created_at', '>', $reviewCutoff);
                        })
                        ->exists();

                    if (!$hasNewAttachmentSinceReview) {
                        return ['status' => false, 'reason' => 'attachments_required'];
                    }
                }

 
                
            }

            // If all explicit requirements from the last review are satisfied → allow resubmit
            return ['status' => true, 'reason' => 'ok'];
        }

        // If no explicit review requirements, allow resubmit when *something* changed since last submission
        if ($projectUpdated || $newDocExists || $newAttachmentExists) {
            return ['status' => true, 'reason' => 'ok'];
        }

        return ['status' => false, 'reason' => 'no_update'];
    }







 
 
 
    /** Project Submission restriction  */
    public static function checkProjectRequirements()
    {
         
        $projectTimer = ProjectTimer::first();

        // DocumentTypes that don't have any reviewers
        $documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray() ?? [];

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

            // dd($openTime);

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

        
        $project_creator = User::find($project->created_by);

        if (!empty($reviewer->user_id)) {


            $user = User::find($reviewer->user_id);

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



                $name = !empty(Auth::user()) ? Auth::user()->name :  $project_creator->name;
                
                // Message for the subscribers
                $message = "The project '" . $project->name . "' has been submitted by '" . $name. "'";

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


                $name = !empty(Auth::user()) ? Auth::user()->name :  $project_creator->name;


                //message for the subscribers 
                $message = "The project '".$project->name."' had been re-submitted by '".$name ."'";
            


            }

            
            
 

        }elseif(!empty($reviewer)){



            // Determine users based on reviewer type
            $reviewerType = $reviewer->reviewer_type; // assuming $reviewer is available

            if (in_array($reviewerType, ['initial', 'final'])) {
                $users = \Spatie\Permission\Models\Permission::whereIn('name', [
                    'system access admin',
                    'system access global admin',
                ])
                ->with('roles.users')
                ->get()
                ->flatMap(function ($permission) {
                    return $permission->roles->flatMap(function ($role) {
                        return $role->users;
                    });
                })->unique('id')->values();
            } elseif ($reviewerType === 'document') {
                $users = \Spatie\Permission\Models\Permission::whereIn('name', [
                    'system access reviewer',
                    'system access admin',
                    'system access global admin',
                ])
                ->with('roles.users')
                ->get()
                ->flatMap(function ($permission) {
                    return $permission->roles->flatMap(function ($role) {
                        return $role->users;
                    });
                })->unique('id')->values();
            } else {
                $users = collect(); // fallback to empty if reviewer_type is unknown
            }




            
            foreach ($users as $user) {
                try {
                    Notification::send($user, new ProjectOpenReviewNotification($project,$reviewer));
                } catch (\Throwable $e) {
                    Log::error('Failed to send ProjectOpenReviewNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                try {
                    Notification::send($user, new ProjectOpenReviewNotificationDB($project,$reviewer));
                } catch (\Throwable $e) {
                    Log::error('Failed to send ProjectOpenReviewNotificationDB notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }




            $name = !empty(Auth::user()) ? Auth::user()->name :  $project_creator->name;
            
            // Message for the subscribers
            $message = "The project '" . $project->name . "' has been submitted by '" . $name. "'";

            



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
                     


 
                }else{
                    // if there is no user in project reviewer record, meaning this is an open review 




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


    // notify creator, project reviewers and project subscribers 
    public static function notifyRevs_Subs_on_RevUpd($project, $project_document_id, $reviewer_type)
    {

        $project_document = ProjectDocument::where('id', $project_document_id)->first();


        //get all the project reviewers of the project within that project document id
        $project_reviewers = ProjectReviewer::where('project_document_id', $project_document_id)
            ->where('project_id',$project->id)
            ->where('reviewer_type', $reviewer_type)
            ->get();
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
                        Notification::send($user, new ProjectReviewerUpdatedByDocument($project, $user,$project_document));
                    } catch (\Throwable $e) {
                        // Log the error without interrupting the flow
                        Log::error('Failed to send ProjectReviewerUpdatedByDocument notification: ' . $e->getMessage(), [
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
                     


 
                }else{
                    // if there is no user in project reviewer record, meaning this is an open review 




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


    // notification to send to the admin reviewer about open project review notification
    public static function sendForReviewersOpenProjectReviewNotification(Project $project, ProjectReviewer $reviewer){






        // Determine users based on reviewer type
            $reviewerType = $reviewer->reviewer_type; // assuming $reviewer is available

            // if (in_array($reviewerType, ['initial', 'final'])) {
            //     $users = \Spatie\Permission\Models\Permission::whereIn('name', [
            //         'system access admin',
            //         'system access global admin',
            //     ])
            //     ->with('roles.users')
            //     ->get()
            //     ->flatMap(function ($permission) {
            //         return $permission->roles->flatMap(function ($role) {
            //             return $role->users;
            //         });
            //     })->unique('id')->values();
            // } elseif ($reviewerType === 'document') {
            //     $users = \Spatie\Permission\Models\Permission::whereIn('name', [
            //         'system access reviewer',
            //         'system access admin',
            //         'system access global admin',
            //     ])
            //     ->with('roles.users')
            //     ->get()
            //     ->flatMap(function ($permission) {
            //         return $permission->roles->flatMap(function ($role) {
            //             return $role->users;
            //         });
            //     })->unique('id')->values();
            // } else {
            //     $users = collect(); // fallback to empty if reviewer_type is unknown
            // }

             $users = \Spatie\Permission\Models\Permission::whereIn('name', [
                    // 'system access reviewer',
                    'system access admin',
                    // 'system access global admin',
                ])
                ->with('roles.users')
                ->get()
                ->flatMap(function ($permission) {
                    return $permission->roles->flatMap(function ($role) {
                        return $role->users;
                    });
                })->unique('id')->values();




            
            foreach ($users as $user) {
                try {
                    Notification::send($user, new ProjectOpenReviewNotification($project,$reviewer));
                } catch (\Throwable $e) {
                    Log::error('Failed to send ProjectOpenReviewNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                try {
                    Notification::send($user, new ProjectOpenReviewNotificationDB($project,$reviewer));
                } catch (\Throwable $e) {
                    Log::error('Failed to send ProjectOpenReviewNotificationDB notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'user_id' => $user->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }
            }













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




    // send notification regarding complete project aproval notification
    public static function sendCompleteProjectApprovalNotification(Project $project){

        // send notification to the creator
        if($project->status == "approved"){
            $creator = User::where('id',$project->created_by)->first();

            try {
                // notify the next reviewer
                Notification::send($creator, new ProjectCompleteApprovalNotification(  $project  )); 
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send ProjectCompleteApprovalNotification notification: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            } 
        }


        // send notification to project subscribers  
        $message = "The project '".$project->name."' had completed the review approval process ";
        ProjectHelper::sendForProjectSubscribersProjectSubscribersNotification($project,  $message, "project_approved");


        // project_approved


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


    // check if user is reviewer of the project
    public static function checkIfUserIsProjectReviewer($project_id){

         
        $user = Auth::user();
        // Check if the user has the role "system access global admin" OR the permission "system access reviewer"
        if (!$user || (!$user->can('system access global admin') && !$user->hasPermissionTo('system access reviewer'))) {
            return false;
        }


 
        $project = Project::findOrFail($project_id);


        
        // check if the user is a reviewer 
        $isReviewer = $project->project_reviewers()->where('user_id', auth()->id())->exists();

        if(!$isReviewer){
            return false;
        }



        return true;
 

    }



    public static function returnHomeRouteBasedOnProject(Project $project){


        if($project->created_by == Auth::user()->id && Auth::user()->can('project list view')){

            return redirect()->route('project.index');
        }elseif(Auth::user()->can('project list view all') || Auth::user()->can('system access global admin')){
            return redirect()->route('project.index.all');
        }elseif(Auth::user()->can('project list view all no drafts')){
            return redirect()->route('project.index.all.no-drafts');
        }{
            return redirect()->route('dashboard');
        }   



    }

    public static function returnHomeRouteBasedOnRoute(string $route){

        

    }



    // return home route for Livewire classes
    // return home route for Livewire classes
    public static function returnHomeProjectRoute(?Project $project = null, array $params = []): string
    {
        $user = Auth::user();


        //  dd($project);

        // If we have a project and caller didn't explicitly set 'project',
        // add it as an optional parameter (will become a query string if
        // the route doesn't have {project} in the URI).
        // if ($project && ! array_key_exists('project', $params)) {
        //     $params['project'] = $project->id;
        // }


        // dd($params['project']);


        // Decide which route name to use
        if ($project) {

            // dd($project);

            if ($project->created_by == $user->id && $user->can('project list view')) {
                // dd(1);
                $routeName = 'project.index';

            } elseif ($user->can('project list view all') || $user->can('system access global admin')) {
                // dd(2);
                $routeName = 'project.index.all';

            } elseif ($user->can('project list view all no drafts')) {
                // dd(3);
                $routeName = 'project.index.all.no-drafts';

            } else {
                // dd(vars: 4);
                $routeName = 'dashboard';
            }

        } else {

            if ($user->can('project list view all') || $user->can('system access global admin')) {

                $routeName = 'project.index.all';

            } elseif ($user->can('project list view all no drafts')) {

                $routeName = 'project.index.all.no-drafts';

            } elseif ($user->can('project list view')) { // for pure users only

                $routeName = 'project.index';

            } else {

                $routeName = 'dashboard';
            }
        }

        return route($routeName, $params);
    }




     // return home route for Livewire classes
    // return home route for Livewire classes
    public static function returnHomeProjectRouteFromId(int $projectId, array $params = []): string
    {
        $user = Auth::user();
        $project = Project::find($projectId);

        //  dd($project);

        // If we have a project and caller didn't explicitly set 'project',
        // add it as an optional parameter (will become a query string if
        // the route doesn't have {project} in the URI).
        // if ($project && ! array_key_exists('project', $params)) {
        //     $params['project'] = $project->id;
        // }


        // dd($params['project']);


        // Decide which route name to use
        if ($project) {

            // dd($project);

            if ($project->created_by == $user->id && $user->can('project list view')) {
                // dd(1);
                $routeName = 'project.index';

            } elseif ($user->can('project list view all') || $user->can('system access global admin')) {
                // dd(2);
                $routeName = 'project.index.all';

            } elseif ($user->can('project list view all no drafts')) {
                // dd(3);
                $routeName = 'project.index.all.no-drafts';

            } else {
                // dd(vars: 4);
                $routeName = 'dashboard';
            }

        } else {

            if ($user->can('project list view all') || $user->can('system access global admin')) {

                $routeName = 'project.index.all';

            } elseif ($user->can('project list view all no drafts')) {

                $routeName = 'project.index.all.no-drafts';

            } elseif ($user->can('project list view')) { // for pure users only

                $routeName = 'project.index';

            } else {

                $routeName = 'dashboard';
            }
        }

        return route($routeName, $params);
    }


  





    // reviewer admin list updated to notify creator, project reviewers and project subscribers 
    public static function notifyReviewersAndSubscribersOnAdminReviewerUpdate($project, $user)
    {

        // project is the project update
        // user is the user that will recieve the email

 
  
        $user = User::where('id',$user->id)->first();

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

            // if the user is the current reviewer user_id, send a review notification   
            if ($current_reviewer->user_id ==  $user->id) {

                try {
                        // Send email   notification to reviewer
                    Notification::send($user, new ProjectReviewNotification($project, $current_reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewNotification notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }

                try {
                        // Send   DB notification to reviewer
                    Notification::send($user, new ProjectReviewNotificationDB($project, $current_reviewer));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to send ProjectReviewNotificationDB notification: ' . $e->getMessage(), [
                        'project_id' => $project->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
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





    static public function updateTitleAndSub($route)
    {   
        $title =  "";
        switch ($route) {
            case 'project.index':
                $title = 'My Projects';
                  
                break;

            case 'project.index.update-pending':
                $title = 'My Projects - Update Pending';
                 
                break;

            case 'project.index.review-pending':
                $title = 'My Projects - Review Pending';
                 
                break;

            case 'project.index.all':
                $title = 'All Projects';
                 
                break;

            case 'project.index.update-pending.all':
                $title = 'All Projects - Update Pending';
                 
                break;

            case 'project.index.review-pending.all':
                $title = 'All Projects - Review Pending';
                 
                break;



            case 'project.index.all.no-drafts':
                $title = 'All Projects (No Drafts)';
                 
                break;

            case 'project.index.update-pending.all-linked':
                $title = 'Linked Projects - Update Pending';
                 
                break;

            case 'project.index.review-pending.all-linked':
                $title = 'Linked Projects - Review Pending';
                 
                break;

            case 'project.index.open-review':
                $title = 'Open Review projects';
                 
                break;  

            

            default:
                $title = 'Projects';
                 
                break;
        }

        return $title;
    }













    static public function delete($id){
 
        $project = Project::find($id);

        if( Auth::user()->can('system access global admin') || Auth::user()->can('system access admin') || Auth::user()->can('project delete override')  ){
        }else{
 
            if($project->status !== "draft"  ){
                Alert::error('Error','Project is not draft. It cannot be deleted. Please contact administrator if you want to delete the project ');

                $route = ProjectHelper::returnHomeProjectRoute($project);

                return redirect($route);
            }

            
        }
            


        // $project->status == "draft" && Auth::user()->id == $project->created_by




        // delete project connected records 
        
            //delete project reviewers 
            if(!empty($project->project_reviewers)){
                foreach($project->project_reviewers as $reviewer){
                    $reviewer->delete();
                } 
            }

            //delete project reviews 
            if(!empty($project->project_reviews)){
                foreach($project->project_reviews as $review){
                    $review->delete();
                } 
            }

            //delete project documents 
            if(!empty($project->project_documents)){
                foreach($project->project_documents as $document){

                    // delete project attachments for each project document 
                    if(!empty($document->project_attachments)){
                        foreach($document->project_attachments as $attachment){
                            // Construct the full file path
                            $filePath = "public/uploads/project_attachments/{$attachment->attachment}";

                            // Check if the file exists in storage and delete it
                            if (Storage::exists($filePath)) {
                                Storage::delete($filePath);
                            }

                            // Delete the record from the database
                            $attachment->delete();
                        }


                    }



                    $document->delete();
                } 
            }

            // delete project subscribers
            if(!empty($project->project_subscribers)){
                foreach($project->project_subscribers as $subcriber){
                    $subcriber->delete();
                } 
            }
        
        // ./ delete project connected records 




        // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = ProjectLogHelper::getActivityMessage('deleted', $project->id, $authId);

            // get the route
            $route = ProjectLogHelper::getRoute('deleted', $project->id);

            // log the event 
            event(new ProjectLogEvent(
                $message ,
                $authId, 
                // $project->id ?? null,

            ));
    
            /** send system notifications to users */
                
                ProjectNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                );

            /** ./ send system notifications to users */
        // ./ logging and system notifications




        $project->delete();


        // ActivityLog::create([
        //     'log_action' => "Project \"".$project->name."\" deleted ",
        //     'log_username' => Auth::user()->name,
        //     'created_by' => Auth::user()->id,
        // ]);




        // Alert::success('Success','Project deleted successfully');
        // return redirect()->route('project.index');

        // if($project->created_by == auth()->user()->id){
        //     return redirect()->route('project.index');

        // }else{

        //     return redirect()->route('project.index');
        // }


 


        // return ProjectHelper::returnHomeRouteBasedOnProject($project);
        $route = ProjectHelper::returnHomeProjectRoute($project);

        return redirect($route)
            ->with('alert.success',$message)
        ; 


 

    }


    public static function open_review_project(int $project_id, ?string $url = null)
    {
        // Load the project document or fail cleanly
        $project = Project::findOrFail($project_id);

        // Get the current authenticated user
        $userId = Auth::id();

        // Create/open the review session for this user
        ProjectHelper::createReviewSession($project->id, $userId);
 
        // If a custom URL is provided, use it
        if (!empty($url)) {
            // you can use redirect()->to() if $url is a full URL
            return redirect()->to($url);
            // or if $url is a named route, use:
            // return redirect()->route($url);
        }

        // Alert::success('Success','Project review claimed successfully');
        $message = 'Project review claimed successfully';

        

         // logging and system notifications
            $authId = Auth::check() ? Auth::id() : null;

            // get the message from the helper 
            $message = ProjectLogHelper::getActivityMessage('open-review-claimed', $project->id, $authId);

            // get the route
            $route = ProjectLogHelper::getRoute('open-review-claimed', $project->id);

            // log the event 
            event(new ProjectLogEvent(
                $message ,
                $authId, 
                $project->id,

            ));
    
            /** send system notifications to users */
                
                ProjectNotificationHelper::sendSystemNotification(
                    message: $message,
                    route: $route 
                ); 


            /** ./ send system notifications to users */

            // send system notification to the project creator
                // this is to notify the user that his project is now under review
                $customIds = [];
                $customIds[] = $project->created_by;

                // get the message from the helper 
                $message = ProjectLogHelper::getActivityMessage('your-open-review-claimed', $project->id, $authId);

                // get the route
                $route = ProjectLogHelper::getRoute('your-open-review-claimed', $project->id);


                ProjectNotificationHelper::sendSystemNotificationCustomIds(
                    message: $message,
                    route: $route,
                    customIds: $customIds
                );



            // ./ send system notification to the project creator

        // ./ logging and system notifications






        // Default redirect to the project document review route
        return redirect()->route('project.review',['project' => $project->id])
        ->with('alert.success',$message)
        ;
    }


   /**
     * Create a session key for an accepted review.
     *
     * @param  int|string  $project_id
     * @param  int|null    $userId
     * @return void
     */
    public static function createReviewSession($project_id, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new Exception('User must be authenticated to create review session.');
        }

        $key = "project.review.accepted.$project_id";

        $hash = hash('sha256', "accepted_by_{$userId}");

        session([
            $key => [
                'user_id' => $userId,
                'hash'    => $hash,
                'time'    => now()->toDateTimeString(),
            ],
        ]);
    }



    /**
     * Verify and clear the session for a review.
     *
     * @param  int|string  $project_id
     * @param  int|null    $userId
     * @param  int         $expiryMinutes  (optional) time validity in minutes
     * @return bool
     */
    public static function verifyAndClearReviewSession($project_id, $userId = null, $expiryMinutes = 10)
    {
        $userId = $userId ?? Auth::id();
        $key = "project.review.accepted.$project_id";
        $data = session($key);

        // Always clear after attempting to use it
        session()->forget($key);

        if (!$data) {
            return false;
        }

        // Check expiry
        $created = Carbon::parse($data['time']);
        if ($created->diffInMinutes(now()) > $expiryMinutes) {
            return false; // expired
        }

        // Validate hash
        $expected = hash('sha256', "accepted_by_{$userId}");
        if (!hash_equals($expected, $data['hash'])) {
            return false;
        }

        return true;
    }



     /** Project Submission restriction  */
    public static function checkProjectDocumentRequirements()
    {
         
        $projectTimer = ProjectTimer::first();

        // DocumentTypes that don't have any reviewers
        $documentTypesWithoutReviewers = DocumentType::whereDoesntHave('reviewers')->pluck('name')->toArray();


        // dd( $documentTypesWithoutReviewers);

        // Check if all document types have at least one reviewer
        $allDocumentTypesHaveReviewers = empty($documentTypesWithoutReviewers);

        // Check if there are reviewers by type
        // $hasInitialReviewers = Reviewer::where('reviewer_type', 'initial')->exists();
        // $hasFinalReviewers = Reviewer::where('reviewer_type', 'final')->exists(); 
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
            'documentTypesWithoutReviewers' => $documentTypesWithoutReviewers,
            // 'no_initial_reviewers' => !$hasInitialReviewers,
            // 'no_final_reviewers' => !$hasFinalReviewers,
        ];
    } 
}
