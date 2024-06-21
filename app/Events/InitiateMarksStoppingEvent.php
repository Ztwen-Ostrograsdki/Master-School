<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class InitiateMarksStoppingEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classe = null;

    public $school_year_model;

    public $semestre = null;

    public $level = null;

    public $subject = null;

    public $excepts = []; // array that contains the classes id's that shouldn't concerned

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(?Classe $classe = null, ?Level $level = null, SchoolYear $school_year_model, $semestre = null, ?Subject $subject = null, ?array $excepts = [])
    {
        $this->classe = $classe;

        $this->level = $level;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->subject = $subject;

        $this->excepts = $excepts;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return new PrivateChannel('master');
    }
}
