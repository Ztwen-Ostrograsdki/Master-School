<?php

namespace App\Jobs;

use App\Models\Pupil;
use App\Models\School;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobUpdateSchoolModel implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $classMapping;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $classMapping, $column)
    {
        $this->user = $user;

        $this->classMapping = $classMapping;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $school = School::first();

        $total = $this->classMapping::all()->count();

        $school->update(['pupils_counter' => $total]);
    }
}
