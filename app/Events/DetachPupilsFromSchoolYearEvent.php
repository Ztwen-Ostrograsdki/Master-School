<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DetachPupilsFromSchoolYearEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $classe;

    public $school_year_model;

    public $pupils = [];

    public $from_data_base = false;

    public $forceDelete = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, SchoolYear $school_year_model, Classe $classe, array $pupils, $from_data_base = false, $forceDelete = false)
    {
        $this->user = $user;
        
        $this->school_year_model = $school_year_model;

        $this->pupils = $pupils;

        $this->classe = $classe;

        $this->from_data_base = $from_data_base;

        $this->forceDelete = $forceDelete;

    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('master'), new PrivateChannel('user.' . $this->user->id)];
    }
}
