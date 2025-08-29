<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class AdminResetPasswordNotification extends Notification
{
    use Queueable;

    public $token;
    /**
     * Create a new notification instance.
     */
    public function __construct($token)
    {
        $this->token = $token;
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
    public function toMail($notifiable)
{
    $url = url(route('admin.password.reset', [
        'token' => $this->token,
        'email' => $notifiable->email,
    ]));

    return (new MailMessage)
        ->subject('Admin Password Reset')
      	->greeting('Hello!')
        ->line('Click the button below to reset your password.')
        ->action('Reset Password', $url)
        ->line('If you did not request this, ignore the email.')
      	->salutation("Regards,\nTechnotronixs");
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
