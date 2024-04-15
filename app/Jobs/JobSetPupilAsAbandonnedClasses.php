<?php

namespace App\Jobs;

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

class JobSetPupilAsAbandonnedClasses implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $pupil;

    public $school_year_model;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Pupil $pupil, User $user, SchoolYear $school_year_model)
    {
        $this->user = $user;

        $this->pupil = $pupil;

        $this->school_year_model = $school_year_model;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->pupil->abandonned ? $this->pupil->update(['abandonned' => false]) 
                                 : $this->pupil->update(['abandonned' => true]);
    }
}
