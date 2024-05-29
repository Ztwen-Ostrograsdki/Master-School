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
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class ClasseMarksDeletionCreatedEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classe;

    public $pupil_id;

    public $school_year_model;

    public $semestre = 'all';

    public $subject = 'all';

    public $type = 'all';

    public $start;

    public $end;

    public $user;

    public $data = [];

    public $total_marks;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, SchoolYear $school_year_model, $semestre = 'all', $subject = 'all', $type = 'all', $start = null, $end = null, $pupil_id = null)
    {
        $this->classe = $classe;

        $this->subject = $subject;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->type = $type;

        $this->end;

        $this->total_marks = rand(14, 204);

        $this->start = $start;

        $this->pupil_id = $pupil_id;

        $this->user = $user;

        $this->data = [
            'subject' => $subject,
            'semestre' => $semestre,
            'type' => $type,
            'start' => $start,
            'end' => $end,
            'pupil_id' => $pupil_id,
        ];
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
