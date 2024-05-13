<?php

namespace App\Listeners;

use App\Events\ParentRequestToFollowPupilCreatedSuccessfullyEvent;
use App\Events\ParentRequestToFollowPupilEvent;
use App\Jobs\JobCreateParentRequestToFollowPupil;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ParentRequestToFollowPupilListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ParentRequestToFollowPupilEvent $event)
    {
        
        $batch = Bus::batch([

            new JobCreateParentRequestToFollowPupil($event->parentable, $event->pupil, $event->relation, $event->authorized)

            ])->then(function(Batch $batch) use ($event){

                ParentRequestToFollowPupilCreatedSuccessfullyEvent::dispatch($event->parentable->user);


            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
