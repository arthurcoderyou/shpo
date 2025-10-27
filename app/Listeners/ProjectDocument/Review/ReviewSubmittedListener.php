<?php

namespace App\Listeners\ProjectDocument\Review;

use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Events\ProjectDocument\Review\ReviewSubmitted;
use App\Mail\ProjectDocument\Review\ReviewSubmittedMail;
use App\Notifications\ProjectDocument\Review\ReviewSubmittedNotification;

use App\Events\ProjectDocument\Review\Reviewed;
use App\Mail\ProjectDocument\Review\ReviewedMail;
use App\Models\ProjectReviewer;
use App\Models\Review;
use App\Notifications\ProjectDocument\Review\ReviewedNotification; 

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

class ReviewSubmittedListener implements ShouldQueue
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
    public function handle(ReviewSubmitted $event): void
    {
        

        // ---- Load records (defensive lookups)
        $review            = Review::find($event->review_id);
        if (!$review) { Log::warning('Review not found in ReviewSubmittedListener', ['review_id' => $event->review_id]); return; }

        $projectReviewer   = ProjectReviewer::find($review->project_reviewer_id);
        if (!$projectReviewer) { Log::warning('ProjectReviewer not found in ReviewSubmittedListener', ['project_reviewer_id' => $review->project_reviewer_id]); return; }

        $projectDocument   = ProjectDocument::find($review->project_document_id);
        if (!$projectDocument) { Log::warning('ProjectDocument not found in ReviewSubmittedListener', ['project_document_id' => $review->project_document_id]); return; }

        $project           = Project::find($review->project_id);
        if (!$project) { Log::warning('Project not found in ReviewSubmittedListener', ['project_id' => $review->project_id]); return; }

        // The user we’ll “notify” first is the submitting reviewer (acknowledgment)
        // If your event carries a different field for the reviewer’s user id, adjust here.
        $submittingUser    = $projectReviewer->user ?: User::find($event->notify_user_id);
        if (!$submittingUser) { Log::warning('Submitting reviewer user not found', ['notify_user_id' => $event->notify_user_id]); }

        $viewUrl = route('project.project-document.show', [
            'project'          => $project->id,
            'project_document' => $projectDocument->id,
        ]);

        // ---- Workflow routing: figure out “next reviewer” if chain continues
        $status            = (string) ($review->review_status ?? '');
        $hasNextUnapproved = $projectDocument->hasNextUnapprovedReviewerAfter($projectReviewer);
        $nextReviewer      = $projectDocument->nextUnapprovedReviewerAfter($projectReviewer); // can be null
        $nextReviewerUser  = $nextReviewer?->user; // may be null for open slot

        $docName           = optional($projectDocument->document_type)->name ?? 'Project Document';
        $projectName       = $project->name ?? 'Project';

        // =====================================================================
        // MESSAGE FOR SUBMITTING REVIEWER (acknowledgment; “you submitted …”)
        // =====================================================================
        switch ($status) {
            case 'approved':
                if ($hasNextUnapproved) {
                    $statusLabelSubmitter   = 'Review Submitted — Forwarded to Next Reviewer';
                    $statusMessageSubmitter = sprintf(
                        "You submitted your review for **%s** on **%s** as **Approved**. The document was forwarded to the next reviewer%s.",
                        $docName,
                        $projectName,
                        $nextReviewerUser ? (" (**" . ($nextReviewerUser->name ?? 'Assigned Reviewer') . "** next)") : ' (Open Reviewer slot)'
                    );
                } else {
                    $statusLabelSubmitter   = 'Review Submitted — Final Reviewer';
                    $statusMessageSubmitter = sprintf(
                        "You submitted your review for **%s** on **%s** as **Approved**. This was the final required review.",
                        $docName,
                        $projectName
                    );
                }
                break;

            case 'rejected':
                $statusLabelSubmitter   = 'Review Submitted — Rejected';
                $statusMessageSubmitter = sprintf(
                    "You submitted your review for **%s** on **%s** as **Rejected**.",
                    $docName,
                    $projectName
                );
                break;

            case 'changes_requested':
                $statusLabelSubmitter   = 'Review Submitted — Changes Requested';
                $statusMessageSubmitter = sprintf(
                    "You submitted your review for **%s** on **%s** with **Changes Requested**.",
                    $docName,
                    $projectName
                );
                break;

            default:
                $statusLabelSubmitter   = 'Review Submitted';
                $statusMessageSubmitter = sprintf(
                    "You submitted your review for **%s** on **%s**.",
                    $docName,
                    $projectName
                );
                break;
        }
 

        // =====================================================================
        // MAIL — send to submitting reviewer (ack)  
        // =====================================================================
        if (!empty($event->sendMail)) {
            try {
                if ($submittingUser && $submittingUser->email) {
                    Mail::to($submittingUser->email)->queue(new ReviewSubmittedMail(
                        $submittingUser,
                        $review,
                        $project,
                        $projectDocument,
                        $projectReviewer,
                        $viewUrl,
                        null,
                        null,
                        $statusLabelSubmitter,
                        $statusMessageSubmitter,
                        $hasNextUnapproved, 
                        $nextReviewer
                    ));
                }
 
            } catch (\Throwable $e) {
                Log::error('Failed sending ReviewSubmittedMail', [
                    'review_id'           => $review->id,
                    'project_reviewer_id' => $projectReviewer->id,
                    'submitting_user_id'  => $submittingUser?->id, 
                    'project_document_id' => $projectDocument->id,
                    'project_id'          => $project->id,
                    'error'               => $e->getMessage(),
                ]);
            }
        }

        // =====================================================================
        // NOTIFICATIONS — database notifications (submitter  )
        // =====================================================================
        if (!empty($event->sendNotification)) {
            try {
                if ($submittingUser) {
                    Notification::send($submittingUser, new ReviewSubmittedNotification(
                        $submittingUser,
                        $review,
                        $project,
                        $projectDocument,
                        $projectReviewer,
                        $statusLabelSubmitter,
                        $statusMessageSubmitter,
                        $hasNextUnapproved, 
                    ));
                }
 
            } catch (\Throwable $e) {
                Log::error('Failed sending ReviewSubmittedNotification', [
                    'review_id'           => $review->id,
                    'project_reviewer_id' => $projectReviewer->id,
                    'submitting_user_id'  => $submittingUser?->id, 
                    'project_document_id' => $projectDocument->id,
                    'project_id'          => $project->id,
                    'error'               => $e->getMessage(),
                ]);
            }
        }

        // =====================================================================
        // ACTIVITY LOG — one entry for the submission (submitter-focused)
        // =====================================================================
        $message = $statusLabelSubmitter . ' — ' . $docName . ' (' . $projectName . ')';
        ActivityLog::create([
            'created_by'          => $event->authId ?? $submittingUser?->id,
            'log_username'        => $submittingUser?->name ?? 'Reviewer',
            'log_action'          => $message,
            'project_review_id'   => $review->id,
            'project_id'          => $projectDocument->project_id,
            'project_document_id' => $projectDocument->id,
            'project_reviewer_id' => $projectReviewer->id,
        ]);










    }
}
