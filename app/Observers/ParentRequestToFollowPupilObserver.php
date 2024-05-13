<?php

namespace App\Observers;

use App\Models\ParentRequestToFollowPupil;

class ParentRequestToFollowPupilObserver
{
    /**
     * Handle the ParentRequestToFollowPupil "created" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function created(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        
    }

    /**
     * Handle the ParentRequestToFollowPupil "updated" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function updated(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        //
    }

    /**
     * Handle the ParentRequestToFollowPupil "deleted" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function deleted(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        
    }

    /**
     * Handle the ParentRequestToFollowPupil "restored" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function restored(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        //
    }

    /**
     * Handle the ParentRequestToFollowPupil "force deleted" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function forceDeleted(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        //
    }
}
