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

class UpdateClasseSanctionsEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classe;

    public $subject;

    public $user;

    public $school_year_model;

    public $semestre = 1;

    public $activated = true;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, User $user, SchoolYear $school_year_model, $semestre = 1, Subject $subject, bool $activated = true)
    {
        $this->activated = $activated;

        $this->classe = $classe;

        $this->subject = $subject;

        $this->user = $user;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

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
