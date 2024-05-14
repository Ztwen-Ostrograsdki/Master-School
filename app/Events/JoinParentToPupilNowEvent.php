<?php

namespace App\Events;

use App\Models\ParentRequestToFollowPupil;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class JoinParentToPupilNowEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $parentRequestToFollowPupil;

    public $user;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        $this->parentRequestToFollowPupil = $parentRequestToFollowPupil;

        $this->user = $parentRequestToFollowPupil->parentable->user;
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
