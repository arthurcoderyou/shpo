<?php

namespace App\Notifications\ProjectDocument\Review;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

use App\Models\Project; 
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;  


class FollowupReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public ProjectReviewer $project_reviewer,
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
            'project_reviewer_id' => $this->project_reviewer->id,
            'project_id' => $this->project->id,
            'project_document_id' => $this->project_document->id,
            'name' => $this->project_document->document_type->name, 
            'message' => "Followup Review Request on '".$this->project_document->document_type->name."' on '".$this->project->name."' ",
            'url' => route('project-document.index.review-pending'),
        ];
    }
}
