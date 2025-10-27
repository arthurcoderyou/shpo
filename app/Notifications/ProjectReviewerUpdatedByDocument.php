<?php

namespace App\Notifications;

use App\Models\ProjectDocument;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ProjectReviewerUpdatedByDocument extends Notification implements ShouldQueue 
{
    use Queueable;

    public $project;
    public $user;
    public $project_document;
    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, User $user, ProjectDocument $project_document)
    {
        $this->project = $project;
        $this->user = $user;
        $this->project_document = $project_document;
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
        //             ->line('The introduction to the notification.')
        //             ->action('Notification Action', url('/'))
        //             ->line('Thank you for using our application!');


        $attachments = $this->project->attachments;

        $email = (new MailMessage)
            ->subject("Project Reviewer List Updated : {$this->project->name}")
            ->markdown('emails.project.reviewer_updated_by_document', [
                // 'project_title' => $this->project_title, 
                'project' => $this->project, 
                'user' => $this->user, 
                'project_document' => $this->project_document,
                'url' => route('project.show', $this->project->id),
            ]);

        // Attach project files if any
        foreach ($attachments as $attachment) {
            $path = storage_path("app/public/uploads/project_attachments/{$attachment->attachment}");
            if (file_exists($path)) {
                $email->attach($path);
            }
        }

        return $email;

    }

    /**
     * Get the array representation of the notification.
     *
     * @return array<string, mixed>
     */
    public function toArray(object $notifiable): array
    {
        return [
            //
        ];
    }
}
