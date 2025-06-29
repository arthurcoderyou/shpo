<?php

namespace App\Notifications;

use App\Models\User;
use App\Models\Project;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectSubscribersNotification extends Notification  implements ShouldQueue
// implements ShouldQueue
{
    use Queueable;

    protected $user;
    protected $project;
    protected $message_type;
    protected $message;


    /**
     * Create a new notification instance.
     */
    public function __construct(User $user,Project $project,String $message_type, String $message)
    {
        $this->user = $user;
        $this->project = $project;
        $this->message_type = $message_type;
        $this->message = $message;


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

 

        $email = (new MailMessage)
            ->subject("Project Update: {$this->project->name}")
            ->markdown('emails.project.project_subscriber_update', [
                'user' => $this->user,
                'project' => $this->project, 
                'message' => $this->message,
                'message_type' => $this->message_type,
                'url' => route('project.show', $this->project->id),
            ]);
 


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
