<?php

namespace App\Observers;

use App\Events\UpdateSchoolModelEvent;
use App\Helpers\AdminTraits\AdminTrait;
use App\Jobs\JobFinalisePupilDeletionFromDataBase;
use App\Jobs\JobUpdateSchoolModel;
use App\Models\Pupil;
use App\Models\School;
use Illuminate\Support\Carbon;

class PupilObserver
{

    use AdminTrait;
    /**
     * Handle the pupil "created" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function created(Pupil $pupil)
    {
        $user = auth()->user(); 

        UpdateSchoolModelEvent::dispatch($user);
    }

    /**
     * Handle the pupil "updated" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function updated(Pupil $pupil)
    {
        // dd($pupil);
    }


    public function deleting(Pupil $pupil)
    {
        JobFinalisePupilDeletionFromDataBase::dispatch($pupil)->delay(Carbon::now()->addDays(30));
    }

    /**
     * Handle the pupil "deleted" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function deleted(Pupil $pupil)
    {
        $user = auth()->user(); 
        
        UpdateSchoolModelEvent::dispatch($user);
    }

    /**
     * Handle the pupil "restored" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function restored(Pupil $pupil)
    {
        //
    }

    /**
     * Handle the pupil "force deleted" event.
     *
     * @param  \App\Models\pupil  $pupil
     * @return void
     */
    public function forceDeleted(Pupil $pupil)
    {
        //
    }
}
