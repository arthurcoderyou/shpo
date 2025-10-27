<?php

namespace App\Mail\ProjectDocument;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use App\Models\ProjectDocument;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;
 
use Illuminate\Mail\Mailables\Address; 
use Illuminate\Mail\Mailables\Headers; 

class SubmittedMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public Project $project,
        public ProjectDocument $document,
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Project Document Submission Confirmation — ' . ($this->project->name ?? 'Project'),
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['project-document', 'submission'],
            metadata: [
                'project_id'  => (string) $this->project->id,
                'document_id' => (string) $this->document->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     * Note: use view keys (no ".blade.php").
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.project-document.submitted',       // resources/views/emails/project/submitted.blade.php
            text: 'emails.project-document.submitted_plain',     // resources/views/emails/project/submitted_plain.blade.php
            with: [
                'project'  => $this->project,
                'document' => $this->document,
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
}
