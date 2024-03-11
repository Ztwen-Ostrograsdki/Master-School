<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Responsible;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobCompletedClasseCreation implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $school_year_model;

    public $classe;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, SchoolYear $school_year_model, User $user)
    {
        $this->classe = $classe;

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
        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        if($classe && $school_year_model){

            Responsible::create(['school_year_id' => $school_year_model->id, 'classe_id' => $classe->id]);

            $position = $classe->getClassePosition();

            $classe->update(['position' => $position]);

            $classe->loadClasseDataOfPositionAndPromotionFilial();

        }
    }
}
