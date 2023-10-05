<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\Pupil;
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
use Illuminate\Support\Str;

class JobProcessingNewsPupilsInsertion implements ShouldQueue
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

            DB::transaction(function($e) use ($pupils, $classe, $school_year_model){
                        
                foreach ($pupils as $pupil_data) { 

                    $firstName = $pupil_data['firstName'];

                    $lastName = $pupil_data['lastName'];

                    $last_id = 0;

                    $last = Pupil::latest()->first();

                    if($last){

                        $last_id = $last->id;
                    }

                    $matricule = date('Y') . '' . Str::random(3) . 'LTPK' . ($last_id + 1);

                    $pupilNameHasAlreadyTaken = Pupil::where('lastName', $lastName)->where('firstName', $firstName)->first();

                    if(!$pupilNameHasAlreadyTaken){

                        $pupil_data['matricule'] = $matricule;

                        try {

                            $pupil = Pupil::create($pupil_data);

                            if($pupil){

                                try {
                                    $joinedToClasseAndSchoolYear = ClassePupilSchoolYear::create(
                                        [
                                            'classe_id' => $classe->id,
                                            'pupil_id' => $pupil->id,
                                            'school_year_id' => $school_year_model->id,
                                        ]
                                    );
                                    if($joinedToClasseAndSchoolYear){
                                        
                                        try {
                                            $school_year_model->pupils()->attach($pupil->id);

                                            $classe->classePupils()->attach($pupil->id);
                                        
                                        } catch (Exception $e3) { }
                                    }
                                    
                                } catch (Exception $e2) { }
                            }
                            else{
                               
                            }
                            
                        } catch (Exception $e1) { }

                    }
                }
            });
        }
    }
}
