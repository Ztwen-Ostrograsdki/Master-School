<?php

namespace App\Listeners;

use App\Events\ClassePupilsListUpdatedEvent;
use App\Events\ClassePupilsListUpdatingEvent;
use App\Events\DetachPupilsFromSchoolYearEvent;
use App\Jobs\JobDetachPupilsFromSchoolYear;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DetachPupilsFromSchoolYearBatcherListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DetachPupilsFromSchoolYearEvent $event)
    {
        // ClassePupilsListUpdatingEvent::dispatch($event->user, $event->classe);

        $batch = Bus::batch([

            new JobDetachPupilsFromSchoolYear($event->user, $event->school_year_model, $event->classe, $event->pupils)

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
