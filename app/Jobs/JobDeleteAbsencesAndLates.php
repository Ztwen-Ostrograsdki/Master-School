<?php

namespace App\Jobs;

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

class JobDeleteAbsencesAndLates implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $user;

    public $semestre;

    public $pupil_id;

    public $school_year_model;

    public $subject_id;

    public $target = 'absences';

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, $semestre, SchoolYear $school_year_model, $subject_id, $pupil_id = null, $target = 'absences')
    {
        $this->classe = $classe;

        $this->user = $user;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->target = $target;

        $this->subject_id = $subject_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        // $classe->deleteClasseAbsences($semestre, $school_year_model->id, $subject_id);
    }
}
