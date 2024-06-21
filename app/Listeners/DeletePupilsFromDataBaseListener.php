<?php

namespace App\Listeners;

use App\Events\ClassePupilsListUpdatedEvent;
use App\Events\PupilDeletionFailedEvent;
use App\Events\PupilDetachingOrDeletionCompletedEvent;
use App\Jobs\JobDeletePupilFromSchoolYearOrFromDatabase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DeletePupilsFromDataBaseListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle($event)
    {
        $jobs = [];

        if($event && $event->pupils && count($event->pupils)){

            foreach($event->pupils as $pupil){

                $jobs[] = new JobDeletePupilFromSchoolYearOrFromDatabase($event->user, $event->school_year_model, $event->classe, $pupil, true, true);

            }

        }

        $batch = Bus::batch(

            $jobs

            )->then(function(Batch $batch) use ($event){

                ClassePupilsListUpdatedEvent::dispatch($event->user, $event->classe);

                PupilDetachingOrDeletionCompletedEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                PupilDeletionFailedEvent::dispatch($event->user, $event->pupils);

            })

            ->finally(function(Batch $batch){


            })->name('pupil_deleting_school_year')->dispatch();
    }
}