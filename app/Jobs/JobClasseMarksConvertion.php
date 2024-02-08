<?php

namespace App\Jobs;

use App\Models\Classe;
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

class JobClasseMarksConvertion implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    
    public $user;

    public $classe;

    public $convertion_type;

    public $semestre;

    public $subject; 

    public $school_year_model;

    public $pupil_id;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $convertion_type, $semestre, SchoolYear $school_year_model, Subject $subject, $pupil_id, User $user)
    {
        $this->convertion_type = $convertion_type;

        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->pupil_id = $pupil_id;

        $this->subject = $subject;

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
        DB::transaction(function($e){

            $classe = $this->classe;

            $convertion_type = $this->convertion_type;

            $school_year_model = $this->school_year_model;

            $semestre = $this->semestre;

            $subject = $this->subject;

            $types = explode('-to-', $convertion_type);

            $from = $types[0];

            $to = $types[1];

            $marks = [];

            if($classe && $subject && $school_year_model && $semestre && $from && $to){

                $pupils = $classe->getPupils($school_year_model->id);

                if($pupils && count($pupils) > 0){

                    foreach($pupils as $p){

                        $mark = $p->marks()->where('marks.type', $from)
                                           ->where('marks.classe_id', $classe->id)
                                           ->where('marks.school_year_id', $school_year_model->id)
                                           ->where('marks.subject_id', $subject->id)
                                           ->where('marks.semestre', $semestre)
                                           ->orderBy('marks.mark_index', 'desc')
                                           ->first();

                        if($mark){

                            $marks[] = $mark;

                        }
                    }

                    if(count($marks) > 0){

                        foreach($marks as $m){

                            $m->update(['type' => $to]);

                        }


                    }
                }

            }



        });


    }
}
