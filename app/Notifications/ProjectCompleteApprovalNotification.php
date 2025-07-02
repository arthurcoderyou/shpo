<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectCompleteApprovalNotification extends Notification 
// implements ShouldQueue 
{
    use Queueable;

    protected $project; 

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project)
    {
        $this->project = $project; 
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
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //                 ->line('The introduction to the notification.')
    //                 ->action('Notification Action', url('/'))
    //                 ->line('Thank you for using our application!');
    // }
    
    public function toMail($notifiable)
    {
        $attachments = $this->project->attachments;
        $projectLink = url()->to(route('project.show', $this->project->id, false));
        $user = User::find($this->project->created_by);

        $email = (new MailMessage)
            ->subject("Project Approval Completed: {$this->project->name}")
            ->markdown('emails.project.complete-approval', [
                'project' => $this->project, 
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
}
