<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobMarksNullActionsManager implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private $actions = [
        'delete' => 'dl', 
        'activate' => 'a', 
        'desactivate' => 'd', 
        'normalize|standardize' => 's'
    ];

    public $pupil;

    public $classe;

    public $subject;

    public $user;

    public $school_year_model;

    public $semestre = 1;

    public $action;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($action, Classe $classe, $semestre, Subject $subject, SchoolYear $school_year_model, $pupil, User $user)
    {
        $this->action = $action;

        $this->classe = $classe;

        $this->semestre = $semestre;

        $this->school_year_model = $school_year_model;

        $this->subject = $subject;

        $this->pupil = $pupil;

        $this->user = $user;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->doJob();
    }


    public function doJob()
    {
        $pupil_id = null;

        $action = $this->action;

        $semestre = $this->semestre;

        $school_year_model = $this->school_year_model;

        $subject = $this->subject;

        $pupil = $this->pupil;

        if($pupil){

            $pupil_id = $pupil->id;

        }

        $marks = $this->classe->getClasseNullMarks($semestre, $school_year_model->id, $subject->id, $pupil_id);

        if(count($marks) > 0){

            DB::transaction(function($e) use ($marks, $action){

                foreach($marks as $mark){

                    if($action == 'dl'){

                        //TO DELETE ALL NULLS MARKS

                        $mark->forceDelete();

                    }
                    elseif($action == 'a'){

                        //TO ACTIVATE ALL NULLS MARKS

                        $mark->update(['forced_mark' => true]);   

                    }
                    elseif($action == 'd'){

                        //TO DESACTIVATE ALL NULLS MARKS OR TO FORGET THEM

                        $mark->update(['forced_mark' => false, 'forget' => true]);   

                    }
                    elseif($action == 's'){

                        //TO NORMALIZE ALL NULLS MARKS

                        $mark->update(['forced_mark' => false, 'forget' => false]);   

                    }

                }

            });
        }
    }
}
