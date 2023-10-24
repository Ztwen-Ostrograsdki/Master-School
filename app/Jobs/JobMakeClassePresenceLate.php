<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\PupilAbsences;
use App\Models\PupilLates;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobMakeClassePresenceLate implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $classe;

    public $data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, $data)
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->data = $data;
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
        $data = $this->data;

        $default = $data['default'];

        $lates_data = $data['lates_data'];

        $abs_data = $data['abs_data'];

        if($default && $lates_data){

            DB::transaction(function($el) use ($default, $lates_data){

                $date = $default['date'];

                $semestre = $default['semestre'];

                $horaire = $default['horaire'];

                $subject_id = $default['subject_id'];

                $school_year_model = $default['school_year_model'];


                foreach($lates_data as $pl_id => $p_l_data){

                    $p_l = Pupil::find($pl_id);
        
                    if($p_l){

                        $yet = $p_l->wasLateThisDayFor($date, $school_year_model->id, $semestre, $subject_id);

                        if(!$yet){

                            $coming_hour = $p_l_data['coming_hour'];

                            $motif = $p_l_data['motif'];

                            $duration = $p_l_data['duration'];
                            
                            PupilLates::create([
                                'horaire' => $horaire,
                                'motif' => $motif,
                                'date' => $date,
                                'duration' => $duration,
                                'coming_hour' => $coming_hour,
                                'school_year_id' => $school_year_model->id,
                                'school_year' => $school_year_model->school_year,
                                'pupil_id' => $p_l->id,
                                'semestre' => $semestre,
                                'subject_id' => $subject_id,
                                'classe_id' => $this->classe->id,
                            ]);
                        }
                    }
                }
            });

        }


        if($default && $abs_data){

            DB::transaction(function($ea) use ($default, $abs_data){

                $date = $default['date'];

                $semestre = $default['semestre'];

                $horaire = $default['horaire'];

                $subject_id = $default['subject_id'];

                $school_year_model = $default['school_year_model'];

                foreach($abs_data as $pa_id => $p_a_data){

                    $p_a = Pupil::find($pa_id);
        
                    if($p_a){

                        $yet = $p_a->isAbsentThisDay($date, $school_year_model->id, $semestre, $subject_id);

                        if(!$yet){

                            $motif = $p_a_data['motif'];

                            PupilAbsences::create([
                                'horaire' => $horaire,
                                'motif' => $motif,
                                'date' => $date,
                                'school_year_id' => $school_year_model->id,
                                'school_year' => $school_year_model->school_year,
                                'pupil_id' => $p_a->id,
                                'semestre' => $semestre,
                                'subject_id' => $subject_id,
                                'classe_id' => $this->classe->id,
                            ]);
                        }
                    }
                }
            });

        }

    }
}
