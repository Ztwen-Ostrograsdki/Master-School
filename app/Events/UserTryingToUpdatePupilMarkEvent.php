<?php

namespace App\Events;

use App\Models\Mark;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class UserTryingToUpdatePupilMarkEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $mark_editor;

    public $mark;

    public $new_value = 0;

    public $others_data = [];

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Mark $mark, User $mark_editor, $new_value, $others_data = [])
    {
        $this->mark_editor = $mark_editor;

        $this->mark = $mark;

        $this->new_value = $new_value;

        $this->others_data = $others_data;
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
