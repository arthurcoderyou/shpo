<?php

namespace App\Notifications\ProjectDocument\Review;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;

class ReviewedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     *
     * Note: The last 4 params are optional so older dispatches won't break.
     */
    public function __construct(
        public User $user,
        public Review $review,
        public Project $project,
        public ProjectDocument $project_document,
        public ProjectReviewer $project_reviewer,
        public ?string $statusLabel = null,           // NEW (optional)
        public ?string $statusMessage = null,         // NEW (optional)
        public bool $hasNextUnapproved = false,       // NEW (optional)
        public ?ProjectReviewer $nextReviewer = null  // NEW (optional)
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Keep using database; add 'broadcast' if you want realtime toasts.
        return ['database'];
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        $docName     = optional($this->project_document->document_type)->name ?? 'Project Document';
        $projectName = $this->project->name ?? 'Project';

        // Fallbacks if this notification is fired from legacy code without the new args.
        $status = (string) ($this->review->review_status ?? '');
        $computedPrefix = match ($status) {
            'approved'          => 'Approved',
            'rejected'          => 'Rejected',
            'changes_requested' => 'Changes Requested',
            default             => 'Reviewed',
        };

        // $statusLabel   = $this->statusLabel   ?: $computedPrefix . ' — ' . ($status === 'approved'
        //     ? ($this->hasNextUnapproved ? 'Passed to Next Reviewer' : 'Review Complete')
        //     : '');
        // $statusMessage = $this->statusMessage ?: match ($status) {
        //     'approved' => $this->hasNextUnapproved
        //         ? "Approved by " . (optional($this->project_reviewer->user)->name ?? 'Reviewer') . " — forwarded to next reviewer."
        //         : "Fully approved. No further action is required.",
        //     'rejected' => "Rejected by " . (optional($this->project_reviewer->user)->name ?? 'Reviewer') . ".",
        //     'changes_requested' => "Changes requested. Please update and resubmit.",
        //     default => "Reviewed.",
        // };

        $nextReviewerId   = optional($this->nextReviewer)->id;
        $nextReviewerName = optional(optional($this->nextReviewer)->user)->name ?? ($nextReviewerId ? 'Reviewer #'.$nextReviewerId : null);

        // Is the review chain complete? (approved and no remaining unapproved reviewers)
        $isReviewComplete = ($status === 'approved') && ($this->hasNextUnapproved === false);

        return [
            // Primary identifiers
            'user_id'              => $this->user->id,
            'review_id'            => $this->review->id,
            'project_reviewer_id'  => $this->project_reviewer->id,
            'project_id'           => $this->project->id,
            'project_document_id'  => $this->project_document->id,

            // Basic display
            'name'                 => $docName, // (Avoid null deref)
            'message'              => sprintf('%s — %s (%s)', $computedPrefix, $docName, $projectName),

            // NEW: Richer status for UI
            'status'               => $status,                 // e.g. approved|rejected|changes_requested
            // 'status_label'         => $statusLabel,            // e.g. "Approved — Review Complete"
            // 'status_message'       => $statusMessage,          // human-readable short text
            'status_label'         => $this->statusLabel,            // e.g. "Approved — Review Complete"
            'status_message'       => $this->statusMessage,          // human-readable short text

            'is_review_complete'   => $isReviewComplete,       // boolean
            'has_next_unapproved'  => $this->hasNextUnapproved, // boolean
            'next_reviewer_id'     => $nextReviewerId,         // nullable
            'next_reviewer_name'   => $nextReviewerName,       // nullable

            // Navigation
            'url' => route('project.project-document.show', [
                'project'          => $this->project->id,
                'project_document' => $this->project_document->id,
            ]),
        ];
    }
}
