<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\PrincipalTeacher;
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

class JobClasseRefereesManager implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $user;

    public $data;

    public $school_year_model;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $data, SchoolYear $school_year_model, User $user)
    {
        $this->classe = $classe;

        $this->user = $user;

        $this->school_year_model = $school_year_model;

        $this->data = $data;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->user->isAdminAs('master')){

            $this->doJob();
        }
    }


    public function doJob()
    {

        $data = $this->data;

        $school_year_model = $this->school_year_model;

        $classe = $this->classe;

        $user = $this->user;

        DB::transaction(function($e) use ($data, $classe, $school_year_model, $user){

            if($data['teacher']){

                $t_c = $data['teacher']['create'];

                $t_u = $data['teacher']['update'];

                if($t_u){

                    $principal = $classe->currentPrincipal();

                    $principal->update(['teacher_id' => $t_c]);
                }
                elseif($t_c){

                    PrincipalTeacher::create(['teacher_id' => $t_c, 'classe_id' => $classe->id, 'school_year_id' => $school_year_model->id]);
                }

            }

            $responsibles = $data['pupils'];

            if($responsibles){

                $model = $classe->currentRespo();

                if($model){

                    $r1 = $responsibles['respo1'];

                    $r2 = $responsibles['respo2'];

                    if($r1 && $r2){

                        $update = $model->update(['respo_1' => $r1, 'respo_2' => $r2]);
                    }
                    elseif($r1 && !$r2){

                        $update = $model->update(['respo_1' => $r1]);

                    }
                    elseif(!$r1 && $r2){

                        $update = $model->update(['respo_2' => $r2]);

                    }

                }
            }
        });
    }
}
