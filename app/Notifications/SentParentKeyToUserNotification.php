<?php

namespace App\Notifications;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;

class SentParentKeyToUserNotification extends Notification
{
    use Queueable;

    public $key;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct($key)
    {
        $this->key = $key;
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
                    ->subject("Gestion parentale de compte")
                    ->line("Monsieur/Madame {$notifiable->name}, vous avez fait une demande de suivi de vos enfants sur la plateforme de notre école")
                    ->line("Nous vous envoyons votre code personnel secret")
                    ->line("Vous devriez être le seul à connaitre ce compte!")
                    ->line("Vous ne pourriez acceder à certaines données sans ce code!")
                    ->line("Et nous serons dans l'incapacité de vous le refournir si oublié!")
                    ->line("")
                    ->line("Le code : ###{$this->key}")
                    ->line("")
                    ->line("Nous vous remercions pour votre fidélité");
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
}
