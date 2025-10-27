<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use App\Models\ProjectDocument;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectDocumentCompleteApprovalNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected $project_document;
    /**
     * Create a new notification instance.
     */
    public function __construct(ProjectDocument $project_document)
    {
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
        //     ->line('The introduction to the notification.')
        //     ->action('Notification Action', url('/'))
        //     ->line('Thank you for using our application!');

        $attachments = $this->project_document->project_attachments;
 

        $projectLink = url()->to(route('project.project-document.show', [
            'project' => $this->project_document->project_id, 
            'project_document' => $this->project_document->id
        ], false));


        $user = User::find($this->project_document->created_by);
  

        $email = (new MailMessage)
            ->subject("Project Approval Completed: {$this->project_document->document_type->name}")
            ->markdown('emails.project-document.complete-approval', [
                'project_document' => $this->project_document, 
                'url' => $projectLink,
                'user' => $user,
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
