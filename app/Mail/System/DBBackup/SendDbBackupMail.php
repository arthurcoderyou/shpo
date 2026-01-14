<?php

namespace App\Mail\System\DBBackup;

use App\Models\User;
use App\Models\DBBackups;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable; 
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;

use Illuminate\Mail\Mailables\Address; 
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers; 

use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;


class SendDbBackupMail extends Mailable implements ShouldQueue
{
    use Queueable, SerializesModels;

    /**
     * Create a new message instance.
     */
    public function __construct(
        public DBBackups $dbBackup,
        public User $targetUser, 
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        

        return new Envelope(
            subject: 'SHPO Database Backup',
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['database', 'backup'],
            metadata: [
                'dbBackup' => (string) $this->dbBackup->id,
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
            markdown: 'emails.system.database.backup_mail',
            text: 'emails.system.database.backup_mail_plain',
            with: [
                'dbBackup'   => $this->dbBackup,
                'targetUser' => $this->targetUser, 
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
        // return [];

        $attachments = [];

        // if ($this->dbBackup->size_mb <= 20) {
            $attachments[] = Attachment::fromStorageDisk(
                'local',
                $this->dbBackup->folder.'/'.$this->dbBackup->file
            )->as('shpo-database-backup.sql');
        // }

        return $attachments;


    }
}
