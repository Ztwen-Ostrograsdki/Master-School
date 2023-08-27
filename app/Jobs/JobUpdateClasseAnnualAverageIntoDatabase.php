<?php

namespace App\Jobs;

use App\Models\Averages;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobUpdateClasseAnnualAverageIntoDatabase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
protected $classe;

    protected $school_year_model;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, SchoolYear $school_year_model)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $classe = $this->classe;

        $school_year_id = $this->school_year_model->id;


        DB::transaction(function($ee) use($classe, $school_year_id){

            $annualAverages = $classe->getClasseAnnualAverageWithRank($school_year_id);

            if($annualAverages){

                $old_averages = $classe->averages_of(null, $school_year_id);

                if(count($old_averages)){

                    foreach($old_averages as $avv){
                        
                        $avv->delete();
                    }
                }


                $pupil_id = null;

                $pupil_id_an = null;

                foreach($annualAverages as $pupil_id_an => $p_an_av){

                    $pupil_an = $p_an_av['pupil'];

                    $moy_an = $p_an_av['moy'];

                    $rank_an = $p_an_av['rank'];

                    $base_an = $p_an_av['base'];

                    $exp_an = $p_an_av['exp'];

                    $min_an = $p_an_av['min'];

                    $max_an = $p_an_av['max'];

                    $mention_an = $p_an_av['mention'];

                    $id_an = $p_an_av['id'];

                    $an_average = $pupil_an->annual_average($classe->id, $school_year_id);

                    if($an_average){

                        $data_an = ['moy' => $moy_an, 'rank' => $rank_an, 'base' => $base_an, 'exp' => $exp_an, 'mention' => $mention_an, 'min' => $min_an, 'max' => $max_an];

                        $an_average->update($data_an);

                    }
                    else{

                        Averages::create([
                            'classe_id' => $classe->id,
                            'school_year_id' => $school_year_id, 
                            'semestre' => null, 
                            'moy' => $moy_an, 
                            'rank' => $rank_an, 
                            'base' => $base_an, 
                            'exp' => $exp_an, 
                            'pupil_id' => $pupil_an->id, 
                            'mention' => $mention_an, 
                            'min' => $min_an, 
                            'max' => $max_an
                        ]);

                    }
                }



            }

        });
    }
}
