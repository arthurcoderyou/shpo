<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Notifications\Notification;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;

class NewUserRegisteredNotificationDB extends Notification
{
    use Queueable;

    protected $new_user;

    /**
     * Create a new notification instance.
     */
    public function __construct(User $new_user)
    {
        $this->new_user = $new_user;
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
            'user_id' => $this->new_user['id'],
            'name' => $this->new_user['name'],
            'email' => $this->new_user['email'],
            'message' => "New User Verification Request: {$this->new_user->name} for the role {$this->new_user->role_request}",
            'url' => route('user.edit', $this->new_user->id),
        ];
    }
}
