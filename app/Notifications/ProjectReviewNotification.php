<?php

namespace App\Notifications;

use App\Models\Project;
use Illuminate\Bus\Queueable;
use App\Models\ProjectReviewer;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectReviewNotification extends Notification    
implements ShouldQueue
{
    use Queueable;

    protected $project;
    protected $projectReviewer;

    /**
     * Create a new notification instance.
     */
    public function __construct(Project $project, ProjectReviewer $projectReviewer)
    {
        $this->project = $project;
        $this->projectReviewer = $projectReviewer;
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
        $projectLink = url()->to(route('project.review', $this->project->id, false));

        $email = (new MailMessage)
            ->subject("Project Review Request: {$this->project->name}")
            ->markdown('emails.project.review', [
                'project' => $this->project,
                'reviewer' => $this->projectReviewer,
                'url' => $projectLink,
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
