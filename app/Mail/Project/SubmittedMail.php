<?php

namespace App\Mail\Project;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue; 
 
use Illuminate\Mail\Mailables\Address; 
use Illuminate\Mail\Mailables\Headers; 


class SubmittedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Project $project,
        public ?string $viewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        

        return new Envelope(
            subject: 'Project Submission Confirmation — ' . ($this->project->name ?? 'Project'),
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['project', 'submission'],
            metadata: [
                'project_id' => (string) $this->project->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     * Note: use view keys (no ".blade.php").
     */
    public function content(): Content
    {

        [$submittedAt, $submittedAtTz] = $this->submittedAtDisplay();
        

        return new Content(
            markdown: 'emails.project.submitted',        // resources/views/emails/project/submitted.blade.php
            text: 'emails.project.submitted_plain',      // resources/views/emails/project/submitted_plain.blade.php
            with: [
                'project' => $this->project,
                'submittedAt'          => $submittedAt,
                'submittedAtTz'        => $submittedAtTz,
                'viewUrl'              => $this->viewUrl,
            ],
        );
    }

    /**
     * Custom headers (deliverability + one-click unsubscribe if provided).
     */
    public function headers(): Headers
    {
        $text = [
            'Precedence'               => 'bulk', // helps avoid auto-replies
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

        $base = $this->project->last_submitted_at
            ?: ($this->project->updated_at ?? now());

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
    }


}
