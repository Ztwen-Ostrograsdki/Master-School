<?php

namespace App\Listeners;

use App\Events\ClassePupilsListUpdatedEvent;
use App\Events\ClassePupilsListUpdatingEvent;
use App\Events\StartNewsPupilsInsertionEvent;
use App\Jobs\JobProcessingNewsPupilsInsertion;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ProcessingNewsPupilsInsertionBatcherListener
{
   
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(StartNewsPupilsInsertionEvent $event)
    {
        ClassePupilsListUpdatingEvent::dispatch($event->user, $event->classe);

        $batch = Bus::batch([

            new JobProcessingNewsPupilsInsertion($event->user, $event->school_year_model, $event->classe, $event->pupils)

            ])->then(function(Batch $batch) use ($event){

                ClassePupilsListUpdatedEvent::dispatch($event->user, $event->classe);

            })
            ->catch(function(Batch $batch, Throwable $er){

                // ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
