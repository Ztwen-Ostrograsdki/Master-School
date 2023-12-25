<?php

namespace App\Listeners;

use App\Events\ClasseDataWasUpdateSuccessfullyEvent;
use App\Events\ClasseMarksDeletionCompletedEvent;
use App\Events\ClasseRefereesManagerEvent;
use App\Jobs\JobClasseRefereesManager;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ClasseRefereesManagerBatcherListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ClasseRefereesManagerEvent $event)
    {
        $batch = Bus::batch([

            new JobClasseRefereesManager($event->classe, $event->data, $event->school_year_model, $event->user),

            ])->then(function(Batch $batch) use ($event){

                // classe referees was updated successfully
                ClasseDataWasUpdateSuccessfullyEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                // ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
