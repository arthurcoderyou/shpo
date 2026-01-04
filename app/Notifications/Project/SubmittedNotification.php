<?php

namespace App\Notifications\Project;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class SubmittedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    
    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Project $project, 
    )
    {}

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
            'project_id' => $this->project->id, 
            'name' => $this->project->name, 
            'message' => "Project '".$this->project->name."' had been submitted",
            // 'url' => route('project-document.index'),
        ];
    }
}
