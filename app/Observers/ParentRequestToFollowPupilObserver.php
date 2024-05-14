<?php

namespace App\Observers;

use App\Events\AboutMyParentRequestsEvent;
use App\Events\MyParentRequestToFollowPupilCreatedEvent;
use App\Events\ParentRequestToFollowPupilWasDeletedEvent;
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
        MyParentRequestToFollowPupilCreatedEvent::dispatch($parentRequestToFollowPupil->parentable->user);
    }

    /**
     * Handle the ParentRequestToFollowPupil "updated" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function updated(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        AboutMyParentRequestsEvent::dispatch($parentRequestToFollowPupil->parentable);

        ParentRequestToFollowPupilWasDeletedEvent::dispatch();
    }

    /**
     * Handle the ParentRequestToFollowPupil "deleted" event.
     *
     * @param  \App\Models\ParentRequestToFollowPupil  $parentRequestToFollowPupil
     * @return void
     */
    public function deleting(ParentRequestToFollowPupil $parentRequestToFollowPupil)
    {
        AboutMyParentRequestsEvent::dispatch($parentRequestToFollowPupil->parentable);

        ParentRequestToFollowPupilWasDeletedEvent::dispatch();
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
