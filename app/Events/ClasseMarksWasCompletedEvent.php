<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClasseMarksWasCompletedEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $user;

    public $classe;

    public $subject;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, $classe = null, $subject = null)
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->subject = $subject;
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
