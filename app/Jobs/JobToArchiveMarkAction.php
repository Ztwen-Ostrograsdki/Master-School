<?php

namespace App\Jobs;

use App\Models\Mark;
use App\Models\MarkActionHistory;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobToArchiveMarkAction implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $mark;

    public $description = "La note a été édité par cet utilisateur";

    public $user; // The user who has made the action on the mark

    public $value;

    public $action; // UPDATE - DELETE - CREATE

    public $school_year_model;

    public $semestre;

    public $classe;

    public $subject;

    public $pupil;

    private $table_properties = ['classe_id', 'subject_id', 'school_year_id', 'semestre', 'user_id', 'value', 'description', 'action', 'mark_id', 'mark_index', 'type', 'session', 'exam_name', 'trimestre'
    ];

    private $actions = ['DELETE', 'CREATE', 'UPDATE'];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($action, $description, Mark $mark, User $user)
    {
        $this->mark = $mark;

        $this->user = $user;

        $this->action = $action;

        $this->description = $description;

        if($mark){

            $this->value = $mark->value;

            $this->subject = $mark->subject;

            $this->semestre = $mark->semestre;

            $this->classe = $mark->classe;

            $this->pupil = $mark->pupil;

            $this->school_year_model = $mark->school_year;

        }
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $classe = $this->classe;

        $subject_id = $this->subject->id;

        $school_year_model = $this->school_year_model;

        $semestre = $this->semestre;

        $user = $this->user;

        $value = $this->value;

        $description = $this->description;

        $action = $this->action;

        $mark = $this->mark;

        $mark_index = $mark->mark_index;

        $type = $mark->type;

        $session = $mark->session;

        $exam_name = $mark->exam_name;
        
        $data = [
            'classe_id' => $classe->id,
            'subject_id' => $subject_id, 
            'school_year_id' => $school_year_model->id, 
            'semestre' => $semestre, 
            'user_id' => $user->id, 
            'value' => $value, 
            'description' => $description, 
            'action' => $action, 
            'mark_id' => $mark->id, 
            'mark_index' => $mark_index, 
            'type' => $type, 
            'session' => $session, 
            'exam_name' => $exam_name
        ];

        MarkActionHistory::create($data);
    }



}
