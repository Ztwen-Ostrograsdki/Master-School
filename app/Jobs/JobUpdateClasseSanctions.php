<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\ClasseSanctionables;
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

class JobUpdateClasseSanctions implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $subject;

    public $user;

    public $school_year_model;

    public $semestre = 1;

    public $activated = true;

    /**
     * Create a new Job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, User $user, SchoolYear $school_year_model, $semestre, Subject $subject, $activated = true)
    {
        $this->activated = $activated;

        $this->classe = $classe;

        $this->subject = $subject;

        $this->user = $user;

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
        $this->doJob();
    }




    public function doJob()
    {
        $school_year_model = $this->school_year_model;

        $classe = $this->classe;

        $subject = $this->subject;

        $activated = $this->activated;

        $semestre = $this->semestre;

        $user = $this->user;


        DB::transaction(function($e) use ($school_year_model, $classe, $subject, $activated, $semestre, $user){

            $sanction = $classe->subject_sanctions($semestre, $subject->id, $school_year_model->id, !$activated);

            if($sanction){

                $sanction->update(['activated' => $activated, 'updator_id' => $user->id]);

            }
            else{

                ClasseSanctionables::create([
                    'classe_id' => $classe->id, 
                    'subject_id' => $subject->id, 
                    'semestre' => $semestre, 
                    'school_year_id' => $school_year_model->id, 
                    'activated' => $activated,
                    'creator_id' => $user->id,
                    'editor_id' => $user->id,
                ]);

            }
        });
    }
}
