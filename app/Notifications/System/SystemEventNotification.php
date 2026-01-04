<?php

namespace App\Notifications\System;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SystemEventNotification extends Notification implements ShouldQueue
{
    use Queueable;

     /**
     * Create a new notification instance.
     *
     * Note: The last 4 params are optional so older dispatches won't break.
     */
    public function __construct(
        public int $targetUserId,
        public string $message,
        public string $link
    ) {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        // Keep using database; add 'broadcast' if you want realtime toasts.
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
            'message'   => $this->message, 
            // Navigation
            'url' => $this->link,
        ];
    }
}
