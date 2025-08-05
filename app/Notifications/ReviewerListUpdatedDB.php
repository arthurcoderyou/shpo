<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class ReviewerListUpdatedDB extends Notification implements ShouldQueue
{
    use Queueable;

    protected $authId;
    protected $message;


    /**
     * Create a new notification instance.
     */
    public function __construct($authId, $message)
    {
        $this->authId = $authId;
        $this->message = $message;
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
            'authId' => $this->authId,
            'message' => $this->message,
            'url' => route('reviewer.index'),
        ];
    }
}
