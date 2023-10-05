<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobDataMigrationToTheNewSchoolYear implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $school_year_model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(SchoolYear $school_year_model, User $user)
    {
        $this->school_year_model = $school_year_model;

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->doJob($this->school_year_model);
    }


    public function doJob($school_year_model)
    {

        if($school_year_model){

            DB::transaction(function($e) use ($school_year_model){

                $classes = Classe::all();

                $classe_groups = ClasseGroup::all();

                $subjects = Subject::all();


                if(count($classes) > 0){

                    foreach($classes as $classe){

                        $school_year_model->classes()->attach($classe->id);
                    }

                }

                if(count($classe_groups) > 0){

                    foreach($classe_groups as $clg){

                        $school_year_model->classe_groups()->attach($clg->id);


                    }
                }

                if(count($subjects) > 0){

                    foreach($subjects as $subject){

                        $school_year_model->subjects()->attach($subject->id);

                    }
                }

            });


        }



    }
}
