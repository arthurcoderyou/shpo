<?php

namespace App\Listeners\ProjectDocument\ReReview;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Support\Facades\Notification; 
use App\Events\ProjectDocument\Review\Reviewed;
use App\Events\ProjectDocument\ReReview\ReReviewRequest;
use App\Mail\ProjectDocument\ReReview\ReReviewRequestMail;
use App\Notifications\ProjectDocument\ReReview\ReReviewRequestNotification;

class ReReviewRequestListener implements ShouldQueue
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
    public function handle(ReReviewRequest $event): void
    {
        $re_review_request = \App\Models\ReReviewRequest::find($event->re_review_request_id); 
        $review = Review::find($event->review_id);
        
        $project_document = ProjectDocument::find($re_review_request->project_document_id); 
        $project = Project::find($re_review_request->project_id);


        $requested_to_reviewer = ProjectReviewer::find($re_review_request->requested_to);  
        $requested_by_reviewer = ProjectReviewer::find($re_review_request->requested_by);  

 

        $viewUrl = route('project.project-document.show',['project' => $project->id,'project_document' => $project_document->id ]);


        // --- NEW: determine "forwarded to next reviewer" vs "complete"
        $status = (string) ($review->review_status ?? ''); 

        $docName     = optional($project_document->document_type)->name ?? 'Project Document';
        $projectName = $project->name ?? 'Project';


        // Label + Message shown in mail/notification
        $statusLabel = 'Re-Review Request ';

        $statusMessage = sprintf(
            "The **review** of **%s** under **%s** has been requested to be **returned for re-review** by **%s**. Please revisit the review and provide the necessary response for rejection or confirmations%s.",
            $docName,
            $projectName,
            optional($requested_by_reviewer->user)->name ?? 'the current reviewer',
            $requested_to_reviewer
                ? (" of the review (previously reviewed by **" . (optional($requested_to_reviewer->user)->name ?? 'Previous Reviewer') . "**)")
                : ''
        );


        // notify the request to user instance
        $user = User::find( $event->notify_user_id);

        // --- Mail
        if ($event->sendMail) {
            try {
                Mail::to($user->email)->queue(
                    // Add new args at the end to avoid breaking older calls
                    new ReReviewRequestMail(
                        $user,
                        $re_review_request,
                        $project,
                        $project_document,
                        $requested_to_reviewer,
                        $viewUrl,
                        null,
                        null, 
                        $statusLabel,        // NEW
                        $statusMessage,      // NEW 
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch ReReviewRequestMail mail: ' . $e->getMessage(), [
                    're_review_request_id'            => $re_review_request->id,
                    'project_reviewer_id'  => $requested_to_reviewer->id,
                    'user_id'              => $user->id,
                    'project_document_id'  => $project_document->id,
                    'project_id'           => $project->id,
                    'trace'                => $e->getTraceAsString(),
                ]);
            }
        }

        // --- Notification
        if ($event->sendNotification) {
            try {
                Notification::send($user, new ReReviewRequestNotification(
                    $project,
                    $project_document,
                    $re_review_request,      // NEW
                ));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch ReReviewRequestNotification notification: ' . $e->getMessage(), [ 
                    're_review_request_id'  => $re_review_request->id,
                    'user_id'              => $user->id,
                    'project_document_id'  => $project_document->id,
                    'project_id'           => $project->id,
                    'trace'                => $e->getTraceAsString(),
                ]);
            }
        }

        // --- Activity Log (fixed $this-> use and document id)
        $message = $statusLabel . ' Re-review Request — ' . $docName . ' (' . $projectName . ')';

    
        ActivityLog::create([
            'created_by'          => $event->authId,
            'log_username'        => $user->name,
            'log_action'          => $message,
            'project_review_id'   => $review->id,
            'project_id'          => $project_document->project_id,
            'project_document_id' => $project_document->id,  
            'project_reviewer_id' => $requested_to_reviewer->id,
            're_review_requests_id' => $re_review_request->id,
        ]);
    }
}
