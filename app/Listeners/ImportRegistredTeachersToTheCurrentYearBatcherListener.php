<?php

namespace App\Listeners;

use App\Events\ImportRegistredTeachersToTheCurrentYearEvent;
use App\Events\TeachersDataUploadingEvent;
use App\Events\TeachersToTheCurrentYearCompletedEvent;
use App\Jobs\JobImportRegistredTeachersToTheCurrentYear;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ImportRegistredTeachersToTheCurrentYearBatcherListener
{
   

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ImportRegistredTeachersToTheCurrentYearEvent $event)
    {
        TeachersDataUploadingEvent::dispatch($event->user, $event->school_year_model);

        $batch = Bus::batch([

            new JobImportRegistredTeachersToTheCurrentYear($event->user, $event->school_year_model)

            ])->then(function(Batch $batch) use ($event){

                TeachersToTheCurrentYearCompletedEvent::dispatch($event->user, $event->school_year_model, true);

            })
            ->catch(function(Batch $batch, Throwable $er){

                TeachersToTheCurrentYearCompletedEvent::dispatch($event->user, $event->school_year_model, false);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
