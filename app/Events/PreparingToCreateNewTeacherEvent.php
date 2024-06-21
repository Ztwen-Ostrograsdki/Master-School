<?php

namespace App\Events;

use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PresenceChannel;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Contracts\Broadcasting\ShouldBroadcast;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class PreparingToCreateNewTeacherEvent implements ShouldBroadcastNow
{
    use Dispatchable, InteractsWithSockets, SerializesModels;
    
    public $name;

    public $surname;

    public $nationality;

    public $contacts;

    public $marital_status;

    public $level_id;

    public $user;

    public $school_year_model;

    public $subject;

    public $teacher;

    public $updating = false;

    public $edit_subject = false;

    public $updating_subject = false;

    public $old_subject;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct($contacts, $level_id, $name, $nationality, $marital_status, SchoolYear $school_year_model, Subject $subject, $surname, User $user, $updating, $updating_subject = false, $old_subject = null)
    {
        $this->name = $name;

        $this->surname = $surname;

        $this->nationality = $nationality;

        $this->contacts = $contacts;

        $this->marital_status = $marital_status;

        $this->level_id = $level_id;

        $this->user = $user;

        $this->school_year_model = $school_year_model;

        $this->subject = $subject;

        $this->updating = $updating;

        $this->updating_subject = $updating_subject;

        $this->old_subject = $old_subject;
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
