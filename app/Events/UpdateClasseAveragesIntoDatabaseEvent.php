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

class UpdateClasseAveragesIntoDatabaseEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classe;

    public $user;

    public $semestre;

    public $school_year_model;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, $semestre, SchoolYear $school_year_model)
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->semestre = $semestre;

        $this->school_year_model = $school_year_model;
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
