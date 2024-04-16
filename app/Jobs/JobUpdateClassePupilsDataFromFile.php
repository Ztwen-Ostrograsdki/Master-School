<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobUpdateClassePupilsDataFromFile implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
     public $user;

    public $data;

    public $classe;

    public function __construct(Classe $classe, array $data, User $user)
    {
        $this->data = $data;

        $this->user = $user;

        $this->classe = $classe;
    }


    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $data = $this->data;

        $classe = $this->classe;

        DB::transaction(function($e) use ($data, $classe){

            foreach ($data as $pupil_data){

                $ltpk_matricule = $pupil_data['ltpk_matricule'];

                $firstName = $pupil_data['firstName'];

                $lastName = $pupil_data['lastName'];


                $model = Pupil::where('pupils.classe_id', $classe->id)
                                ->where('pupils.firstName', $firstName)
                                ->first();

                if($model){

                    $db_lastnames = explode(' ', $model->lastName);

                    $file_lastnames = explode(' ', $lastName);

                    foreach($db_lastnames as $name){

                        if(strlen($name) > 2 && in_array($name, $file_lastnames)){

                            $model->update($pupil_data);

                            $model->updatePupilLTPKMatricule($ltpk_matricule);
                            
                        }

                    }
                }
            }

        });
    }
}
