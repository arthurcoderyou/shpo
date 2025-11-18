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
use App\Helpers\ProjectReviewerHelpers;
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Events\ProjectDocument\Review\Reviewed;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Notifications\ProjectOpenReviewNotification;
use App\Notifications\ProjectSubscribersNotification;
use App\Events\ProjectDocument\Review\ReviewSubmitted;
use App\Notifications\ProjectOpenReviewNotificationDB;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectCompleteApprovalNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;
use App\Notifications\ProjectDocumentCompleteApprovalNotification;
 



class ProjectDocumentHelpers
{


    public static function submit_project_document($project_document_id, $override_on_que_submission = false){
        
 
        $errors = ProjectDocumentHelpers::checkProjectDocumentRequirements();
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
                    case 'document_types_missing_reviewers':
                        // $message = 'Some document types have no project reviewers assigned. Please wait for the administrator to set them up. ';
                        $message = "Missing reviewers for document/s: ";
                        $message .= implode(', ', $errors['documentTypesWithoutReviewers']);
                        $errorMessages[] = $message;
                        break; 
                }
            }
        }
  

        $projectTimer = ProjectTimer::first();
        $isProjectSubmissionAllowed = true;
        $queuedForNextDay = false;
          
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


            // dd("true");



        }
 
        $project_document = ProjectDocument::find($project_document_id);
        $project = Project::find($project_document->project_id);
         

        if (!empty($errorMessages)) {

            // dd($errorMessages);

            $message = 'The project document cannot be submitted because: ';
            $message .= implode(', ', $errorMessages);
            $message .= '. Please wait for the admin to configure these settings.';
            Alert::error('Error', $message);


            // if($project->created_by == Auth::id()){ 
            //     return redirect()->route('project.index');

            // }else{ 

            //     // return redirect()->route('project.index');
            //     return ProjectHelper::returnHomeRouteBasedOnProject($project);
 
            // }

            return redirect()->route('project.project-document.show',[
                'project' => $project->id,
                'project_document' => $project_document->id
            ]);
 
            
        }
 

        // check if there are existing project reviewers 
        if(Reviewer::count() == 0){
            Alert::error('Error','Project reviewers are not added yet to the system');

            // if($project->created_by == Auth::id()){ 
            //     return redirect()->route('project.project_documents',['project' => $project]);

            // }else{ 

            //     // return redirect()->route('project.index');
            //     return ProjectHelper::returnHomeRouteBasedOnProject($project);

            // }

            return redirect()->route('project.project-document.show',[
                'project' => $project->id,
                'project_document' => $project_document->id
            ]);

             

        }
 
        // dd($project->project_documents->count());
        $attachmemts = $project_document->project_attachments->count(); 
        
        // check if there are existing project documents as it is required 
        if($attachmemts == 0){
            Alert::error('Error','There must be atleast one project attachment to the submitted project document'); 
  
            return redirect()->route('project.project-document.show',[
                'project' => $project->id,
                'project_document' => $project_document->id
            ]);

        }
 

        $submission_type = "initial_submission";


        // dd("All Goods");

        
        // if override admin que submit is true
        if($override_on_que_submission){ 
            $submission_type = "initial_submission";
            
            // update project document
            ProjectDocumentHelpers::updateDocumentAndAttachments(
                $project_document,
                "submitted", 
                false
            );

        }else{ 

            // SUBMISSION OF NEW PROJECT DOCUMENTS 
            // if the project document is a draft, create the default values
            if($project_document->status == "draft"){

                $submission_type = "initial_submission"; 

                // check if project document submission restriction is on
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

                    $message = "Project document submission is currently restricted. Your project has been queued and will be submitted automatically on $nextAvailableDay between $formattedOpen and $formattedClose.";

                      
                    // update project document
                    ProjectDocumentHelpers::updateDocumentAndAttachments(
                        $project_document,
                        "on_que", 
                        false
                    );



                    Alert::info('Project Document Notice',$message); 
                    return redirect()->route('project.project-document.show',[
                        'project' => $project->id,
                        'project_document' => $project_document->id
                    ]);



                }else{
                    // else, submit normally

                    // update project document
                    ProjectDocumentHelpers::updateDocumentAndAttachments(
                        $project_document,
                        "submitted", 
                        false
                    );

 

                }

    
                
                // ProjectHelper::setProjectReviewers($project,$submission_type);

                // $reviewer = $project->getNextReviewer();

                // ProjectHelper::notifyReviewersAndSubscribers($project, $reviewer, $submission_type);

            // RESUBMISSION OF PROJECT DOCUMENTS
            }else{ // if not, get the current reviewer


                // condition for resubmission
                $errorMessage = ProjectDocumentHelpers::getResubmissionErrorMessage($project_document);

                if ($errorMessage) {
                    Alert::error('Error', $errorMessage);
 
                    return redirect()->route('project.project-document.show',[
                        'project' => $project->id,
                        'project_document' => $project_document->id
                    ]);
                }



                // dd("Resubmission is good");


                // update the current project reviewer 
                // for resubmission, make the current reviewer review_status into pending;

                $current_reviewer = $project_document->getCurrentReviewer();
                $current_reviewer->review_status = "pending";
                $current_reviewer->save();

 
                $submission_type = "supplemental_submission";
                
                 
                // update project document
                ProjectDocumentHelpers::updateDocumentAndAttachments(
                $project_document,
                "submitted", 
                false
                );

                 

            } 
        }


        // if all are good, then we move on to the setup of the document reviewers 
        
        if($submission_type == "initial_submission"){

            // set the project document reviewers
            ProjectReviewerHelpers::setProjectDocumentReviewers($project_document, $submission_type);


            try {
                event(new \App\Events\ProjectDocument\Submitted(
                    $project_document->id, 
                    Auth::user()->id,
                    true,
                    true));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch Submitted event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }


            // set the project document reviewers
            ProjectReviewerHelpers::sendNotificationOnReviewerListUpdate($project_document);

            // send notification to the current reviewer
            ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document, $submission_type);



        }else{  // $submission_type = "supplemental_submission";


            // set the project document reviewers
            ProjectReviewerHelpers::setProjectDocumentReviewers($project_document, $submission_type);

            
            try {
                event(new \App\Events\ProjectDocument\Submitted(
                    $project_document->id, 
                    Auth::user()->id,
                    true,
                    true));
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to dispatch Submitted event: ' . $e->getMessage(), [
                    'project_id' => $project->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            }

 
            // send notification to the current reviewer
            ProjectReviewerHelpers::sendReviewNotificationToReviewer($project_document,$submission_type);


        }


        // update project submission and review timers 
        $project_timer = ProjectTimer::first();

        if(!empty($project_timer)){
            $reviewer_response_duration =  $project_timer->reviewer_response_duration ?? null;
            $reviewer_response_duration_type =  $project_timer->reviewer_response_duration_type ?? null;
            $submitter_response_duration =   $project_timer->submitter_response_duration ?? null;
            $submitter_response_duration_type = $project_timer->submitter_response_duration_type ?? null;


            $project_document->reviewer_response_duration = $reviewer_response_duration;
            $project_document->reviewer_response_duration_type = $reviewer_response_duration_type;
            // after updating the project, update the due date timers
            $project_document->reviewer_due_date = Project::calculateDueDate(now(),$reviewer_response_duration_type, $reviewer_response_duration );


            $project_document->submitter_response_duration = $submitter_response_duration;
            $project_document->submitter_response_duration_type = $submitter_response_duration_type;  
            // $project_document->submitter_due_date = Project::calculateDueDate(now(),$project_document->submitter_response_duration_type, $project_document->submitter_response_duration );
            $project_document->submitter_due_date = Project::calculateDueDate(now(),$submitter_response_duration_type, $submitter_response_duration );
    
            $project_document->save();


        }



        



 
        Alert::success('Success','Project submitted successfully');
 

        return redirect()->route('project.project-document.show',[
            'project' => $project->id,
            'project_document' => $project_document->id
        ]);
        
            
        

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



    public static function updateDocumentAndAttachments(ProjectDocument $project_document, $status, $allow_project_submission){
        
        // get the date time now
        $now = now();


        $project = Project::find($project_document->project_id);
        // update project
        // override submit the project 
        $project->status = $status;
        $project->allow_project_submission = $allow_project_submission; // do not allow double submission until it is reviewed 
        $project->updated_at = $now;
        $project->last_submitted_at = $now;
        $project->last_submitted_by = Auth::user()->id ?? $project->created_by;
        $project->save();
 

        // update project document
        // override submit the project document
        $project_document->status = $status;
        $project_document->allow_project_submission = $allow_project_submission; // do not allow double submission until it is reviewed
        $project_document->updated_at = $now;
        $project_document->last_submitted_at = $now;
        $project_document->last_submitted_by = Auth::user()->id ?? $project->created_by;
        $project_document->save(); 
 

        // Update only the new attachments
        $project_document->project_attachments()
            ->whereNull('last_submitted_at')
            ->whereNull('last_submitted_by')
            ->update([
                'last_submitted_at' => $project_document->last_submitted_at,
                'last_submitted_by' => $project_document->last_submitted_by,
            ]);
         

    }



    public static function getResubmissionErrorMessage(ProjectDocument $project_document): ?string
    {
        $result = self::canResubmit($project_document);

        if (!empty($result['status'])) {
            return null; // ok to resubmit
        }

        $reason = $result['reason'] ?? 'default';

        switch ($reason) {
            case 'never_submitted':
                return 'This project docuemnt has never been submitted. Please proceed to submit.';

            case 'project_required':
                return 'Please update the project document details and save your changes before resubmitting.';

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

    public static function canResubmit(ProjectDocument $project_document): array
    {
        // If project document has never been submitted
        if (empty($project_document->last_submitted_at)) {
            return ['status' => false, 'reason' => 'never_submitted'];
        }

        $lastSubmitted = Carbon::parse($project_document->last_submitted_at);

        $currentReviewer = $project_document->getCurrentReviewerByProjectDocument(); // ProjectReviewer instance

        // dd($currentReviewer );


        $lastReview = $currentReviewer
            ? ProjectReviewer::getLastSubmitterDocumentReview($project_document, $currentReviewer)
            : null;

        // dd($lastReview);

        // If no prior review found, fall back to generic checks since user is trying to resubmit
        // "Updated project" means updated after last submission (with a 2-minute grace window)
        $grace = $lastSubmitted->copy()->addMinutes(2);
        $projectDocumentUpdated = $project_document->updated_at?->gt($grace) ?? false;

        // $newDocExists = $project_document->project_documents()
        //     ->where('created_at', '>', $lastSubmitted)
        //     ->exists();

        $newAttachmentExists = $project_document
            ->whereHas('project_attachments', function ($q) use ($lastSubmitted) {
                $q->where('created_at', '>', $lastSubmitted);
            })
            ->exists();

        // If there was a review that explicitly required things, enforce those
        if ($lastReview) {
            $reviewCutoff = $lastReview->created_at ?? $lastSubmitted; // use review time as the requirement baseline

            // 1) Requires project update?
            if ($lastReview->requires_project_update) {
                $projectUpdatedSinceReview = $project_document->updated_at?->gt($reviewCutoff) ?? false;
                if (!$projectUpdatedSinceReview) {
                    return ['status' => false, 'reason' => 'project_required'];
                }
            }

            // 2) Requires document update?
            // if ($lastReview->requires_document_update) {
            //     // Gather required document type IDs from the review
            //     $requiredTypeIds = $lastReview->required_document_updates()
            //         ->pluck('document_type_id')
            //         ->filter()
            //         ->unique()
            //         ->values();

            //         // dd( $requiredTypeIds  );

            //     // If specific types were specified, enforce adding at least one new doc of those types
            //     if ($requiredTypeIds->isNotEmpty()) {
                     

            //         // Collect which required types actually have a new doc since the cutoff
            //         $addedTypeIds = $project->project_documents()
            //             ->where('created_at', '>', $reviewCutoff)
            //             ->whereIn('document_type_id', $requiredTypeIds)
            //             ->pluck('document_type_id')
            //             ->filter()
            //             ->unique()
            //             ->values();

            //         // Figure out what's still missing
            //         $missingTypeIds = $requiredTypeIds->diff($addedTypeIds);

            //         if ($missingTypeIds->isNotEmpty()) {
            //             return [
            //                 'status' => false,
            //                 'reason' => 'docs_required', // all specified types must be present
            //                 'required_document_type_names' => DocumentType::whereIn('id', $requiredTypeIds)->pluck('name')->all(),
            //                 'missing_document_type_names'  => DocumentType::whereIn('id', $missingTypeIds)->pluck('name')->all(),
            //             ];
            //         }

            //         // All required types satisfied; continue...


            //     } else {
            //         // No types specified; require at least one new document of any type
            //         $hasNewDocSinceReview = $project->project_documents()
            //             ->where('created_at', '>', $reviewCutoff)
            //             ->exists();

            //         if (!$hasNewDocSinceReview) {
            //             return ['status' => false, 'reason' => 'docs_required'];
            //         }
            //     }
            // }

            // 3) Requires attachment update?
            if ($lastReview->requires_attachment_update) {


                // Gather required project documents for attachment update from the review
                $requiredProjectDocumentIds = $lastReview->required_attachment_updates()
                    ->pluck('project_document_id')
                    ->filter()
                    ->unique()
                    ->values();

 

                // if specific project documents are specified, enforce adding updated project attachments that is required to be updated for each project document
                // if($requiredProjectDocumentIds->isNotEmpty()){


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
                    $updatedProjectDocumentIds = $project_document->project_attachments()
                        ->where('created_at', '>', $reviewCutoff)
                        // ->whereIn('project_document_id', $requiredProjectDocumentIds)
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




                // }else{

                //     $hasNewAttachmentSinceReview = $project->project_documents()
                //         ->whereHas('project_attachments', function ($q) use ($reviewCutoff) {
                //             $q->where('created_at', '>', $reviewCutoff);
                //         })
                //         ->exists();

                //     if (!$hasNewAttachmentSinceReview) {
                //         return ['status' => false, 'reason' => 'attachments_required'];
                //     }
                // }

 
                
            }

            // If all explicit requirements from the last review are satisfied → allow resubmit
            return ['status' => true, 'reason' => 'ok'];
        }

         
        // If no explicit review requirements, allow resubmit when *something* changed since last submission
        if ($projectDocumentUpdated ||  $newAttachmentExists) {
            return ['status' => true, 'reason' => 'ok'];
        }

        return ['status' => false, 'reason' => 'no_update'];
    }





    public static function notifySubmitter($review_id, $notify_user_id){
        $auth = Auth::user();

        try {
            // send notification update about reviewed project document update to submitter
            event(new Reviewed(
            $review_id,
            $notify_user_id, 
            $auth->id, 
            true, 
            true));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send Reviewed event: ' . $e->getMessage(), [
                'review_id' => $review_id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
 

         

    }



    public static function notifyReviewSubmitter($review_id, $notify_user_id){
        $auth = Auth::user();

        try {
            // send notification update to the review submitter about the success on submission
            event(new  ReviewSubmitted(
            $review_id,
            $notify_user_id, 
            $auth->id, 
            true, 
            true));
        } catch (\Throwable $e) {
            // Log the error without interrupting the flow
            Log::error('Failed to send  ReviewSubmitted event: ' . $e->getMessage(), [
                'review_id' => $review_id,
                'trace' => $e->getTraceAsString(),
            ]);
        }
 

         

    }




    // notify creator, project reviewers and project subscribers 
    public static function notifyRevs_Subs_on_RevUpd($project_document_id)
    {

        $project_document = ProjectDocument::where('id', $project_document_id)->first();
        $project = Project::find($project_document->project_id) ;


        // dd($project_document);

        //get all the project reviewers of the project within that project document id
        $project_reviewers = $project_document->project_reviewers();
        // dd($project_reviewers);



        // notify the creator about the project reviewer update 
        $creator = User::where('id',$project_document->created_by)->first();
 
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

   public static function open_review_project_document(int $project_document_id, ?string $url = null)
    {
        // Load the project document or fail cleanly
        $project_document = ProjectDocument::findOrFail($project_document_id);

        // Get the current authenticated user
        $userId = Auth::id();

        // Create/open the review session for this user
        ProjectDocumentHelpers::createReviewSession($project_document->id, $userId);










        
        // If a custom URL is provided, use it
        if (!empty($url)) {
            // you can use redirect()->to() if $url is a full URL
            return redirect()->to($url);
            // or if $url is a named route, use:
            // return redirect()->route($url);
        }

        // Default redirect to the project document review route
        return redirect()->route('project-document.review', [
            'project'          => $project_document->project_id,
            'project_document' => $project_document->id,
        ]);
    }





    /**
     * Create a session key for an accepted review.
     *
     * @param  int|string  $documentId
     * @param  int|null    $userId
     * @return void
     */
    public static function createReviewSession($documentId, $userId = null)
    {
        $userId = $userId ?? Auth::id();

        if (!$userId) {
            throw new Exception('User must be authenticated to create review session.');
        }

        $key = "review.accepted.$documentId";

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
     * @param  int|string  $documentId
     * @param  int|null    $userId
     * @param  int         $expiryMinutes  (optional) time validity in minutes
     * @return bool
     */
    public static function verifyAndClearReviewSession($documentId, $userId = null, $expiryMinutes = 10)
    {
        $userId = $userId ?? Auth::id();
        $key = "review.accepted.$documentId";
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




    // used for the table formatting tools on datetime
    public static function returnFormattedDatetime($datetime){
        $formatted = $datetime
            ? ( $datetime instanceof Carbon
                ? $datetime
                : Carbon::parse($datetime)
              )->format('M d, Y • H:i')
            : null;

        return $formatted;
    }

    // used for the table formatting tools on user
    public static function returnFormattedUser($userId){

        $userName = $userId
            ? optional(User::find($userId))->name ?? '—'
            : '—';

        return $userName;
    }


    public static function returnStatusConfig($status){

        // 1) Status badge styles
        $map = [
            'draft' => ['label' => 'Draft', 'bg' => 'bg-slate-50', 'text' => 'text-slate-600', 'ring' => 'ring-slate-200'],
            'submitted' => ['label' => 'Submitted', 'bg' => 'bg-blue-50', 'text' => 'text-blue-700', 'ring' => 'ring-blue-200'],
            'in_review' => ['label' => 'In Review', 'bg' => 'bg-amber-50', 'text' => 'text-amber-700', 'ring' => 'ring-amber-200'],
            'approved' => ['label' => 'Approved', 'bg' => 'bg-emerald-50', 'text' => 'text-emerald-700', 'ring' => 'ring-emerald-200'],
            'rejected' => ['label' => 'Rejected', 'bg' => 'bg-rose-50', 'text' => 'text-rose-700', 'ring' => 'ring-rose-200'],
            'completed' => ['label' => 'Completed', 'bg' => 'bg-indigo-50', 'text' => 'text-indigo-700', 'ring' => 'ring-indigo-200'],
            'cancelled' => ['label' => 'Cancelled', 'bg' => 'bg-gray-100', 'text' => 'text-gray-500', 'ring' => 'ring-gray-200'],
            'reviewed' => ['label' => 'Reviewed', 'bg' => 'bg-lime-100', 'text' => 'text-lime-500', 'ring' => 'ring-lime-200'],
            'changes_requested' => ['label' => 'Changes Requested', 'bg' => 'bg-yellow-100', 'text' => 'text-yellow-500', 'ring' => 'ring-yellow-200'],
            'on_que' => ['label' => 'On Queue', 'bg' => 'bg-slate-100', 'text' => 'text-slate-500', 'ring' => 'ring-slate-200'],
        ];

        $config = $map[$status] ?? [
            'label' => ucfirst(str_replace('_', ' ', (string) $status)),
            'bg' => 'bg-slate-100',
            'text' => 'text-slate-500',
            'ring' => 'ring-slate-200',
        ];
        
        return $config;

    }
    

    // usage : returnFormattedLabel($project_document->status) return a formatted label
    public static function returnFormattedLabel($column_value){
        return ucfirst(str_replace('_', ' ', (string) $column_value)); 
    }

    // returns the reviewer name for the project document 
    public static function returnReviewerName(ProjectDocument $project_document){
        // 2) Current reviewer (active/true, by order asc)
        $reviewer = null;
        if ($project_document && method_exists($project_document, 'getCurrentReviewerByProjectDocument')) {
            $reviewer = $project_document->getCurrentReviewerByProjectDocument();
        }

        // 3) Slot details
        $slotType = $reviewer->slot_type ?? null; // 'open' | 'person'
        $slotRole = $reviewer->slot_role ?? null; // only shown for 'person'

        // 4) Reviewer display name
        $reviewerName = 'Open review';
        if ($slotType === 'person') {
            // assumes relation $reviewer->user
            $reviewerName = optional($reviewer->user)->name ?: 'Unassigned person';
        }else{
            if(!empty($reviewer->user_id)){
                // assumes relation $reviewer->user
                $reviewerName = optional($reviewer->user)->name ?: 'Unassigned person';
            }

        }

        return $reviewerName;
    }
    
    // returns the slot data        : slot_type or slot_role        : the type can be slot_type or slot_role
    public static function returnSlotData(ProjectDocument $project_document, $type = null){
         // 2) Current reviewer (active/true, by order asc)
        $reviewer = null;
        if ($project_document && method_exists($project_document, 'getCurrentReviewerByProjectDocument')) {
            $reviewer = $project_document->getCurrentReviewerByProjectDocument();
        }
        // dd( $reviewer);


        // 3) Slot details
        $slotType = $reviewer->slot_type ?? null; // 'open' | 'person'
        $slotRole = $reviewer->slot_role ?? null; // only shown for 'person'


        if($type == "slot_type"){
            return $slotType;
        }else{
            return $slotRole;
        }
    }

    // two types: dueAt can return dueAtText or dueAtDiff
    // two types of due date for : reviewer, submitter
    public static function returnDueDate(ProjectDocument $project_document, $return_type = null, $due_for = "reviewer"){



        $due_date = null;
        $due_timer_count = null;
        $due_timer_type = null;


        if(empty($due_for)){
            $due_for = $project_document->allow_project_submission == true ? "submitter" : 'reviewer';
        }

        switch($due_for){

            case "reviewer":
                $due_date = $project_document->reviewer_due_date;
                $due_timer_count = $project_document->reviewer_response_duration;
                $due_timer_type = $project_document->reviewer_response_duration_type;

                break;
            case "submitter":
                $due_date = $project_document->submitter_due_date;
                $due_timer_count = $project_document->submitter_response_duration;
                $due_timer_type = $project_document->submitter_response_duration_type;
                break;

            default:  
                // default is reviewer
                $due_date = $project_document->reviewer_due_date;
                $due_timer_count = $project_document->reviewer_response_duration;
                $due_timer_type = $project_document->reviewer_response_duration_type; 
                break;

        }




        // Expected due date
        // Prefer explicit due date on Project Document; fallback to timer_count + timer_type
        $dueAt = null;


  

        if (!empty($due_date)) {
            $dueAt = Carbon::parse($due_date);
        } else {
            $count = (int) ($due_timer_count ?? 0);
            $type  = (string) ($due_timer_type ?? '');
            if ($count > 0 && $type) {
                // normalize type
                switch (strtolower($type)) {
                    case 'day':
                    case 'days':
                        $dueAt = Carbon::now()->addDays($count);
                        break;
                    case 'week':
                    case 'weeks':
                        $dueAt = Carbon::now()->addWeeks($count);
                        break;
                    case 'month':
                    case 'months':
                        $dueAt = Carbon::now()->addMonths($count);
                        break;
                    case 'year':
                    case 'years':
                        $dueAt = Carbon::now()->addYears($count);
                        break;
                }
            }
        }

        


        if($return_type == "dueAtText"){ // dueAtText
            $dueAtText = $dueAt ? $dueAt->timezone(config('app.timezone', 'UTC'))->format('M d, Y g:ia') : null;
            return $dueAtText;
        }elseif($return_type == "dueAtDiff"){  // dueAtDiff
            $dueAtDiff = $dueAt ? $dueAt->diffForHumans() : null;
            return $dueAtDiff;
        }else{ // default
            $dueAtDiff = $dueAt ? $dueAt->diffForHumans() : null;
            return  $dueAtDiff;
        }
            
        

    }

    public static function returnReviewStatus(ProjectDocument $project_document){
        // 2) Current reviewer (active/true, by order asc)
        $reviewer = null;
        if ($project_document && method_exists($project_document, 'getCurrentReviewerByProjectDocument')) {
            $reviewer = $project_document->getCurrentReviewerByProjectDocument();
        }


        // 5) Reviewer status on the reviewer instance (e.g., 'pending','accepted','returned', etc.)
        $reviewStatus = $reviewer->review_status ?? null;
        return $reviewStatus;
    }

    public static function returnReviewFlagsStatus(ProjectDocument $project_document){
         // 2) Current reviewer (active/true, by order asc)
        $reviewer = null;
        if ( $project_document && method_exists( $project_document, 'getCurrentReviewerByProjectDocument')) {
            $reviewer =  $project_document->getCurrentReviewerByProjectDocument();
        }

        $flags = [
            'requires_project_update'   => (bool) ($reviewer->requires_project_update ?? false),
            'requires_document_update'  => (bool) ($reviewer->requires_document_update ?? false),
            'requires_attachment_update'=> (bool) ($reviewer->requires_attachment_update ?? false),
        ];

        return $flags;


    }


    // send notification regarding complete project document aproval notification
    public static function sendCompleteProjectDocumentApprovalNotification(ProjectDocument $project_document){



        // send notification to the creator
        if($project_document->status == "approved"){
            $creator = User::where('id',$project_document->created_by)->first();

            try {
                // notify the next reviewer
                Notification::send($creator, new ProjectDocumentCompleteApprovalNotification(  $project_document  )); 
            } catch (\Throwable $e) {
                // Log the error without interrupting the flow
                Log::error('Failed to send ProjectDocumentCompleteApprovalNotification notification: ' . $e->getMessage(), [
                    'project_document_id' => $project_document->id,
                    'trace' => $e->getTraceAsString(),
                ]);
            } 
        }


        // send notification to project document subscribers  
        $message = "The project document '".$project_document->document_type->name."' for '".$project_document->project->name."'  had completed the review approval process ";

        $project = Project::find($project_document->project_id);

        ProjectHelper::sendForProjectSubscribersProjectSubscribersNotification($project,  $message, "project_approved");


        // project_approved


    }

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





    // READ: DO NOT ADD HERE FUNCTIONS THAT ARE NOT FOR MANUAL RUN
    // THIS FUNCTIONS ARE FOR MANUAL RUN THROUGH TINKER
    // custom functions to run custom needs and modifications
        public function setCurrentReviewerAsOpenForDocuments(){



        }

 

};