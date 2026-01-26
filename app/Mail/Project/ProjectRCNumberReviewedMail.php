<?php

namespace App\Mail\Project;

use App\Models\Review;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Address; 

use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Mail\Mailables\Headers; 
use Illuminate\Mail\Mailables\Attachment;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectRCNumberReviewedMail extends Mailable
{
    use Queueable, SerializesModels;

     /**
     * Create a new message instance.
     */
    public function __construct(
        public Review $review, 
        public ?string $viewUrl = null,         // e.g. route('admin.project-documents.review.show', [$project->id, $document->id])
        public ?string $unsubscribeUrl = null,   // e.g. route('unsubscribe.oneclick', ['token' => $user->token])
        public ?string $unsubscribeEmail = null  // e.g. 'unsubscribe@example.org'
    ) {}

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        
        $review = Review::find($this->review->id);    
        $project = Project::find($this->review->project_id); 

        return new Envelope(
            subject: $this->buildSubjectText(),
            from: new Address(config('mail.from.address'), config('mail.from.name')),
            replyTo: [
                new Address(
                    config('mail.reply_to.address', config('mail.from.address')),
                    config('mail.reply_to.name', config('mail.from.name'))
                ),
            ],
            tags: ['project', 'reviewed'],
            metadata: [
                'project_id' => (string) $project->id,
                'review_id' => (string) $review->id,
            ],
        );
    }


    /**
     * Build email subject based on review status.
     * @return string 
     */
    protected function buildSubjectText(): string
    {

        $review = Review::find($this->review->id);
        $project = Project::find($this->review->project_id); 
        $projectName = $project->name ?? 'Project';

        // Normalize status to reduce mismatch risk (case/spacing/underscores).
        $status = strtolower(trim((string) $review->review_status));
        $status = str_replace(['-', ' '], '_', $status); // e.g. "changes requested" -> "changes_requested"

        return match ($status) {
            // Approved / has RC number / can proceed with documents
            'reviewed', 'approved', 'with_rc', 'rc_issued', 'rc_number_issued' =>
                'Project Approved — RC Number Assigned — ' . $projectName,

            // Needs corrections / action required by submitter
            'changes_requested', 'revision_requested', 'needs_changes' =>
                'Changes Requested — Action Required — ' . $projectName,

            // Default fallback
            default =>
                'Project Reviewed Successfully — ' . $projectName,
        };
    }



    /**
     * Get the message content definition.
     * Note: use view keys (no ".blade.php").
     */
    public function content(): Content
    {

        [$reviewedAt, $reviewedAtTz] = $this->reviewedAtDisplay();
        
        $review = Review::find($this->review->id);
        $project = Project::find($this->review->project_id);

        /*
        |------------------------------------------------------------
        | Resolve mail views based on review status
        |------------------------------------------------------------
        */
        $status = $review->review_status; // e.g. reviewed, changes_requested


        $viewMap = [
            'reviewed' => [
                'markdown' => 'emails.project.rc_number_review.reviewed_mail',
                'text'     => 'emails.project.rc_number_review.reviewed_mail_plain',
            ],
            'changes_requested' => [
                'markdown' => 'emails.project.rc_number_review.changes_requested_mail',
                'text'     => 'emails.project.rc_number_review.changes_requested_mail_plain',
            ],
        ];

        // Fallback (important for safety)
        $views = $viewMap[$status] ?? $viewMap['reviewed'];

        
         
        return new Content(
            markdown: $views['markdown'],
            text: $views['text'],
            with: [
                'review' => $review,
                'project' => $project,
                'reviewedAt'          => $reviewedAt,
                'reviewedAtTz'        => $reviewedAtTz,
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
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        // return [];

        $attachments = [];

        // $review = Review::find($this->review->id);
       
        // foreach($review->attachments  as $attachment){

        //     $fileName = $attachment->stored_name;
        //     $path = "uploads/review_attachments/{$review->id}/{$fileName}";

        //     // if ($this->dbBackup->size_mb <= 20) {
        //         $attachments[] = Attachment::fromStorageDisk(
        //             'public',
        //             $path
        //         )->as($fileName);
        //     // }


        // }

        return $attachments;


    }


     /**
     * Determine reviewedAt display value with fallback:
     * last_updated_at → updated_at → now()
     *
     * @return array{0:string,1:string}
     */
    protected function reviewedAtDisplay(): array
    {
        $tz = config('app.timezone');

        $review = Review::find($this->review->id);

        $base = $review->created_at
            ?: ($review->updated_at ?? now());

        $formatted = method_exists($base, 'timezone')
            ? $base->timezone($tz)->format('F j, Y g:ia')
            : (string) $base;

        return [$formatted, $tz];
    }

}
