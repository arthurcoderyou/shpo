<?php

namespace App\Notifications;

use App\Models\ProjectTimer;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class ProjectTimerUpdatedNotificationDB extends Notification implements ShouldQueue
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
            'name' => 'Time Settings Updated',
            'description' => 'Time Settings for All Projects Have Been Updated',
            'message' => 'Time Settings for All Projects Have Been Updated',
            'url' => url()->to(route('project_timer.index',  false)),
        ];
    }
}
