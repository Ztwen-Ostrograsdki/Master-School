<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\ClasseMarksStoppedForSchoolYear;
use App\Models\Level;
use App\Models\MarkStopped;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobMarksStoppedManager implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $level;

    public $school_year_model;

    public $semestre;

    public $subject;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, Level $level, SchoolYear $school_year_model, $semestre = null, ?Subject $subject = null)
    {
        $this->classe = $classe;

        $this->level = $level;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->subject = $subject;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function($e){

            $school_year_model = $this->school_year_model;

            $semestre = $this->semestre;

            $classe = $this->classe;

            $level = $this->level;

            $subject = $this->subject;

            $data0 = ['school_year_id' => $school_year_model->id, 'stopped' => 1, 'semestre' => $semestre, 'level_id' => $level->id];


            $old_stp0 = MarkStopped::where($data0);

            if($old_stp0->count() > 0){

                $old_stp0->each(function($stp0){

                    $stp0->delete();

                });
            }

            MarkStopped::create($data0);

            $data1 = ['school_year_id' => $school_year_model->id, 'classe_id' => $classe->id, 'semestre' => $semestre, 'level_id' => $level->id];

            if($subject && is_object($subject)){

                $data1['subject_id'] = $subject->id;

            }

            $old_stp1 = ClasseMarksStoppedForSchoolYear::where($data1);

            if($old_stp1->count() > 0){

                $old_stp1->each(function($stp1){

                    $stp1->delete();

                });
            }

            ClasseMarksStoppedForSchoolYear::create($data1);
        });
    }
}
