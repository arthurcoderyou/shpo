<?php

namespace App\Notifications;

use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class NewUserRegisteredNotification extends Notification   implements ShouldQueue
// implements ShouldQueue
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
     * To prevent sending duplicate notifications for the same $new_user instance, you can use database notifications and check if the user has already been notified.
     * Modify your NewUserRegisteredNotification class to use the database as a delivery channel in addition to email:
     * Update via() Method to Include 'database'
     * 
     *
     * @return array<int, string>
     */
    public function via(object $notifiable): array
    {
        return ['mail'];
        // return ['database'];
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
            ->subject("New User Verification Request: {$this->new_user->name} for the role {$this->new_user->role_request}")
            ->markdown('emails.user.review', [
                'new_user' => $this->new_user, 
                'url' => route('user.edit', $this->new_user->id),
            ]);
 

        return $email;


    }

     

}
