<?php

namespace App\Listeners;

use App\Events\ClassePupilsListUpdatedEvent;
use App\Events\ClassePupilsListUpdatingEvent;
use App\Events\DetachPupilsFromSchoolYearEvent;
use App\Events\PupilDetachingFailedEvent;
use App\Events\PupilDetachingOrDeletionCompletedEvent;
use App\Jobs\JobDeletePupilFromSchoolYearOrFromDatabase;
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

        $jobs = [];

        if($event && $event->pupils && count($event->pupils)){

            foreach($event->pupils as $pupil){

                $jobs[] = new JobDetachPupilsFromSchoolYear($event->user, $event->school_year_model, $event->classe, $pupil, false, false);

            }

        }

        $batch = Bus::batch(

            $jobs

            )->then(function(Batch $batch) use ($event){

                ClassePupilsListUpdatedEvent::dispatch($event->user, $event->classe);

                PupilDetachingOrDeletionCompletedEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                PupilDetachingFailedEvent::dispatch($event->user, $event->pupils);

            })

            ->finally(function(Batch $batch){


            })->name('pupil_detaching_school_year')->dispatch();
    }
}
