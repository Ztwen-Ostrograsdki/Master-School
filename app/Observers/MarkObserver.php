<?php

namespace App\Observers;

use App\Jobs\JobUpdateClasseAnnualAverageIntoDatabase;
use App\Jobs\JobUpdateClasseSemestrialAverageIntoDatabase;
use App\Jobs\UpdateAverageTable;
use App\Models\Mark;
use Illuminate\Support\Carbon;

class MarkObserver
{
    /**
     * Handle the Mark "created" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function created(Mark $mark)
    {
        $this->doJob($mark);
    }

    /**
     * Handle the Mark "updated" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function updated(Mark $mark)
    {
        $this->doJob($mark);
    }

    /**
     * Handle the Mark "deleted" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function deleted(Mark $mark)
    {
        $this->doJob($mark);
    }

    /**
     * Handle the Mark "restored" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function restored(Mark $mark)
    {
        $this->doJob($mark);
    }

    /**
     * Handle the Mark "force deleted" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function forceDeleted(Mark $mark)
    {
        $this->doJob($mark);
    }


    public function doJob($mark)
    {

        $school_year_model = $mark->school_year;

        $semestre = $mark->semestre;

        $classe = $mark->classe;

        if($classe && $semestre){

            dispatch(new JobUpdateClasseSemestrialAverageIntoDatabase($classe, $semestre, $school_year_model))->delay(Carbon::now()->addSeconds(15));

            dispatch(new JobUpdateClasseAnnualAverageIntoDatabase($classe, $school_year_model))->delay(Carbon::now()->addSeconds(30));

        }

    }
}
