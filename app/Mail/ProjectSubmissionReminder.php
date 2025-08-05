<?php

namespace App\Mail;

use Carbon\Carbon;
use App\Models\Project;
use App\Models\ActiveDays;
use App\Models\ProjectTimer;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Queue\SerializesModels;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Contracts\Queue\ShouldQueue;

class ProjectSubmissionReminder extends Mailable 
implements ShouldQueue
{
    use Queueable, SerializesModels;

    public $project;
    public $message;

    public function __construct(Project $project)
    {
        $this->project = $project;

        $openTime = Carbon::parse(ProjectTimer::first()->project_submission_open_time);
        $closeTime = Carbon::parse(ProjectTimer::first()->project_submission_close_time);
        $today = now()->format('l');
        
        $isTodayActive = ActiveDays::where('day', $today)->where('is_active', true)->exists();
    
        if (!$isTodayActive) {
            // $this->message = "Project submissions are not allowed today ({$today}). Your project has been queued for automatic submission on the next active working day.";
            $this->message = "Project submissions are only allowed on active working days. Please submit during working hours on the next active day ({$today} is not a working day).";
        } else {
            // $this->message = "Project submission is currently outside working hours ({$openTime->format('h:i A')} - {$closeTime->format('h:i A')}). Your project has been queued for automatic submission during the next working period.";
            $this->message = "Project submissions are only accepted between {$openTime->format('h:i A')} and {$closeTime->format('h:i A')}. Please submit within these hours.";
        }


    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Reminder: Your Project can now be submitted',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // return new Content(
        //     view: 
        // );

        return new Content(
            markdown: 'emails.project.reminder',
        );

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
