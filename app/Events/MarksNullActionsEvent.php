<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\Pupil;
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

class MarksNullActionsEvent
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $pupil;

    public $classe;

    public $subject;

    public $user;

    public $school_year_model;

    public $semestre = 1;

    public $action;



    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($action, Classe $classe, $semestre, Subject $subject, SchoolYear $school_year_model, $pupil, User $user)
    {
        $this->action = $action;

        $this->classe = $classe;

        $this->semestre = $semestre;

        $this->school_year_model = $school_year_model;

        $this->subject = $subject;

        $this->pupil = $pupil;

        $this->user = $user;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('reloadMarkChannel.' . $this->user->id);
    }
}
