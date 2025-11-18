<?php

namespace App\Notifications\ProjectDocument\ReReview;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectDocument;
use App\Models\ProjectReviewer;
use App\Models\ReReviewRequest;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ReReviewRequestNotification extends Notification implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct(
        public Project $project,
        public ProjectDocument $project_document,
        public ReReviewRequest $re_review_request,
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
        // Try to surface “who requested” and “why”
        $requestedByName = optional($this->re_review_request->project_reviewer_requested_by ?? null)->name
            ?? optional($this->re_review_request->project_reviewer_requested_by ?? null)->name
            ?? 'Requester';

        $requestedToName = optional($this->re_review_request->project_reviewer_requested_to ?? null)->name
            ?? optional($this->re_review_request->project_reviewer_requested_to ?? null)->name
            ?? 'Requester';

        return [
            'project_id' => $this->project->id,
            'project_document_id' => $this->project_document->id,
            're_review_request_id' => $this->re_review_request->id,
            'name' => $this->project_document->document_type->name." Re-Review Request", 
            'message' => "Project Document '".$this->project_document->document_type->name."' on '".$this->project->name."' had been requested for re-review to '".$requestedToName."' by '".$requestedByName."'" ,
            // 'url' => route('project-document.index'),
        ];
    }
}
