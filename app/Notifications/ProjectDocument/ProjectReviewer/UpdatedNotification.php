<?php

namespace App\Notifications\ProjectDocument\ProjectReviewer;

use App\Models\User;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectDocument;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class UpdatedNotification extends Notification  implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public User $user,
        public Project $project,
        public ProjectDocument $project_document,
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
            'user_id' => $this->user->id,
            'project_id' => $this->project->id,
            'project_document_id' => $this->project_document->id,
            'name' => $this->project_document->document_type->name, 
            'message' => "Reviewer list updated on '".$this->project_document->document_type->name."' on '".$this->project->name."' ",
            'url' => route('project-document.index.review-pending'),
        ];
    }
}
