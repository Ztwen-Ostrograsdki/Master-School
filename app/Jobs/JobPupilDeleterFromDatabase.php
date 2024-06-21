<?php

namespace App\Jobs;

use App\Models\Mark;
use App\Models\Pupil;
use App\Models\RelatedMark;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobPupilDeleterFromDatabase implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $pupil;

    public $before_pruned = false;


    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Pupil $pupil, $before_pruned = false)
    {
        $this->pupil = $pupil;

        $this->before_pruned = $before_pruned;
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

            Mark::withoutEvents(function() use ($pupil){

                $pupil->marks()->each(function($mark){

                    $mark->forceDelete();
                });

            });

            RelatedMark::withoutEvents(function() use ($pupil){

                $pupil->related_marks()->each(function($r_m){

                    $r_m->forceDelete();
                });

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

            if(!$this->before_pruned){

                $pupil->forceDelete();

            }

        });
    }
}
