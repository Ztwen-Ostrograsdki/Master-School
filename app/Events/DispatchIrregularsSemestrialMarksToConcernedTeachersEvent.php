<?php

namespace App\Events;

use App\Models\Classe;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DispatchIrregularsSemestrialMarksToConcernedTeachersEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $teachers = [];

    public $semestre;

    public $classe;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $semestre, ?array $teachers)
    {
        $this->teachers = $teachers;

        $this->semestre = $semestre;

        $this->classe = $classe;
    }

    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        $channels = [];

        $teachers = $this->teachers;

        if(count($teachers)){

            foreach($teachers as $teacher){

                if($teacher && $teacher->user){

                    $user = $teacher->user;

                    $channel = new PrivateChannel('user.' . $user->id);

                    $channels[] = $channel;

                }

            }

        }


        return [$channels, new PrivateChannel('master')];
    }
}
