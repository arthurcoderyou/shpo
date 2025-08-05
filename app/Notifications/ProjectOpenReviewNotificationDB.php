<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectReviewer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectOpenReviewNotificationDB extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $project_reviewer;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, ProjectReviewer $project_reviewer)
    {
        $this->project = $project;
        $this->project_reviewer = $project_reviewer;
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

    // /**
    //  * Get the mail representation of the notification.
    //  */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }

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
            'message' => "Project Open Review Request: {$this->project->name}",
            'project_reviewer' => $this->project_reviewer,
            'url' => url()->to(route('project.index.open-review', false)),
        ];
    }
}
