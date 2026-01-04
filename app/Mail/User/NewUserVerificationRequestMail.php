<?php

namespace App\Mail\User;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address; 
use Illuminate\Mail\Mailables\Envelope;

use Illuminate\Mail\Mailables\Headers; 
use Illuminate\Contracts\Queue\ShouldQueue;
use App\Helpers\ActivityLogHelpers\UserLogHelper;

class NewUserVerificationRequestMail extends Mailable
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public User $user,
        public User $userToNotify,
        public ?string $viewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'

    )
    {
        
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        $subject = UserLogHelper::getActivityMessage('new-user-verification-request',$this->user->id, $this->user->id);

        return new Envelope(
            subject: $subject,
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['user', 'verification'],
            metadata: [
                'user_id' => (string) $this->user->id,
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
            markdown: 'emails.user.new_user_verification_request',       
            text: 'emails.user.new_user_verification_request_plain',      
            with: [
                'user' => $this->user,
                'userToNotify' => $this->userToNotify,
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

        $base = $this->user->updated_at
            ?: ($this->user->created_at ?? now());

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
    }
}
