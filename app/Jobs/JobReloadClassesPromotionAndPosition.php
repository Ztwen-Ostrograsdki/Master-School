<?php

namespace App\Jobs;

use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobReloadClassesPromotionAndPosition implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $school_year_model;


    /**
     * Create a new Job instance.
     *
     * @return void
     */
    public function __construct(SchoolYear $school_year_model, User $user)
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

        $school_year_model = $this->school_year_model;

        $classes = $school_year_model->classes;

        if(count($classes) > 0 && $school_year_model){

            foreach($classes as $classe){

                $position = $classe->getClassePosition();

                $classe->update(['position' => $position]);

                $classe->loadClasseDataOfPositionAndPromotionFilial();

            }

        }
    }
}
