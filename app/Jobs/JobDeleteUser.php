<?php

namespace App\Jobs;

use App\Models\Epreuve;
use App\Models\TeacherCursus;
use App\Models\TimePlan;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobDeleteUser implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $forceDelete = true;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, $forceDelete)
    {
        $this->user = $user;

        $this->forceDelete = $forceDelete;
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

            $user = $this->user;

            if($user){

                $teacher = $user->teacher;

                $admin = $user->admin;

                if($this->forceDelete){

                    $user->forceDelete();

                    if($teacher){

                        TeacherCursus::where('teacher_id', $teacher->id)->each(function($cursus){

                            $cursus->delete();

                        });

                        TimePlan::where('teacher_id', $teacher->id)->each(function($time_plan){

                            $time_plan->delete();

                        });

                        Epreuve::where('user_id', $user->id)->each(function($epreuve){

                            $epreuve->delete();

                        });

                        $teacher->forceDelete();

                    }

                    if($admin){

                        $admin->delete();

                    }

                }
                else{

                    $user->delete();

                }



            }

        });
    }
}
