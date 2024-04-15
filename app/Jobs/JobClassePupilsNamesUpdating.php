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

class JobClassePupilsNamesUpdating implements ShouldQueue
{
   use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public $user;

    public $names_data;

    public function __construct(array $names_data, User $user)
    {
        $this->names_data = $names_data;

        $this->user = $user;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $names_data = $this->names_data;

        DB::transaction(function($e) use ($names_data){

            foreach($names_data as $pupil_id => $upd_lastName){

                $pupil = Pupil::find($pupil_id);

                if($pupil){

                    $pupil->update(['lastName' => ucwords(trim($upd_lastName))]);

                }

            }

        });
    }
}
