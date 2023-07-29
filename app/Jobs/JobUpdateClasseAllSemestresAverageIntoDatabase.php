<?php

namespace App\Jobs;

use App\Models\Averages;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobUpdateClasseAllSemestresAverageIntoDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $classe;

    protected $school_year_model;

    protected $semestres;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, array $semestres, SchoolYear $school_year_model)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestres = $semestres;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $allSemestrialAverages = [];

        $classe = $this->classe;

        $school_year_id = $this->school_year_model->id;

        $semestres = $this->semestres;

        foreach($semestres as $sm){

            $allSemestrialAverages[$sm] = $classe->getClasseSemestrialAverageWithRank($sm, $school_year_id);
        } 

        DB::transaction(function($e) use ($allSemestrialAverages, $school_year_id, $classe){

            foreach($allSemestrialAverages as $semestre => $semestrialAverages){


                    if($semestrialAverages){

                        $old_averages = $classe->averages_of($semestre, $school_year_id);

                        if(count($old_averages)){

                            foreach($old_averages as $avv){
                                
                                $avv->delete();
                            }
                        }


                        foreach($semestrialAverages as $pupil_id_sm => $p_sm_av){

                            $pupil_sm = $p_sm_av['pupil'];

                            $moy = $p_sm_av['moy'];

                            $rank = $p_sm_av['rank'];

                            $base = $p_sm_av['base'];

                            $exp = $p_sm_av['exp'];

                            $min = $p_sm_av['min'];

                            $max = $p_sm_av['max'];

                            $id_sm = $p_sm_av['id'];

                            $mention = $p_sm_av['mention'];

                            $sm_average = $pupil_sm->average($classe->id, $semestre, $school_year_id);

                            if($sm_average){

                                $data = ['moy' => $moy, 'rank' => $rank, 'base' => $base, 'exp' => $exp, 'mention' => $mention, 'min' => $min, 'max' => $max];

                                $sm_average->update($data);

                            }
                            else{

                                $data = [
                                    'classe_id' => $classe->id,
                                    'school_year_id' => $school_year_id, 
                                    'semestre' => $semestre, 
                                    'moy' => $moy, 
                                    'rank' => $rank, 
                                    'base' => $base, 
                                    'exp' => $exp, 
                                    'pupil_id' => $id_sm, 
                                    'mention' => $mention, 
                                    'min' => $min, 
                                    'max' => $max
                                ];

                                Averages::create($data);

                            }
                        }

                    }

            }

        });
        
    }
    
   
}
