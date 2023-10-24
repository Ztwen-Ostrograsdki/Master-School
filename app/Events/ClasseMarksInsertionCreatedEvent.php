<?php

namespace App\Events;

use App\Helpers\ZtwenDrivers\ClasseMarksInsertionDriver;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClasseMarksInsertionCreatedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $data;

    public $classe;

    public $subject;

    public $user;

    public $school_year_model;

    public $semestre = 1;

    public $related = false;

    public $related_data = [];




    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data, $related = false, $related_data = [])
    {
        $this->data = $data;

        $this->classe = $data['classe'];

        $this->subject = $data['subject'];

        $this->user = $data['user'];

        $this->school_year_model = $data['school_year_model'];

        $this->semestre = $data['semestre'];

        $this->related = $related;

        $this->related_data = $related_data;

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
