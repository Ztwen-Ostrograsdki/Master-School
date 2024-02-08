<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ThrowClasseMarksConvertionEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $classe;

    public $convertion_type;

    public $semestre;

    public $subject; 

    public $school_year_model;

    public $pupil_id;


    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $convertion_type, $semestre, SchoolYear $school_year_model, Subject $subject, $pupil_id, User $user)
    {
        $this->convertion_type = $convertion_type;

        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->pupil_id = $pupil_id;

        $this->subject = $subject;

        $this->user = $user;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('user.' . $this->user->id);
    }
}
