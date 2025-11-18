<?php 
namespace App\Helpers;
use App\Events\ProjectDocument\ReReview\ReReviewRequest;
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
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;
use App\Helpers\ProjectDocumentHelpers; 
use RealRashid\SweetAlert\Facades\Alert;
use Illuminate\Support\Facades\Notification;
use App\Notifications\ProjectReviewNotification;
use App\Notifications\ReviewerReviewNotification;
use App\Notifications\ProjectReviewNotificationDB;
use App\Notifications\ReviewerReviewNotificationDB;
use App\Events\ProjectDocument\Review\ReviewRequest;
use App\Notifications\ProjectOpenReviewNotification;
use App\Notifications\ProjectSubscribersNotification;
use App\Notifications\ProjectOpenReviewNotificationDB;
use App\Events\ProjectDocument\Review\OpenReviewRequest;
use App\Notifications\ProjectReviewFollowupNotification;
use App\Notifications\ProjectReviewerUpdatedNotification;
use App\Notifications\ProjectCompleteApprovalNotification;
use App\Notifications\ProjectReviewFollowupNotificationDB;
use App\Notifications\ProjectReviewerUpdatedNotificationDB;
use App\Events\ProjectDocument\Review\FollowupReviewRequest;
use App\Notifications\ProjectDocumentCompleteApprovalNotification;
 
use Illuminate\Support\Collection;

class ProjectReviewerHelpers
{


    /**
     * Seed/ensure ProjectReviewer rows for a ProjectDocument.
     *
     * @param  \App\Models\ProjectDocument $project_document
     * @param  string $submission_type     'initial_submission' | 'supplemental_submission'
     */
    public static function setProjectDocumentReviewers(ProjectDocument $project_document, string $submission_type = 'initial_submission'): void
    {

        $auth_user_id = Auth::id() ?? $project_document->created_by;
        // Normalize submission type
        $submission_type = $submission_type === 'supplemental_submission'
            ? 'supplemental_submission'
            : 'initial_submission';

        if (empty($project_document)) {
            return;
        }

        // Eager load the reviewer template rows for this document type
        $reviewers = Reviewer::query()
            ->where('document_type_id', $project_document->document_type_id)
            ->orderBy('order')
            ->get();

        if ($reviewers->isEmpty()) {
            Log::warning('No reviewer templates found for document type.', [
                'document_type_id' => $project_document->document_type_id,
                'project_document_id' => $project_document->id,
            ]);
            return;
        }

        DB::transaction(function () use ($reviewers, $project_document, $submission_type,$auth_user_id) {
            foreach ($reviewers as $reviewer) {
                // Keys that must be unique
                $unique = [
                    'project_id'          => (int) $project_document->project_id,
                    'project_document_id' => (int) $project_document->id,
                    'order'               => (int) $reviewer->order,
                ];

                // Values to set on create
                $create = [
                    'review_status'  => 'pending',
                    'user_id'        => $reviewer->user_id ?? null,
                    'reviewer_type'  => 'document',
                    'slot_type'      => $reviewer->slot_type ?? null,
                    'slot_role'      => $reviewer->slot_role ?? null,
                    'period_value'      => $reviewer->period_value ?? null,
                    'period_unit'      => $reviewer->period_unit ?? null,
                    'created_by'     => Auth::id() ?? $auth_user_id,
                    'updated_by'     => Auth::id() ?? $auth_user_id,
                ];

                // Values to update (only for supplemental or if the row already exists)
                // You can decide what should be mutable on supplemental—below we refresh
                // the templated fields in case the template changed.
                $update = [
                    'user_id'    => $reviewer->user_id ?? null,
                    'slot_type'  => $reviewer->slot_type ?? null,
                    'slot_role'  => $reviewer->slot_role ?? null,
                    'period_value'      => $reviewer->period_value ?? null,
                    'period_unit'      => $reviewer->period_unit ?? null,
                    'updated_by' => Auth::id() ?? $auth_user_id,
                ];

                if ($submission_type === 'initial_submission') {
                    // Create if missing; do NOT touch existing (prevents clobbering state)
                    ProjectReviewer::firstOrCreate($unique, $create);
                } else { // supplemental_submission
                    // Ensure row exists; if it exists, update the mutable fields
                    $pr = ProjectReviewer::firstOrCreate($unique, $create);
                    // Optional: only update if needed
                    $pr->fill($update);
                    // Do not reset review_status on supplemental unless you intend to
                    // $pr->review_status = $pr->review_status ?? 'pending';
                    $pr->save();
                }
            }

            // After seeding/ensuring rows, align the "current" reviewer pointers.
            // This keeps your original behavior.
            ProjectDocument::resetCurrentProjectDocumentReviewersByDocument($project_document->id);
        });
    }



