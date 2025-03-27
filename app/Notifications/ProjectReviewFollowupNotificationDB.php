<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectReviewer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectReviewFollowupNotificationDB extends Notification
{
    use Queueable;

    protected $project;
    protected $projectReviewer;


    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, ProjectReviewer $projectReviewer)
    {
        $this->project = $project;
        $this->projectReviewer = $projectReviewer;
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
            'message' => "Project Update Review Request: {$this->project->name}",
            'url' => url()->to(route('project.review', $this->project->id, false)),
        ];
    }
}
