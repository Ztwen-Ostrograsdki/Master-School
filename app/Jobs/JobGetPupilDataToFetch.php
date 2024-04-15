<?php

namespace App\Jobs;

use App\Models\Level;
use App\Models\Pupil;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class JobGetPupilDataToFetch implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $data;

    public $level;

    /**
     * Create a new job instance.
     *
     * @return void
     */

    public function __construct(User $user, Level $level)
    {
        $this->user = $user;

        $this->level = $level;

    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {

        $tab = [];

        for ($i=0; $i < 20; $i++) { 

            $tab[] = $i;
        }
        // $this->data = Pupil::where('level_id', $this->level->id)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
    }


    public function getData()
    {
        return $this->data;
    }
}
