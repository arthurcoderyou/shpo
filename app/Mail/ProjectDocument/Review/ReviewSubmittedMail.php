<?php

namespace App\Mail\ProjectDocument\Review;

use App\Models\User;
use App\Models\Review;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailables\Address; 
use Illuminate\Mail\Mailables\Headers; 

class ReviewSubmittedMail extends Mailable  implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(

        public User $user,
        public Review $review,
        public Project $project,
        public ProjectDocument $project_document,
        public ProjectReviewer $project_reviewer,
        public ?string $viewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null,  // e.g. 'unsubscribe@example.org'
        public ?string $statusLabel = null,      // NEW
        public ?string $statusMessage = null,    // NEW
        public bool $hasNextUnapproved = false,  // NEW
        public ?ProjectReviewer $nextReviewer = null // NEW

    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $docName     = optional($this->project_document->document_type)->name ?? 'Project Document';
        $projectName = $this->project->name ?? 'Project';

        // Prefer supplied statusLabel from listener (submitter vs next reviewer)
        $prefix = $this->statusLabel
            ?: match ((string) ($this->review->review_status ?? '')) {
                'approved'          => 'Review Submitted — Approved',
                'rejected'          => 'Review Submitted — Rejected',
                'changes_requested' => 'Review Submitted — Changes Requested',
                default             => 'Review Submitted',
            };

        $subject = sprintf('%s — %s (%s)', $prefix, $docName, $projectName);

        return new Envelope(
            subject: $subject,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name',    config('mail.from.name'))
                ),
            ],
            // switched tag from "reviewed" to "review-submitted"
            tags: ['project-document', 'review-submitted'],
            metadata: [
                'audience'             => 'reviewer',
                'review_id'            => (string) ($this->review->id ?? ''),
                'project_id'           => (string) ($this->project->id ?? ''),
                'project_document_id'  => (string) ($this->project_document->id ?? ''),
                'project_reviewer_id'  => (string) ($this->project_reviewer->id ?? ''),
            ],
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        [$submittedAt, $submittedAtTz] = $this->submittedAtDisplay();

        // Prefer provided label/message from listener; fallback to computed
        $label   = $this->statusLabel;
        $message = $this->statusMessage;
        if (!$label || !$message) {
            [$fallbackLabel, $fallbackMessage] = $this->statusLines();
            $label   = $label   ?: $fallbackLabel;
            $message = $message ?: $fallbackMessage;
        }

        // Determine if THIS recipient is the next actionable reviewer
        $isActionable = $this->isActionableForRecipient();

        return new Content(
            // use dedicated "review-submitted" templates (markdown + plain)
            markdown: 'emails.project-document.review.review_submitted',
            text:     'emails.project-document.review.review_submitted_plane',
            with: [
                'project'             => $this->project,
                'projectDocument'     => $this->project_document,
                'projectReviewer'     => $this->project_reviewer,
                'review'              => $this->review,

                // status & routing
                'status'              => $this->review->review_status,
                'statusLabel'         => $label,
                'statusMessage'       => $message,
                'hasNextUnapproved'   => $this->hasNextUnapproved,
                'nextReviewer'        => $this->nextReviewer,
                'nextReviewerName'    => optional(optional($this->nextReviewer)->user)->name ?? 'Open Reviewer',

                // recipient & CTA context
                'recipientName'       => $this->user->name,
                'isActionable'        => $isActionable,     // show “Review now” button emphasis if true
                'viewUrl'             => $this->viewUrl,

                // when it was submitted
                'submittedAt'         => $submittedAt,
                'submittedAtTz'       => $submittedAtTz,
            ],
        );
    }

    /**
     * Custom headers (deliverability + one-click unsubscribe if provided).
     */
    public function headers(): Headers
    {
        $text = [
            'Precedence'               => 'bulk',
            'X-Auto-Response-Suppress' => 'All',
            'X-Mailer'                 => config('app.name') . ' Mailer',
        ];

        if ($this->unsubscribeUrl && $this->unsubscribeEmail) {
            $text['List-Unsubscribe']      = '<' . $this->unsubscribeUrl . '>, <mailto:' . $this->unsubscribeEmail . '>';
            $text['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
        }

        return new Headers(text: $text);
    }

    public function attachments(): array
    {
        return [];
    }

    /**
     * Determine submittedAt display value with fallback:
     * last_submitted_at → updated_at → now()
     */
    protected function submittedAtDisplay(): array
    {
        $tz = config('app.timezone');

        $base = $this->project_document->last_submitted_at
            ?: ($this->project_document->updated_at ?? now());

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
    }

    /**
     * Compute status-aware copy (fallback only; listener should pass label/message).
     */
    protected function statusLines(): array
    {
        $status = (string) ($this->review->review_status ?? '');

        $label = match ($status) {
            'approved'          => 'Review Submitted — Approved',
            'rejected'          => 'Review Submitted — Rejected',
            'changes_requested' => 'Review Submitted — Changes Requested',
            default             => 'Review Submitted',
        };

        $message = match ($status) {
            'approved'          => 'Your review has been recorded as **Approved**.',
            'rejected'          => 'Your review has been recorded as **Rejected**.',
            'changes_requested' => 'Your review has been recorded with **Changes Requested**.',
            default             => 'Your review has been recorded.',
        };

        return [$label, $message];
    }

    /**
     * Is this recipient the next actionable reviewer?
     * True when:
     * - There is a next reviewer,
     * - That reviewer has a user,
     * - And that user is the current mail recipient.
     */
    protected function isActionableForRecipient(): bool
    {
        if (!$this->hasNextUnapproved || !$this->nextReviewer) {
            return false;
        }

        $nextUserId = optional($this->nextReviewer->user)->id;
        return $nextUserId && $nextUserId === $this->user->id;
    }
}
