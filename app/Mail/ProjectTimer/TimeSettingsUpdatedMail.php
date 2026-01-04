<?php

namespace App\Mail\ProjectTimer;

use App\Models\User;
use App\Models\ProjectTimer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address; 


use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers; 
use Illuminate\Contracts\Queue\ShouldQueue;

class TimeSettingsUpdatedMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public ProjectTimer $projectTimer,
        public User $targetUser,
        public ?string $viewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
        public ?string $emailForRole = null,   //  admin, reviewer, user, guest
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        

        return new Envelope(
            subject: 'Time Settings Updated',
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['project_timer', 'submission'],
            metadata: [
                'project_timer' => (string) $this->projectTimer->id,
            ],
        );
    }

    /**
     * Get the message content definition.
     * Note: use view keys (no ".blade.php").
     */
    public function content(): Content
    {

        [$updatedAt, $supdatedAtTz] = $this->updatedAtDisplay();
        
        [$markdownView, $textView] = $this->resolveViewsForRole($this->emailForRole);

        return new Content(
             markdown: $markdownView,
            text: $textView,
            with: [
                'projectTimer'   => $this->projectTimer,
                'targetUser' => $this->targetUser,
                'updatedAt'    => $updatedAt,
                'supdatedAtTz'  => $supdatedAtTz,
                'viewUrl'        => $this->viewUrl,
                // Optional: useful in the template
                'emailForRole'   => $this->emailForRole,
            ],
        );
    }


    private function resolveViewsForRole(?string $role): array
    {
        $role = strtolower(trim((string) $role));

        $map = [
            'admin'    => ['emails.project-timer.admin.time_settings_updated',    'emails.project-timer.admin.time_settings_updated_plain'],
            'reviewer' => ['emails.project-timer.reviewer.time_settings_updated', 'emails.project-timer.reviewer.time_settings_updated_plain'],
            'user'     => ['emails.project-timer.user.time_settings_updated',     'emails.project-timer.user.time_settings_updated_plain'],
            'guest'    => ['emails.project-timer.guest.time_settings_updated',    'emails.project-timer.guest.time_settings_updated_plain'],
        ];

        return $map[$role]
            ?? ['emails.project-timer.guest.time_settings_updated', 'emails.project-timer.guest.time_settings_updated_plain'];
    }

     /**
     * Determine submittedAt display value with fallback:
     * last_submitted_at → updated_at → now()
     *
     * @return array{0:string,1:string}
     */
    protected function updatedAtDisplay(): array
    {
        $tz = config('app.timezone');

        $base = $this->projectTimer->updated_at
            ?: ($this->projectTimer->created_at ?? now());

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
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
