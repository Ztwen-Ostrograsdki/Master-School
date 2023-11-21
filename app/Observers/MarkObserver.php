<?php

namespace App\Observers;

use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
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
        // $this->doJob($mark);
    }

    /**
     * Handle the Mark "updated" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function updated(Mark $mark)
    {
        $mark->value > 0 ? $this->doJob($mark) : $this->doNotJob();
    }

    /**
     * Handle the Mark "deleted" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function deleted(Mark $mark)
    {
        $mark->value > 0 ? $this->doJob($mark) : $this->doNotJob();
    }

    /**
     * Handle the Mark "restored" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function restored(Mark $mark)
    {
        $mark->value > 0 ? $this->doJob($mark) : $this->doNotJob();
    }

    /**
     * Handle the Mark "force deleted" event.
     *
     * @param  \App\Models\Mark  $mark
     * @return void
     */
    public function forceDeleted(Mark $mark)
    {
        $mark->value > 0 ? $this->doJob($mark) : $this->doNotJob();
    }


    public function doJob($mark)
    {

        $school_year_model = $mark->school_year;

        $semestre = $mark->semestre;

        $classe = $mark->classe;

        if($classe && $semestre){

            $user = $mark->user;

            FlushAveragesIntoDataBaseEvent::dispatch($user, $classe, $school_year_model, $semestre);

        }

    }

    public function doNotJob()
    {
        //DO ANYTHINK
    }
}
