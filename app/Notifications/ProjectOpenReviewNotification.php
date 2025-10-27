<?php

namespace App\Notifications;

use App\Models\Project;
use App\Models\ProjectReviewer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectOpenReviewNotification extends Notification implements ShouldQueue
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
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     */
    public function toMail(object $notifiable): MailMessage
    {
        // return (new MailMessage)
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');

        $attachments = $this->project->attachments;
        // $projectLink = url()->to(route('project.review', $this->project->id, false));
        $projectLink = url()->to(route('project-document.index.open-review',[
            'project' => $this->project->id,
            'project_document' => $this->project_reviewer->project_document_id
        ]));
        

        $email = (new MailMessage)
            ->subject("Project Open Review Request: {$this->project->name}")
            ->markdown('emails.project.open_review', [
                'project' => $this->project, 
                'url' => $projectLink,
                'project_reviewer' => $this->project_reviewer,
            ]);

        // Attach project files if any
        foreach ($attachments as $attachment) {
            $path = storage_path("app/public/uploads/review_attachments/{$attachment->attachment}");
            if (file_exists($path)) {
                $email->attach($path);
            }
        }



        

        return $email;
    }

    // /**
    //  * Get the array representation of the notification.
    //  *
    //  * @return array<string, mixed>
    //  */
    // public function toArray(object $notifiable): array
    // {
    //     return [
    //         //
    //     ];
    // }
}
