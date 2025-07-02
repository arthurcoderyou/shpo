<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Auth\Notifications\VerifyEmail as BaseVerifyEmail;

class CustomVerifyEmail extends BaseVerifyEmail 
// implements ShouldQueue
{
    use Queueable;

    /**
     * Create a new notification instance.
     */
    public function __construct()
    {
        //
    }

    /**
     * Get the notification's delivery channels.
     *
     * @return array<int, string>
     */
    // public function via(object $notifiable): array
    // {
    //     return ['mail'];
    // }

    /**
     * Get the mail representation of the notification.
     */
    // public function toMail(object $notifiable): MailMessage
    // {
    //     // return (new MailMessage)
    //     //             ->line('The introduction to the notification.')
    //     //             ->action('Notification Action', url('/'))
    //     //             ->line('Thank you for using our application!');

        
    //     // Generate the verification URL
    //     $verificationUrl = $this->verificationUrl($notifiable);

    //     return (new MailMessage)
    //         ->subject('Verify Your Email Address')
    //         ->view('emails.verify-email', [
    //             'user' => $notifiable,
    //             'verificationUrl' => $verificationUrl,
    //         ]);
   
    // }

    public function toMail($notifiable)
    {
        return (new MailMessage)
            ->subject('Verify Your Email Address')
            ->greeting('Hello ' . $notifiable->name . ',')
            ->line('Thank you for registering! Please verify your email address by clicking the button below.')
            ->action('Verify Email', $this->verificationUrl($notifiable))
            ->line('If you did not create an account, no further action is required.')
            ->salutation('Best Regards, ' . config('app.name'));
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
