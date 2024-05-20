<?php

namespace App\Events;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PrepareClasseMarksExcelFileDataInsertionToDatabaseEvent implements ShouldBroadcast
{
    use Dispatchable, InteractsWithSockets, SerializesModels;

    public $classe;

    public $school_year_model;

    public $file_name;

    public $semestre;

    public $file_path;

    public $subject;

    public $user;



    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $file_name, $file_path, SchoolYear $school_year_model, $semestre, Subject $subject, User $user)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->file_name = $file_name;

        $this->file_path = $file_path;

        $this->subject = $subject;

        $this->user = $user;

    }


    /**
     * Get the channels the event should broadcast on.
     *
     * @return \Illuminate\Broadcasting\Channel|array
     */
    public function broadcastOn()
    {
        return [new PrivateChannel('user.' . $this->user->id), new PrivateChannel('master')];
    }
}
