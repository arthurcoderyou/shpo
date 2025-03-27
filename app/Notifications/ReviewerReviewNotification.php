<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
 
class ReviewerReviewNotification extends Notification implements ShouldQueue
{
    use Queueable;


    protected $project;
    protected $review;


    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, Review $review)
    {
        $this->project = $project;
        $this->review = $review;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // return (new MailMessage)
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');

        $attachments = $this->review->attachments; 

        $email = (new MailMessage)
            ->subject("Project Reviewed: {$this->project->name}")
            ->markdown('emails.review.project_review', [
                'project' => $this->project,
                'review' => $this->review,
                'url' => route('project.show', $this->project->id),
            ]);

        // Attach project files if any
        foreach ($attachments as $attachment) {
            $path = storage_path("app/public/uploads/project_attachments/{$attachment->attachment}");
            if (file_exists($path)) {
                $email->attach($path);
            }
        }


        return $email;
    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
