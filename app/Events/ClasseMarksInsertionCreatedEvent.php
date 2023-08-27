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




    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($data)
    {
        // $this->data = new ClasseMarksInsertionDriver($data);
        $this->data = $data;

        $this->classe = $data['classe'];

        $this->subject = $data['subject'];

        $this->user = $data['user'];


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
