<?php

namespace App\Notifications;

use App\Models\ProjectTimer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectTimerUpdatedNotification extends Notification implements ShouldQueue
{
    use Queueable;

    protected ProjectTimer $project_timer; 

    /**
     * Create a new notification instance.
     */
    public function __construct($project_timer,  )
    {
        $this->project_timer = $project_timer; 
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


    public function toMail($notifiable)
    {  

        $email = (new MailMessage)
            ->subject("Time Settings for All Projects Have Been Updated")
            ->markdown('emails.project.timer_updated', [ 
                'project_timer' => $this->project_timer,
                'user' => $notifiable,
                'url' => url()->to(route('project_timer.index',  false)),
            ]);

          
        return $email;
 
 


    }



    // /**
    //  * Get the mail representation of the notification.
    //  */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     return (new MailMessage)
    //         ->line('The introduction to the notification.')
    //         ->action('Notification Action', url('/'))
    //         ->line('Thank you for using our application!');
    // }




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
