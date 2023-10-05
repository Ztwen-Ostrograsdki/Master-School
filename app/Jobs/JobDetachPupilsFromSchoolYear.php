<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobDetachPupilsFromSchoolYear implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $classe;

    public $school_year_model;

    public $pupils = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, SchoolYear $school_year_model, Classe $classe, $pupils)
    {
        $this->user = $user;
        
        $this->school_year_model = $school_year_model;

        $this->pupils = $pupils;

        $this->classe = $classe;
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

        $pupils = $this->pupils;

        $school_year_model = $this->school_year_model;

        $classe = $this->classe;

        if($classe && $pupils && count($pupils) > 0){

            foreach($pupils as $pupil){

                DB::transaction(function($e) use($school_year_model, $pupil, $classe){

                    if($pupil->isPupilOfThisYear($school_year_model->school_year)){

                        $classe_year = $pupil->getClasseAndYear($classe->id, $school_year_model->id);

                        if($classe_year){

                            $pupil->marks()->where('marks.school_year_id', $school_year_model->id)
                                            ->where('marks.classe_id', $classe->id)
                                            ->each(function($mark){

                                $mark->delete();

                            });

                            $pupil->related_marks()->where('related_marks.school_year_id', $school_year_model->id)
                                            ->where('related_marks.classe_id', $classe->id)
                                            ->each(function($r_m){

                                $r_m->delete();

                            });

                            $pupil->lates()->where('pupil_lates.school_year_id', $school_year_model->id)
                                            ->where('pupil_lates.classe_id', $classe->id)
                                            ->each(function($pl){

                                $pl->delete();

                            });

                            $pupil->absences()->where('pupil_absences.school_year_id', $school_year_model->id)
                                            ->where('pupil_absences.classe_id', $classe->id)
                                            ->each(function($p_abs){

                                $p_abs->delete();

                            });


                            $classe_year->classe->classePupils()->attach($pupil->id);

                            $classe_year->delete();

                            $school_year_model->pupils()->detach($pupil->id);

                        }

                    }

                });

            }

        }
    }
}
