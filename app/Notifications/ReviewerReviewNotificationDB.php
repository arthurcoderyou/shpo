<?php

namespace App\Notifications;

use App\Models\Review;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewerReviewNotificationDB extends Notification
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
        return ['database'];
    }

     

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
 
        return [
            'project_id' => $this->project['id'],
            'name' => $this->project['name'],
            'description' => $this->project['description'],
            'message' => "Project Reviewed: {$this->project->name} has been {$this->review->review_status}",
            'url' => route('project.show', $this->project->id),
        ];
    }
}
