<?php

namespace App\Mail\Project\Review;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ProjectReviewer;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Address; 


use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers; 
use Illuminate\Queue\SerializesModels; 
use Illuminate\Contracts\Queue\ShouldQueue;
  

class FollowupReviewRequestMail extends Mailable
{
    use Queueable, SerializesModels;

   /**
     * Create a new message instance.
     */
    public function __construct(
        public ProjectReviewer $project_reviewer,
        public Project $project, 
        public ?string $reviewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'
    )
    {
        //
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = sprintf(
            'Followup Review Request — %s ', 
            $this->project->name ?? 'Project'
        );


        return new Envelope(
            subject: $subject,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['project-document', 'review-request'],
            metadata: [
                'project_id'       => (string) $this->project->id, 
                'project_reviewer' => (string) $this->project_reviewer->id,
            ],

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        [$submittedAt, $submittedAtTz] = $this->submittedAtDisplay();

        // Optional deadline from reviewer record if present (e.g., due_at)
        $deadlineAt = null;
        if (!empty($this->project_reviewer->due_at)) {
            // If it's Carbon, format it; if it's a string, we’ll best-effort via ->toString()
            $deadlineAt = method_exists($this->project_reviewer->due_at, 'timezone')
                ? $this->project_reviewer->due_at->timezone(config('app.timezone'))->format('F j, Y g:ia')
                : (string) $this->project_reviewer->due_at;
        }

        return new Content(
            markdown: 'emails.project.review.followup_review_request_mail',
            text: 'emails.project.review.followup_review_request_mail_plain',
            with: [
                'project'        => $this->project, 
                'projectReviewer'=> $this->project_reviewer,
                'reviewUrl'      => $this->reviewUrl,
                'submittedAt'    => $submittedAt,
                'submittedAtTz'  => $submittedAtTz, // e.g., "Asia/Manila"
                'deadlineAt'     => $deadlineAt,
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
     * Get the attachments for the message.
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

        $base = $this->project->updated_at
            ?: ($this->document->created_at ?? now());

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
    }

}
