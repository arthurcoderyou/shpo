<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectReviewerUpdatedNotificationDB extends Notification implements ShouldBroadcastNow
{
    use Queueable;

    public $project;
    public $user;
    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, User $user)
    {
        $this->project = $project;
        $this->user = $user;
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
            'message' => "Project Reviewer List and Order Updated :  {$this->project->name}",
            'url' => url()->to(route('project.reviewer.index', $this->project->id, false)),
        ];
    }
}
