<?php

namespace App\Notifications;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Notification;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\URL;

class NotifyTeachersAboutIrregularsSemestrialMarks extends Notification
{
    use Queueable;

    public $classe;

    public $pupils = [];

    public $school_year_model;

    public $semestre;

    /**
     * Create a new notification instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, array $pupils, $semestre, SchoolYear $school_year_model)
    {
        $this->classe = $classe;

        $this->pupils = $pupils;

        $this->semestre = $semestre;

        $this->school_year_model = $school_year_model;
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
        $info = "";

        $pupils = [];

        foreach($this->pupils as $pupil){

            $pupils[] = $pupil->getName();

        }

        $infos = "Les apprenants concernés sont : " . implode(' - ', $pupils);

        return (new MailMessage)
                    ->line('NOMBRE DE NOTES MINIMAL IRREGULIER')
                    ->line($infos)
                    ->line("Ces apprenants n'ont pas le nombre de notes minimal dans votre matière, réquis pour le calcul de moyennes du semestre/trimestre " . $this->semestre . " !")
                    ->line("Toutefois, si ce couriel est une erreure et que vous n'êtes pas concerné pas ce retard veuillez signaler cela à l'administration. Et veuillez sans ce cas accepter nos sincères excuses!")
                    ->action('Connectez-vous à votre compte enseignant ici', $this->getUrl($notifiable))
                    ->line("Merci de prendre en compte ce couriel afin que les administratifs puissent finaliser les bulletins de notes du semestre/trimestre " . $this->semestre. " !");
    }


    protected function getUrl($notifiable)
    {
        return URL::route(
            'teacher_profil_as_user',
            [
                'id' => $notifiable->teacher->id,
                'classe_id' => $this->classe->id,
                'slug' => $this->classe->getSlug()
            ]
        );
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
