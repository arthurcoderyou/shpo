<?php

namespace App\Notifications\ProjectDocument\Review;

use App\Models\User;
use App\Models\Review;
use App\Models\Project; 
use Illuminate\Support\Str;
use Illuminate\Bus\Queueable;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer; 
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewSubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
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

    )
    {
        //
    }

    /**
     * Delivery channels.
     */
    public function via(object $notifiable): array
    {
        // Add 'broadcast' if you also want real-time toasts.
        return ['database'];
    }

    /**
     * Optional: group/type for database notifications table (Laravel 11+)
     */
    public function databaseType(object $notifiable): string
    {
        return 'project_document.review.submitted';
    }

    /**
     * Database payload (explicit).
     */
    public function toDatabase(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * Fallback for older code paths that call toArray().
     */
    public function toArray(object $notifiable): array
    {
        return $this->payload();
    }

    /**
     * Build the normalized payload.
     */
    protected function payload(): array
    {
        $docName     = optional($this->project_document->document_type)->name ?? 'Project Document';
        $projectName = $this->project->name ?? 'Project';

        $status = (string) ($this->review->review_status ?? '');

        // Compute fallback label/message when not injected by the listener
        $computedPrefix = match ($status) {
            'approved'          => 'Approved',
            'rejected'          => 'Rejected',
            'changes_requested' => 'Changes Requested',
            default             => 'Reviewed',
        };

        $computedMessage = match ($status) {
            'approved' => $this->hasNextUnapproved
                ? 'Approved and forwarded to the next reviewer.'
                : 'Fully approved. No further action is required.',
            'rejected' => 'Rejected. Please review notes.',
            'changes_requested' => 'Changes requested. Please update and resubmit.',
            default => 'Reviewed.',
        };

        $label   = $this->statusLabel   ?: $computedPrefix . ($status === 'approved'
            ? ($this->hasNextUnapproved ? ' — Passed to Next Reviewer' : ' — Review Complete')
            : '');

        $message = $this->statusMessage ?: $computedMessage;

        $nextReviewerId   = optional($this->nextReviewer)->id;
        $nextReviewerName = optional(optional($this->nextReviewer)->user)->name
            ?? ($nextReviewerId ? ('Reviewer #'.$nextReviewerId) : 'Open Reviewer');

        $title = sprintf('%s — %s (%s)', $computedPrefix, $docName, $projectName);

        // Simple icon/category hints your UI can map
        $icon = match ($status) {
            'approved'          => 'check-circle',
            'rejected'          => 'x-circle',
            'changes_requested' => 'alert-circle',
            default             => 'info',
        };

        $category = 'review_submitted';

        // Safe route (adjust name if your route differs)
        $url = route('project.project-document.show', [
            'project'          => $this->project->id,
            'project_document' => $this->project_document->id,
        ]);

        return [
            // Identity
            'notification_id'      => (string) Str::uuid(),
            'category'             => $category,
            'icon'                 => $icon,

            // Primary identifiers
            'user_id'              => $this->user->id,
            'review_id'            => $this->review->id,
            'project_reviewer_id'  => $this->project_reviewer->id,
            'project_id'           => $this->project->id,
            'project_document_id'  => $this->project_document->id,

            // Display
            'title'                => $title,
            'name'                 => $docName,
            'message'              => $message,
            'status'               => $status,            // approved|rejected|changes_requested|...
            'status_label'         => $label,             // e.g. "Approved — Review Complete"
            'status_message'       => $message,           // same as message; keep both if your UI distinguishes

            // Chain/routing context
            'is_review_complete'   => ($status === 'approved') && ($this->hasNextUnapproved === false),
            'has_next_unapproved'  => $this->hasNextUnapproved,
            'next_reviewer_id'     => $nextReviewerId,
            'next_reviewer_name'   => $nextReviewerName,

            // Navigation
            'url'                  => $url,
        ];
    }
}
