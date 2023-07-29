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

class JobUpdateClasseSemestrialAverageIntoDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $classe;

    protected $school_year_model;

    protected $semestre;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $semestre, SchoolYear $school_year_model)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;
        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $semestrialAverages = [];

        $classe = $this->classe;

        $school_year_id = $this->school_year_model->id;

        $semestre = $this->semestre;

        $semestrialAverages = $classe->getClasseSemestrialAverageWithRank($semestre, $school_year_id);

        DB::transaction(function($e) use ($semestrialAverages, $school_year_id, $classe, $semestre){

            if($semestrialAverages){

                $old_averages = $classe->averages_of($semestre, $school_year_id);

                if(count($old_averages)){

                    foreach($old_averages as $avv){
                        
                        $avv->delete();
                    }
                }

                foreach($semestrialAverages as $pupil_id => $sm_av){

                    $pupil_sm = $sm_av['pupil'];

                    $moy = $sm_av['moy'];

                    $rank = $sm_av['rank'];

                    $base = $sm_av['base'];

                    $exp = $sm_av['exp'];

                    $min = $sm_av['min'];

                    $max = $sm_av['max'];

                    $id_sm = $sm_av['id'];

                    $mention = $sm_av['mention'];

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

        });
        
    }
}
