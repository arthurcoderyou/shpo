<?php

namespace App\Notifications\Project\Review;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectReviewer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ProjectReviewer $project_reviewer,
        public Project $project, 
    )
    {
        //
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
            'project_reviewer_id' => $this->project_reviewer->id,
            'project_id' => $this->project->id, 
            'name' => $this->project->name, 
            'message' => "Review Request on '".$this->project->name."' ",
            'url' =>  route('project.index',[
                'review_status' => 'pending',
                'pending_rc_number' => true,
            ]),
        ];
    }
}
