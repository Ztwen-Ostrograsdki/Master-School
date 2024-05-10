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

class InitiateClasseParticipationMarksEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $classe;

    public $school_year_model;

    public $subject;

    public $semestre = null;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, SchoolYear $school_year_model, $semestre, Subject $subject, User $user)
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->subject = $subject;

        $this->semestre = $semestre;


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
