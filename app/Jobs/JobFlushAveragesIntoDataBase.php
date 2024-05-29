<?php

namespace App\Jobs;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
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

class JobFlushAveragesIntoDataBase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels, ModelQueryTrait;

    public $user;

    public $classe;

    public $school_year_model;

    public $semestre = null;

    public $tries = 2;

    public $backoff = 2;

    /**
     * Create a new event instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, SchoolYear $school_year_model, $semestre = null)
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

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

        DB::transaction(function($e){

            $this->classe->averages()->where('averages.school_year_id', $this->school_year_model->id)->each(function($average){

                $average->delete();

            });

        });
    }
}
