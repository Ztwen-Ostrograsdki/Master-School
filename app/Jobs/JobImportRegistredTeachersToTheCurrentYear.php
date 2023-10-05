<?php

namespace App\Jobs;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
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

class JobImportRegistredTeachersToTheCurrentYear implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    use ModelQueryTrait;

    public $user;

    public $school_year_model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, SchoolYear $school_year_model)
    {
        $this->user = $user;

        $this->school_year_model = $school_year_model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->doJob($this->user, $this->school_year_model);
    }



    public function doJob($user, $school_year_model)
    {

        if($user->isAdminAs('master')){

            DB::transaction(function($e) use ($user, $school_year_model){

                $school_year_befor_model = $this->getSchoolYearBefor($school_year_model->school_year);

                if($school_year_befor_model && $school_year_befor_model->id !== $school_year_model->id){

                    $school_year_befor_model->teachers()->each(function($teacher) use ($school_year_model){

                        if($teacher->isNotTeacherOfThisYear($school_year_model->school_year)){

                            $school_year_model->teachers()->attach($teacher->id);

                        }

                    });
                }

            });


        }


    }
}
