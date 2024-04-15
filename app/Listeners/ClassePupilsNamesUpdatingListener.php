<?php

namespace App\Listeners;

use App\Events\ClassePupilsNamesUpdatedEvent;
use App\Events\InitiateClassePupilsNamesUpdateEvent;
use App\Jobs\JobClassePupilsNamesUpdating;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ClassePupilsNamesUpdatingListener
{
    
    public function handle(InitiateClassePupilsNamesUpdateEvent $event)
    {
        $batch = Bus::batch([

            new JobClassePupilsNamesUpdating($event->names_data, $event->user),

            ])->then(function(Batch $batch) use ($event){

                ClassePupilsNamesUpdatedEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
