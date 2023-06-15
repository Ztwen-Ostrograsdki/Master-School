<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class SendTokenToBlockedUserForVerification extends Notification
{
    use Queueable;

    public $key;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($original_key)
    {
        $this->key = $original_key;
    }

    /**
     * Get the notification's delivery channels.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function via($notifiable)
    {
        return ['mail'];
    }

    /**
     * Get the mail representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return \Illuminate\Notifications\Messages\MailMessage
     */
    public function toMail($notifiable)
    {
       return (new MailMessage)
                    ->subject("Déblocage de compte")
                    ->line("Monsieur/Madame {$notifiable->pseudo}, vous avez fait une demande de déblocage de compte Ztwen-Oströgrasdki")
                    ->line("Nous procédons donc à une vérification")
                    ->line("Si vous ne reconnaissez pas cette requête veuillez juste ignorer ce courriel")
                    ->line("")
                    ->line("La clé est: {$this->key}")
                    ->line("")
                    ->line("Nous vous remercions de votre fidélité");
    }

        
    public function getUrl($notifiable)
    {
        return $this->verificationUrl($notifiable);
    }


    /**
     * Get the array representation of the notification.
     *
     * @param  mixed  $notifiable
     * @return array
     */
    public function toArray($notifiable)
    {
        return [
            //
        ];
    }

    protected function verificationUrl($notifiable)
    {
        return URL::temporarySignedRoute(
            'confirmed-email-verification',
            Carbon::now()->addMinutes(120),
            [
                'id' => $notifiable->getKey(),
                'token' => urlencode($notifiable->email_verified_token),
                'key' => urlencode($notifiable->token),
                'hash' => sha1($notifiable->getEmailForVerification()),
            ]
        );
    }
}
