<?php

namespace App\Observers;

use App\Jobs\JobDeleteClasseSecurityAfterExpired;
use App\Models\ClassesSecurity;

class ClassesSecurityObserver
{
    /**
     * Handle the ClassesSecurity "created" event.
     *
     * @param  \App\Models\ClassesSecurity  $security
     * @return void
     */
    public function created(ClassesSecurity $security)
    {
        dispatch(new JobDeleteClasseSecurityAfterExpired($security))->delay(Carbon::now()->addHours($security->duration)); 
    }

    /**
     * Handle the ClassesSecurity "updated" event.
     *
     * @param  \App\Models\ClassesSecurity  $security
     * @return void
     */
    public function updated(ClassesSecurity $security)
    {
        dispatch(new JobDeleteClasseSecurityAfterExpired($security))->delay(Carbon::now()->addHours($security->duration));
    }

    /**
     * Handle the ClassesSecurity "deleted" event.
     *
     * @param  \App\Models\ClassesSecurity  $security
     * @return void
     */
    public function deleted(ClassesSecurity $security)
    {
        //
    }

    /**
     * Handle the ClassesSecurity "restored" event.
     *
     * @param  \App\Models\ClassesSecurity  $security
     * @return void
     */
    public function restored(ClassesSecurity $security)
    {
        //
    }

    /**
     * Handle the ClassesSecurity "force deleted" event.
     *
     * @param  \App\Models\ClassesSecurity  $security
     * @return void
     */
    public function forceDeleted(ClassesSecurity $security)
    {
        //
    }
}
