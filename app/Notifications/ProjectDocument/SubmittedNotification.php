<?php

namespace App\Notifications\ProjectDocument;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectDocument;
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
        public ProjectDocument $project_document,
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
            'project_document_id' => $this->project_document->id,
            'name' => $this->project_document->document_type->name, 
            'message' => "Project Document '".$this->project_document->document_type->name."' on '".$this->project->name."' had been submitted",
            // 'url' => route('project-document.index'),
        ];
    }
}
