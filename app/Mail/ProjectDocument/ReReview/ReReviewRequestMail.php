<?php

namespace App\Mail\ProjectDocument\ReReview;

use App\Models\User;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\ReReviewRequest;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

use Illuminate\Mail\Mailables\Address; 
use Illuminate\Mail\Mailables\Headers; 

class ReReviewRequestMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * @param ?string $viewUrl          Optional deep link to the review screen
     * @param ?string $unsubscribeUrl   Optional one-click unsubscribe URL
     * @param ?string $unsubscribeEmail Optional List-Unsubscribe email
     * @param ?string $statusLabel      Optional precomputed label override
     * @param ?string $statusMessage    Optional precomputed message override
     */
    public function __construct(
        public User             $user,                // recipient
        public ReReviewRequest  $re_review_request,  // the re-review request record
        public Project          $project,
        public ProjectDocument  $project_document,
        public ProjectReviewer  $project_reviewer,   // previous reviewer (the target of this email)
        public ?string          $viewUrl = null,
        public ?string          $unsubscribeUrl = null,
        public ?string          $unsubscribeEmail = null,
        public ?string          $statusLabel = null,
        public ?string          $statusMessage = null,
    ) {}

    public function envelope(): Envelope
    {
        $docName     = optional($this->project_document->document_type)->name ?? 'Project Document';
        $projectName = $this->project->name ?? 'Project';

        // // Re-review requests are always “Submitted/Requested” from the perspective of the recipient.
        // $prefix = $this->statusPrefix();

        $subject = sprintf('Re-review Request — %s (%s)',  $docName, $projectName);

        return new Envelope(
            subject: $subject,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name',    config('mail.from.name'))
                ),
            ],
            tags: ['project-document', 're-review', 'request'],
            metadata: [
                'project_id'           => (string) ($this->project->id ?? ''),
                'project_document_id'  => (string) ($this->project_document->id ?? ''),
                'project_reviewer_id'  => (string) ($this->project_reviewer->id ?? ''),
                're_review_request_id' => (string) ($this->re_review_request->id ?? ''),
            ],
        );
    }

    public function content(): Content
    {
        [$requestedAt, $requestedAtTz] = $this->requestedAtDisplay();
        [$label, $message] = $this->statusLines(); // safe, null-aware

        // Try to surface “who requested” and “why”
        $requestedByName = optional($this->re_review_request->project_reviewer_requested_by->user ?? null)->name
            ?? optional($this->re_review_request->project_reviewer_requested_by->user ?? null)->name
            ?? 'Requester';

        $reason = (string) ($this->re_review_request->reason ?? $this->re_review_request->notes ?? '');

        return new Content(
            // Ensure these views exist
            markdown: 'emails.project-document.rereview.re-review-requested',   // markdown blade (no .blade.php)
            text: 'emails.project-document.rereview.re-review-requested-plain', // text fallback
            with: [
                // core domain
                'project'             => $this->project,
                'projectDocument'     => $this->project_document,
                'projectReviewer'     => $this->project_reviewer, // previous reviewer (recipient context)
                'reReviewRequest'     => $this->re_review_request,

                // status copy
                'statusLabel'         => $label,
                'statusMessage'       => $message,

                // UX convenience
                'viewUrl'             => $this->viewUrl,

                // meta
                'requestedAt'         => $requestedAt,
                'requestedAtTz'       => $requestedAtTz,
                'requestedByName'     => $requestedByName,
                'reason'              => $reason,

                // helpful names for the email body
                'docTypeName'         => optional($this->project_document->document_type)->name ?? 'Project Document',
                'projectName'         => $this->project->name ?? 'Project',
                'recipientName'       => $this->user->name ?? 'Reviewer',
            ],
        );
    }

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

    /**
     * Attachments (none by default).
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }

    /* ============================
     | Helpers (null-safe, simple)
     ============================ */

    // protected function statusPrefix(): string
    // {
    //     // For re-review, common statuses you might have: requested|cancelled|completed
    //     $status = (string) ($this->re_review_request->status ?? '');
    //     return match ($status) {
    //         'cancelled' => 'Cancelled',
    //         'completed' => 'Completed',
    //         'submitted' => 'Submitted',
    //         'approved' => 'Approved',
    //         default     => 'Re-review Requested',
    //     };
    // }

    /**
     * Display the request timestamp with tz fallback.
     * @return array{0:string,1:string}
     */
    protected function requestedAtDisplay(): array
    {
        $tz   = config('app.timezone');
        $base = $this->re_review_request->created_at ?? now();

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
    }

    /**
     * Build label/message shown in the email body.
     * Uses injected overrides if provided.
     * @return array{0:string,1:string}
     */
    protected function statusLines(): array
    {
        if ($this->statusLabel || $this->statusMessage) {
            return [
                $this->statusLabel ?? 'Re-review Requested',
                $this->statusMessage ?? 'A re-review has been requested for this document.',
            ];
        }

        $status = (string) ($this->re_review_request->status ?? '');
        $label  = match ($status) {
            'submitted' => 'Re-review Submitted',
            'rejected' => 'Re-review Cancelled',
            'approved' => 'Re-review Approved',
            default     => 'Re-review Requested',
        };

        $message = match ($status) {
            'submitted' => 'This re-review request has been **submitted**. Please review the request.',
            'rejected' => 'This re-review request has been **rejected**. No action is required.',
            'approved' => 'This re-review request has been **approved**. Thank you for your review.',
            default     => 'A **re-review** has been requested. Please review the document again and provide your decision.',
        };

        return [$label, $message];
    }
}
