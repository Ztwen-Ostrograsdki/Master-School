<?php

namespace App\Observers;

use App\Helpers\AdminTraits\AdminTrait;
use App\Models\pupil;

class PupilObserver
{

    use AdminTrait;
    /**
     * Handle the pupil "created" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function created(pupil $pupil)
    {
        
    }

    /**
     * Handle the pupil "updated" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function updated(pupil $pupil)
    {
        // dd($pupil);
    }

    /**
     * Handle the pupil "deleted" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function deleted(pupil $pupil)
    {
        //
    }

    /**
     * Handle the pupil "restored" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function restored(pupil $pupil)
    {
        //
    }

    /**
     * Handle the pupil "force deleted" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function forceDeleted(pupil $pupil)
    {
        //
    }
}
