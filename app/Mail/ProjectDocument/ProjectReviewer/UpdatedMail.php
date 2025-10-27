<?php

namespace App\Mail\ProjectDocument\ProjectReviewer;

use App\Models\User;
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

class UpdatedMail extends Mailable  implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public Project $project,
        public ProjectDocument $document,
        public ?string $viewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
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
            'Reviewer List Updated — %s (%s)',
            $this->document->document_type->name ?? 'Project Document',
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
                'user_id'       => (string) $this->user->id,
                'project_id'       => (string) $this->project->id,
                'document_id'      => (string) $this->document->id, 
            ],

        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.project-document.reviewer.updated',
            text: 'emails.project-document.reviewer.updated_plane',
            with: [
                'user'        => $this->user,
                'project'        => $this->project,
                'document'       => $this->document, 
                'viewUrl'      => $this->viewUrl, 
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
}