    /**
     * Summary of sendReviewNotificationToReviewer
     * @param \App\Models\ProjectDocument $project_document
     * @param string $submission_type
     * @return void
     * initial_submission           - first submission of document submitter
     * supplemental_submission      - supplemental submission of document submitter after updating the project
     * reviewer_submission          - reviewer submission that is event based on after review on the document by the reviewer 
     */
    public static function sendReviewNotificationToReviewer(ProjectDocument $project_document, string $submission_type = 'initial_submission'){

        // get the current reviewer of the document 
        $current_reviewer =     $project_document->getCurrentReviewerByProjectDocument();

        $auth_user_id = Auth::user()->id ?? $project_document->created_by;


        // check if there are still project reviewers 
        if( !empty($current_reviewer) ){
            $slot_type = $current_reviewer->slot_type; 


            if($submission_type == "initial_submission" || $submission_type == "reviewer_submission"){

            
                switch($slot_type){

                    case "person":  

                        try {
                        event(new ReviewRequest( $current_reviewer->id, $auth_user_id, true, true));
                        } catch (\Throwable $e) {
                            // Log the error without interrupting the flow
                            Log::error('Failed to dispatch ReviewRequest event: ' . $e->getMessage(), [
                                'project_reviewer_id' => $current_reviewer->id,
                                'trace' => $e->getTraceAsString(),
                            ]);
                        }
                        
                        break;
                    case "open":

                        // Fetch admin IDs for this document
                        $rawAdminIds = ProjectReviewerHelpers::getNotificationRecipientUserIds(
                            'admins',
                            $current_reviewer->project_document_id
                        ) ?? [];

                        // Normalize -> integers, remove null/empty, dedupe, skip current actor
                        $adminIds = collect(is_array($rawAdminIds) ? $rawAdminIds : [$rawAdminIds])
                            ->filter()                                     // drop null/empty
                            ->map(fn ($id) => (int) $id)
                            ->unique()
                            ->reject(fn ($id) => $id === (int) Auth::id()) // don't notify the actor
                            ->values();

                        if ($adminIds->isEmpty()) {
                            Log::info('No admin recipients for OpenReviewRequest.', [
                                'project_reviewer_id' => $current_reviewer->id,
                                'project_document_id' => $current_reviewer->project_document_id,
                            ]);
                        } else {
                            foreach ($adminIds as $userId) {
                                try {
                                    event(new OpenReviewRequest(
                                        $current_reviewer->id, // project_reviewer_id
                                        $userId,               // recipient user id
                                        $auth_user_id,
                                        true,                  // send email
                                        true                   // send broadcast
                                    ));
                                } catch (\Throwable $e) {
                                    Log::error('Failed to dispatch OpenReviewRequest event.', [
                                        'error'                 => $e->getMessage(),
                                        'project_reviewer_id'   => $current_reviewer->id,
                                        'recipient_user_id'     => $userId,
                                        'actor_user_id'         => Auth::id(),
                                        'trace'                 => $e->getTraceAsString(),
                                    ]);
                                }
                            }
                        }

                        
                        break;
                    default: 
                        
                        break;
                }
            }else{ // string $submission_type = 'suplemental_submission'
                
                try {
                event(new FollowupReviewRequest( $current_reviewer->id, $auth_user_id, true, true));
                } catch (\Throwable $e) {
                    // Log the error without interrupting the flow
                    Log::error('Failed to dispatch FollowupReviewRequest event: ' . $e->getMessage(), [
                        'project_reviewer_id' => $current_reviewer->id,
                        'trace' => $e->getTraceAsString(),
                    ]);
                }


            }



        }else{

            // update project document  
            $project_document->status = "approved";
            $project_document->save();
        }
        
        // send project approval updates for creators and project subscribers if the project is approved 
        if($project_document->status == "approved"){
            ProjectDocumentHelpers::sendCompleteProjectDocumentApprovalNotification($project_document);
        }
 


    }





