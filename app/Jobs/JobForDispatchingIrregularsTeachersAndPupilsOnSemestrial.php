<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Teacher;
use App\Notifications\NotifyTeachersAboutIrregularsSemestrialMarks;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobForDispatchingIrregularsTeachersAndPupilsOnSemestrial implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $teacher;

    public $irregulars_pupils = [];

    public $semestre;

    public $school_year_model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, ?array $irregulars_pupils, $semestre, SchoolYear $school_year_model, Teacher $teacher)
    {
        $this->classe = $classe;

        $this->teacher = $teacher;

        $this->irregulars_pupils = $irregulars_pupils;

        $this->semestre = $semestre;

        $this->school_year_model = $school_year_model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $user = $this->teacher->user;

        $pupils = $this->irregulars_pupils;

        $semestre = $this->semestre;

        $school_year_model = $this->school_year_model;

        $classe = $this->classe;

        if($user && $pupils){

            $user->notify(new NotifyTeachersAboutIrregularsSemestrialMarks($classe, $pupils, $semestre, $school_year_model));

        }
    }
}
