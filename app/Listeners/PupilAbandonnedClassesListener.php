<?php

namespace App\Listeners;

use App\Events\ClasseDataWasUpdateSuccessfullyEvent;
use App\Events\PupilAbandonnedClassesEvent;
use App\Events\PupilSetToOrFromAbandonnedEvent;
use App\Jobs\JobSetPupilAsAbandonnedClasses;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class PupilAbandonnedClassesListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PupilAbandonnedClassesEvent $event)
    {
        $batch = Bus::batch([

            new JobSetPupilAsAbandonnedClasses($event->pupil, $event->user, $event->school_year_model)

            ])->then(function(Batch $batch) use ($event){

                PupilSetToOrFromAbandonnedEvent::dispatch($event->pupil, $event->user, $event->school_year_model);

            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
