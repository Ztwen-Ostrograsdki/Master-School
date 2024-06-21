<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Mark;
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

class JobDetachPupilsFromSchoolYear implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $classe;

    public $school_year_model;

    public $pupil;

    public $from_data_base = false;

    public $forceDelete = false;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, SchoolYear $school_year_model, Classe $classe, Pupil $pupil, $from_data_base = false, $forceDelete = false)
    {
        $this->user = $user;
        
        $this->school_year_model = $school_year_model;

        $this->pupil = $pupil;

        $this->classe = $classe;

        $this->from_data_base = $from_data_base;

        $this->forceDelete = $forceDelete;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        if($this->batch()->cancelled()){

            return;

        }
        
        $this->doJob();
    }

    public function doJob()
    {

        $pupil = $this->pupil;

        $school_year_model = $this->school_year_model;

        $classe = $this->classe;

        if($classe && $pupil){


            Mark::withoutEvents(function() use($pupil, $school_year_model){

                $pupil->pupilDeleteManager($school_year_model);

            });
            


        }
    }
}
