<?php

namespace App\Events;

use App\Models\Parentable;
use App\Models\Pupil;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ParentRequestToFollowPupilEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $parentable;

    public $pupil;

    public $relation;

    public $authorized;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Parentable $parentable, Pupil $pupil, $relation, bool $authorized = false, User $user)
    {
        $this->parentable = $parentable;

        $this->pupil = $pupil;

        $this->relation = $relation;

        $this->authorized = $authorized;

        $this->user = $user;
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
