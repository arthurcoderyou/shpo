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

class ReviewedMail extends Mailable implements ShouldQueue
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
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {

        $docName     = optional($this->project_document->document_type)->name ?? 'Project Document';
        $projectName = $this->project->name ?? 'Project';

        // Subject prefix based on review status
        $status = (string) ($this->review->review_status ?? '');
        $prefix = match ($status) {
            'approved'          => 'Approved',
            'rejected'          => 'Rejected',
            'changes_requested' => 'Changes Requested',
            default             => 'Reviewed',
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
            tags: ['project-document', 'reviewed'],
            metadata: [
                'review_id'             => (string) ($this->review->id ?? ''),
                'project_id'            => (string) ($this->project->id ?? ''),
                'project_document_id'   => (string) ($this->project_document->id ?? ''),
                'project_reviewer_id'   => (string) ($this->project_reviewer->id ?? ''),
            ],
        );
    }

    /**
     * Get the message content definition.
     * Note: view keys (no ".blade.php").
     */
    public function content(): Content
    {
        [$submittedAt, $submittedAtTz] = $this->submittedAtDisplay();

        // Build status-aware messaging + resubmission requirements
        [$statusLabel, $statusMessage, $resubmitRequirements] = $this->statusLines();

        // Compute submitter due date (only relevant if changes requested and duration fields present)
        [$dueAt, $dueAtTz] = $this->computeSubmitterDueAt();

        return new Content(
            // Make sure these views exist (markdown + plain)
            markdown: 'emails.project-document.review.reviewed',         // <- updated name recommendation
            text: 'emails.project-document.review.reviewed_plane',       // <- fix typo: "plain"
            with: [
                'project'              => $this->project,
                'projectDocument'      => $this->project_document,
                'projectReviewer'      => $this->project_reviewer,
                'review'               => $this->review,


                // existing vars...
                'statusLabel'        => $this->statusLabel, // e.g., "Approved" / "Rejected" / "Changes Requested"
                'statusMessage'      => $this->statusMessage, // human message about the decision
                'hasNextUnapproved'  => $this->hasNextUnapproved,
                'nextReviewer'       => $this->nextReviewer,
                'nextReviewerName'   => optional(optional($this->nextReviewer)->user)->name ?? 'Open Reviewer',
                'status'             => $this->review->review_status, // for button text logic in Blade

                'viewUrl'              => $this->viewUrl,

                'submittedAt'          => $submittedAt,
                'submittedAtTz'        => $submittedAtTz,

                'isSubmitter'           => $this->project_document->created_by == $this->user->id ? true : false,
       
                'resubmitRequirements' => $resubmitRequirements, // bullet list text (nullable)

                'dueAt'                => $dueAt,                // formatted due date for submitter (nullable)
                'dueAtTz'              => $dueAtTz,              // tz string (nullable)
            ],
        );
    }

    /**
     * Custom headers (deliverability + one-click unsubscribe if provided).
     */
    public function headers(): Headers
    {
        $text = [
            'Precedence'                 => 'bulk',      // helps avoid auto-replies
            'X-Auto-Response-Suppress'   => 'All',
            'X-Mailer'                   => config('app.name') . ' Mailer',
        ];

        if ($this->unsubscribeUrl && $this->unsubscribeEmail) {
            $text['List-Unsubscribe']      = '<' . $this->unsubscribeUrl . '>, <mailto:' . $this->unsubscribeEmail . '>';
            $text['List-Unsubscribe-Post'] = 'List-Unsubscribe=One-Click';
        }

        return new Headers(text: $text);
    }

    /**
     * Attachments (none by default).
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }



    /**
     * Determine submittedAt display value with fallback:
     * last_submitted_at → updated_at → now()
     *
     * @return array{0:string,1:string}
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
     * Build status-aware copy and resubmission requirements.
     *
     * @return array{0:string,1:string,2:?string}
     */
    protected function statusLines(): array
    {
        $status = (string) ($this->review->review_status ?? '');
        $label  = match ($status) {
            'approved'          => 'Approved',
            'rejected'          => 'Rejected',
            'changes_requested' => 'Changes Requested',
            default             => 'Reviewed',
        };

        // Base message (you can tailor more deeply to your Review fields)
        $message = match ($status) {
            'approved'   => 'Your submission has been **approved**. No further action is required.',
            'rejected'   => 'Your submission has been **rejected**. Please review the notes and consider submitting a new document if appropriate.',
            'changes_requested' => 'Your submission requires **updates**. Please review the requested changes and resubmit before the due date.',
            default      => 'Your submission has been reviewed.',
        };

        // Resubmission requirements (only for changes_requested)
        $requirements = null;
        if ($status === 'changes_requested') {
            $reqs = [];
            if ($this->review->requires_project_update ?? false) {
                $reqs[] = '• Update the **project details** as indicated in the review notes.';
            }
            if ($this->review->requires_attachment_update ?? false) {
                $reqs[] = '• Upload a **revised attachment** that addresses the noted issues.';
            }
            if (!empty($reqs)) {
                $requirements = implode("\n", $reqs) . "\n\nPlease **resubmit** the project document once the above are completed.";
            }
        }

        return [$label, $message, $requirements];
    }

    /**
     * Compute the submitter's due date for resubmission
     * Based on project_document.submitter_response_duration_type (day|week|month)
     * and project_document.submitter_response_duration (int).
     *
     * Baseline: review.updated_at → review.created_at → now()
     *
     * @return array{0:?string,1:?string} [formatted, tz] or [null, null] if not applicable
     */
    protected function computeSubmitterDueAt(): array
    {
        // Only compute when changes were requested
        if (($this->review->review_status ?? null) !== 'changes_requested') {
            return [null, null];
        }

        $type   = $this->project_document->submitter_response_duration_type ?? null; // 'day'|'week'|'month'
        $amount = (int) ($this->project_document->submitter_response_duration ?? 0);

        if (!$type || $amount <= 0) {
            return [null, null];
        }

        $tz   = config('app.timezone');
        $base = $this->review->updated_at ?? $this->review->created_at ?? now();

        // Normalize type to singular for relativedelta-like add
        $type = strtolower($type);
        $carbon = method_exists($base, 'copy') ? $base->copy() : now();

        // Add duration
        switch ($type) {
            case 'day':
            case 'days':
                $carbon = $carbon->addDays($amount);
                break;
            case 'week':
            case 'weeks':
                $carbon = $carbon->addWeeks($amount);
                break;
            case 'month':
            case 'months':
                $carbon = $carbon->addMonthsNoOverflow($amount);
                break;
            default:
                // Unknown type; do not emit due date
                return [null, null];
        }

        $formatted = $carbon->timezone($tz)->format('F j, Y g:ia');

        return [$formatted, $tz];
    }





}