    public static function sendNotificationOnReviewerListUpdate(ProjectDocument $project_document){
        // notify reviewers and subscribers about the project document reviewer list update 
        
        // Make sure reviewers are loaded efficiently
        $project_document->loadMissing('project_reviewers:id,project_document_id,user_id');


        $id = Auth::id() ?? $project_document->created_by;

        // 1) Build the full recipient list (helper + assigned reviewers)
        $recipientIds = collect(
                ProjectReviewerHelpers::getNotificationRecipientUserIds(
                    // ['submitter', 'reviewers', 'admins', 'subscribers'],
                    ['submitter', 'reviewers', 'subscribers'],
                    $project_document->id
                ) ?? []
            )
            ->merge($project_document->project_reviewers->pluck('user_id'))
            ->filter() // remove nulls/empties
            ->unique()
            ->reject(fn ($id) => (int) $id === (int) $id) // don't notify the actor
            ->values();

        // 2) Nothing to do? Bail early.
        if ($recipientIds->isEmpty()) {
            Log::info('No recipients for ProjectReviewer Updated event.', [
                'project_document_id' => $project_document->id,
            ]);
            // return; // optional
        }

        // 3) Dispatch per recipient; keep going even if one fails.
        foreach ($recipientIds as $recipientId) {
            try {
                event(new \App\Events\ProjectDocument\ProjectReviewer\Updated(
                    $project_document->id,
                    (int) $recipientId,           // recipient user id
                    (int) $id,             // actor
                    true,                         // send email
                    true                          // send broadcast
                ));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch ProjectReviewer Updated event.', [
                    'error'                 => $e->getMessage(),
                    'project_document_id'   => $project_document->id,
                    'recipient_user_id'     => $recipientId,
                    'actor_user_id'         => $id,
                    'trace'                 => $e->getTraceAsString(),
                ]);
                // continue loop
            }
        }




    }



    public static function sendReReviewRequest(\App\Models\ReReviewRequest $re_review_request){
 

        $project_document = ProjectDocument::find($re_review_request->project_document_id);
        $last_review =  $project_document->getLastReview(); 
        

        // check if there is a last review instance
        if(empty($last_review)){
            Alert::error('Error','Re-review request failed. There are no previuos reviewers');
            return redirect()->route('project-document.review',[
                'project' => $$project_document->project_id,
                'project_document' => $project_document->id,
            
            ]); 
        }   

        // get the admin ids
        $adminIds = User::permission('system access admin')->pluck('id');
        // dd($adminIds);

        // dd($re_review_request);

        // 2) Nothing to do? Bail early.
        if ($adminIds->isEmpty()) {
            Log::info('No recipients for ProjectReviewer Updated event.', [ 
            ]);
            // return; // optional
        }

        // 3) Dispatch per recipient; keep going even if one fails.
        foreach ($adminIds as $key => $recipientId) {
            try {
                event(new \App\Events\ProjectDocument\ReReview\ReReviewRequest(
                    $re_review_request->id,
                    (int) $last_review->id,           // recipient user id
                    $recipientId,  // user to notify
                    (int) Auth::id(),             // actor
                    true,                         // send email
                    true                          // send broadcast
                ));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch ProjectReviewer Updated event.', [
                    'error'                 => $e->getMessage(),
                    're_review_request_id'   => $re_review_request->id, 
                    'recipient_user_id'     => $recipientId,
                    'actor_user_id'         => Auth::id(),
                    'trace'                 => $e->getTraceAsString(),
                ]);
                // continue loop
            }
        }


    }



     
    // ['submitter', 'reviewers', 'admins', 'subscribers']
    public static function getNotificationRecipientUserIds($recipient = 'all', int $project_document_id): array
    {

        $project_document = ProjectDocument::find($project_document_id);


        

        /**
         * Accepted types:
         * - submitter      => project_documents.created_by
         * - reviewers      => project_document->project_reviewers()->pluck('user_id')
         * - admins         => users with permission 'system access admin'
         * - subscribers    => project->project_subscribers()->pluck('user_id')
         * - all            => all of the above
         *
         * $recipient can be:
         * - string: 'submitter' | 'reviewers' | 'admins' | 'subscribers' | 'all'
         * - comma list: 'submitter,reviewers'
         * - array: ['submitter', 'reviewers']
         */

        // Normalize the requested types
        $types = match (true) {
            is_array($recipient) => array_map('trim', $recipient),
            is_string($recipient) && strtolower($recipient) === 'all' => ['submitter', 'reviewers', 'admins', 'subscribers'],
            is_string($recipient) => array_map('trim', explode(',', strtolower($recipient))),
            default => ['submitter', 'reviewers', 'admins', 'subscribers'],
        };

        $ids = collect();

        foreach ($types as $type) {
            switch ($type) {
                case 'submitter':
                    // Always return as array, even if a single value
                    $ids = $ids->merge(
                        collect([$project_document->created_by])->filter()
                    );
                    break;

                case 'reviewers':
                    // From project_reviewers relation (only those with a user_id)
                    $reviewerIds = $project_document->project_reviewers()
                        ->whereNotNull('user_id')
                        ->pluck('user_id');
                    $ids = $ids->merge($reviewerIds);
                    break;

                case 'admins':
                    // Users with permission 'system access admin' (Spatie)
                    // You can swap to ->role('Admin') if that’s your convention, but the ask was permission-based.
                    $adminIds = User::permission('system access admin')->pluck('id');
                    $ids = $ids->merge($adminIds);
                    break;

                case 'subscribers':
                    // Through project -> project_subscribers (user_id)
                    $subscriberIds = optional($project_document->project)
                        ? $project_document->project->project_subscribers()->whereNotNull('user_id')->pluck('user_id')
                        : collect();
                    $ids = $ids->merge($subscriberIds);
                    break;

                default:
                    // ignore unknown tokens
                    break;
            }
        }

        // Ensure unique, integers, no nulls, reindex
        return $ids
            ->filter(fn ($v) => !is_null($v))
            ->map(fn ($v) => (int) $v)
            ->unique()
            ->values()
            ->all();

            
    }

      
    // this is used for the project reviewer list page to extract project reviewer ids 
    public static function extractProjectReviewerIds(array $matrix): Collection
    {
        // If the shape is [typeId => [rows...]], flatten once, then pluck
        return collect($matrix)
            ->flatten(1) // collapse to [rows...]
            ->pluck('project_reviewer_id')
            ->filter(fn ($v) => !is_null($v)) // drop nulls
            ->map(fn ($v) => (int) $v)        // normalize type
            ->unique()
            ->values();
    }



}