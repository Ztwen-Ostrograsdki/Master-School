<?php

namespace App\Jobs;

use App\Models\Pupil;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobUpdateClassePupilsMatricule implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user;

    public $matricule_data;

    public function __construct(array $matricule_data, User $user)
    {
        $this->matricule_data = $matricule_data;

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $matricule_data = $this->matricule_data;

        DB::transaction(function($e) use ($matricule_data){

            foreach($matricule_data as $pupil_id => $matricule){

                $pupil = Pupil::find($pupil_id);

                if($pupil){

                    $pupil->updatePupilLTPKMatricule(trim($matricule));

                }

            }

        });
    }
}
