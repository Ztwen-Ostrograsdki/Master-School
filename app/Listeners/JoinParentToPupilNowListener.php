<?php

namespace App\Listeners;

use App\Events\AboutMyParentRequestsEvent;
use App\Events\JoinParentToPupilNowEvent;
use App\Events\ParentHaveBeenJoinedToPupilEvent;
use App\Jobs\JobCreateParentPupilToJoinParentToPupil;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class JoinParentToPupilNowListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(JoinParentToPupilNowEvent $event)
    {

        $batch = Bus::batch([

            new JobCreateParentPupilToJoinParentToPupil($event->parentRequestToFollowPupil),


            ])->then(function(Batch $batch) use ($event){

                AboutMyParentRequestsEvent::dispatch($event->parentRequestToFollowPupil->parentable);

                ParentHaveBeenJoinedToPupilEvent::dispatch();

            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
