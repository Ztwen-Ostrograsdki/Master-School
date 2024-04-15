<?php

namespace App\Listeners;

use App\Events\ClassePupilsMatriculeUpdatedEvent;
use App\Events\InitiateClassePupilsMatriculeUpdateEvent;
use App\Jobs\JobUpdateClassePupilsMatricule;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UpdatingClassePupilsMatriculeListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InitiateClassePupilsMatriculeUpdateEvent $event)
    {
        $batch = Bus::batch([

            new JobUpdateClassePupilsMatricule($event->matricule_data, $event->user),

            ])->then(function(Batch $batch) use ($event){

                ClassePupilsMatriculeUpdatedEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
