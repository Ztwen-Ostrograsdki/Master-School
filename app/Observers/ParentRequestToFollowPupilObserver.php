<?php

namespace App\Observers;

use App\Events\AboutMyParentRequestsEvent;
use App\Events\JoinParentToPupilNowEvent;
use App\Events\MyParentRequestToFollowPupilCreatedEvent;
use App\Events\ParentRequestToFollowPupilWasDeletedEvent;
use App\Models\ParentRequestToFollowPupil;
use Illuminate\Support\Facades\DB;

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
        if($parentRequestToFollowPupil->authorized){

            $joined = $parentRequestToFollowPupil->parentable->pupils()->where('parent_pupils.pupil_id', $parentRequestToFollowPupil->pupil_id)->first();

            if(!$joined){

                JoinParentToPupilNowEvent::dispatch($parentRequestToFollowPupil);

            }
        }

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
        $joineds = $parentRequestToFollowPupil->parentable->pupils()->where('parent_pupils.pupil_id', $parentRequestToFollowPupil->pupil_id)->get();

        if(count($joineds)){

            DB::transaction(function($e) use ($joineds){

                foreach($joineds as $join){

                    $join->delete();

                }

            });

            AboutMyParentRequestsEvent::dispatch($parentRequestToFollowPupil->parentable);

            ParentRequestToFollowPupilWasDeletedEvent::dispatch();

        }
        else{

            AboutMyParentRequestsEvent::dispatch($parentRequestToFollowPupil->parentable);

            ParentRequestToFollowPupilWasDeletedEvent::dispatch();

        }

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
