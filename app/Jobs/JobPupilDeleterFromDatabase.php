<?php

namespace App\Jobs;

use App\Models\Pupil;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobPupilDeleterFromDatabase implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    protected $pupil;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Pupil $pupil)
    {
         $this->pupil = $pupil;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        DB::transaction(function($e){

            $pupil = $this->pupil;

            $pupil->lates()->each(function($late){

                $late->delete();

            });

            $pupil->absences()->each(function($abs){

                $abs->delete();
            });

            $pupil->marks()->each(function($mark){
                $mark->delete();
            });

            $pupil->related_marks()->each(function($r_m){
                $r_m->delete();
            });

            $pupil->classes()->each(function($classe) use($pupil){

                $classe->classePupils()->detach($pupil->id);
            });

            $pupil->pupilClassesHistoriesBySchoolYears()->each(function($history){

                $history->delete();

            });

            $pupil->school_years()->each(function($sy) use($pupil){

                $sy->pupils()->detach($pupil->id);

            });

            $pupil->forceDelete();

        });
    }
}
