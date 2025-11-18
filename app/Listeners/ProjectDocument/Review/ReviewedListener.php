<?php

namespace App\Listeners\ProjectDocument\Review;

use App\Events\ProjectDocument\Review\Reviewed;
use App\Mail\ProjectDocument\Review\ReviewedMail;
use App\Models\ProjectReviewer;
use App\Models\Review;
use App\Notifications\ProjectDocument\Review\ReviewedNotification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

use App\Models\User;
use App\Models\Project;
use App\Models\ActivityLog;
use App\Models\ProjectDocument; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail; 
use Illuminate\Support\Facades\Notification; 
use App\Livewire\Admin\Project\ProjectReview;
use App\Events\ProjectDocument\Review\ReviewRequest;
use App\Mail\ProjectDocument\Review\ReviewRequestMail;
use App\Notifications\ProjectDocument\SubmittedNotification;
use App\Notifications\ProjectDocument\Review\ReviewRequestNotification;


class ReviewedListener implements ShouldQueue
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
    public function handle(Reviewed $event): void
    {
        $review = Review::find($event->review_id); 
        $project_reviewer = ProjectReviewer::find($review->project_reviewer_id); 
        $project_document = ProjectDocument::find($review->project_document_id); 
        $project = Project::find($review->project_id);
        $user = User::find($event->notify_user_id);

        $viewUrl = route('project.project-document.show',['project' => $project->id,'project_document' => $project_document->id ]);


        // --- NEW: determine "forwarded to next reviewer" vs "complete"
        $status = (string) ($review->review_status ?? '');
        $hasNextUnapproved = $project_document->hasNextUnapprovedReviewerAfter($project_reviewer);
        $nextReviewer      = $project_document->nextUnapprovedReviewerAfter($project_reviewer);

        $docName     = optional($project_document->document_type)->name ?? 'Project Document';
        $projectName = $this->project->name ?? 'Project';


        // Label + Message shown in mail/notification
        switch ($status) {
            case 'approved':
                if ($hasNextUnapproved) {
                    $statusLabel   = 'Approved — Passed to Next Reviewer';
                    $statusMessage = sprintf(
                        "Your **%s** on **%s** was approved by **%s** and has been **forwarded to the next reviewer**%s. No action is required from you yet.",
                        $docName,
                        $projectName,
                        optional($project_reviewer->user)->name ?? 'Reviewer',
                        $nextReviewer ? (" (**" . (optional($nextReviewer->user)->name ?? 'Open Reviewer') . "** next)") : ''
                    );
                } else {
                    $statusLabel   = 'Approved — Review Complete';
                    $statusMessage = sprintf(
                        "Your **%s** on **%s** has been **fully approved**. No further action is required.",
                        $docName,
                        $projectName
                    );
                }
                break;

            case 'reviewed':
                if ($hasNextUnapproved) {
                    $statusLabel   = 'Reviewed — Passed to Next Reviewer';
                    $statusMessage = sprintf(
                        "Your **%s** on **%s** was reviewed by **%s** and has been **forwarded to the next reviewer**%s. No action is required from you yet.",
                        $docName,
                        $projectName,
                        optional($project_reviewer->user)->name ?? 'Reviewer',
                        $nextReviewer ? (" (**" . (optional($nextReviewer->user)->name ?? 'Open Reviewer') . "** next)") : ''
                    );
                } else {
                    $statusLabel   = 'Reviewed — Review Complete';
                    $statusMessage = sprintf(
                        "Your **%s** on **%s** has been **fully reviewed**. No further action is required.",
                        $docName,
                        $projectName
                    );
                }
                break;

            case 'rejected':
                $statusLabel   = 'Rejected';
                $statusMessage = sprintf(
                    "Your **%s** on **%s** was **rejected** by **%s**. Please review the notes below.",
                    $docName,
                    $projectName,
                    optional($project_reviewer->user)->name ?? 'Reviewer'
                );
                break;

            case 'changes_requested':
                $statusLabel   = 'Changes Requested';
                $statusMessage = sprintf(
                    "Your **%s** on **%s** requires updates before it can be reviewed again. See details below and **resubmit** when ready.",
                    $docName,
                    $projectName
                );
                break;

            default:
                $statusLabel   = 'Reviewed';
                $statusMessage = sprintf(
                    "Your **%s** on **%s** has been reviewed.",
                    $docName,
                    $projectName
                );
                break;
        }

        // --- Mail
        if ($event->sendMail) {
            try {
                Mail::to($user->email)->queue(
                    // Add new args at the end to avoid breaking older calls
                    new ReviewedMail(
                        $user,
                        $review,
                        $project,
                        $project_document,
                        $project_reviewer,
                        $viewUrl,
                        null,
                        null, 
                        $statusLabel,        // NEW
                        $statusMessage,      // NEW
                        $hasNextUnapproved,  // NEW
                        $nextReviewer        // NEW (can be null)
                    )
                );
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch ReviewedMail mail: ' . $e->getMessage(), [
                    'review_id'            => $review->id,
                    'project_reviewer_id'  => $project_reviewer->id,
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
                Notification::send($user, new ReviewedNotification(
                    $user,
                    $review,
                    $project,
                    $project_document,
                    $project_reviewer,
                    $statusLabel,       // NEW
                    $statusMessage,     // NEW
                    $hasNextUnapproved, // NEW
                    $nextReviewer       // NEW
                ));
            } catch (\Throwable $e) {
                Log::error('Failed to dispatch ReviewedNotification notification: ' . $e->getMessage(), [
                    'review_id'            => $review->id,
                    'project_reviewer_id'  => $project_reviewer->id,
                    'user_id'              => $user->id,
                    'project_document_id'  => $project_document->id,
                    'project_id'           => $project->id,
                    'trace'                => $e->getTraceAsString(),
                ]);
            }
        }

        // --- Activity Log (fixed $this-> use and document id)
        $message = $statusLabel . ' — ' . $docName . ' (' . $projectName . ')';

    
        ActivityLog::create([
            'created_by'          => $event->authId,
            'log_username'        => $user->name,
            'log_action'          => $message,
            'project_review_id'   => $review->id,
            'project_id'          => $project_document->project_id,
            'project_document_id' => $project_document->id, // FIXED
            'project_reviewer_id' => $project_reviewer->id,
        ]);
    }
}
