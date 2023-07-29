<?php

namespace App\Jobs;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\PupilCursus;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;

class JobMigratePupilsToClasse implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ModelQueryTrait;



    protected $classe;

    protected $pupils;

    protected $school_year = null;

    protected $move = false;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $pupils, $school_year, $move)
    {
        $this->classe = $classe;

        $this->pupils = $pupils;

        $this->school_year = $school_year;

        $this->move = $move;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        //
    }

    public function migrater($classe, $pupils, $school_year = null, $move = false)
    {

        if(count($pupils)){

            DB::transaction(function($e) use ($classe, $pupils, $school_year, $move){

                $school_year_model = $this->getSchoolYear($school_year);

                $school_year_befor_model = $this->getSchoolYearBefor($school_year);

                foreach($pupils as $pupil){

                    if(is_object($pupil)){

                        $pupil_id = $pupil->id;

                        if($classe->isNotPupilOfThisClasseThisSchoolYear($pupil_id)){

                            $make = ClassePupilSchoolYear::create(['classe_id' => $classe->id, 'pupil_id' => $pupil_id, 'school_year_id' => $school_year_model->id]);

                            if($make && $school_year_model->isNotPupilOfThisSchoolYear($pupil_id)){

                                $old_cursus = $pupil->cursus()->where('pupil_cursuses.school_year_id', $school_year_befor_model->id)->where('pupil_cursuses.classe_id', $pupil->classe_id)->first();

                                if($old_cursus){

                                    $old_cursus->update(['end' => Carbon::now(), 'fullTime' => true]);

                                }
                                else{

                                    $cursus = PupilCursus::create(
                                        [
                                            'classe_id' => $pupil->classe_id,
                                            'pupil_id' => $pupil->id,
                                            'level_id' => $pupil->level_id,
                                            'school_year_id' => $school_year_befor_model->id,
                                            'start' => $pupil->created_at,
                                            'end' => Carbon::now(),
                                            'fullTime' => true,
                                        ]
                                    );


                                }

                                $update_classe = $pupil->update(['classe_id' => $classe->id]);

                                $attach = $school_year_model->pupils()->attach($pupil_id);

                                PupilCursus::create(
                                    [
                                        'classe_id' => $classe->id,
                                        'pupil_id' => $pupil->id,
                                        'level_id' => $classe->level_id,
                                        'school_year_id' => $school_year_model->id,
                                        'start' => Carbon::now(),
                                    ]
                                );


                                if(!$classe->isOldPupilOfThisClasse($pupil_id)){

                                    $classe->classePupils()->attach($pupil_id);
                                }
                            }


                        }
                    }

                }

            });


        }

    }

}
