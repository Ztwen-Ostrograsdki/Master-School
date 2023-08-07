<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobUpdatePupilMarksClasseIdAfterMovingToNewClasse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;


    public $classe;

    public $pupil;

    public $school_year_model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $newClasse, Pupil $pupil, SchoolYear $school_year_model)
    {
        $this->classe = $newClasse;

        $this->pupil = $pupil;

        $this->school_year_model = $school_year_model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function($e){

            $classe_group = $this->classe->classe_group;

            $marks = $this->pupil->marks()->where('marks.school_year_id', $this->school_year_model->id)->each(function($mark){

                if($classe_group && $classe_group->hasThisSubject($mark->subject_id)){

                    $mark->update(['classe_id' => $this->classe->id]);
                }

            });

            $related_marks = $this->pupil->related_marks()->where('related_marks.school_year_id', $this->school_year_model->id)->each(function($related_mark){

                if($classe_group && $classe_group->hasThisSubject($mark->subject_id)){

                    $related_mark->update(['classe_id' => $this->classe->id]);

                }

            });

        });
    }
}
