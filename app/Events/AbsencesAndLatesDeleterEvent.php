<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class AbsencesAndLatesDeleterEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classe;

    public $user;

    public $semestre;

    public $pupil_id;

    public $school_year_model;

    public $subject_id;

    public $target = 'absences';

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, $semestre, SchoolYear $school_year_model, $subject_id, $pupil_id = null, $target = 'absences')
    {
        $this->classe = $classe;

        $this->user = $user;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->target = $target;

        $this->subject_id = $subject_id;
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
