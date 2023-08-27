<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasCompletedEvent;
use App\Events\ClasseMarksWasFailedEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobUpdateClasseAnnualAverageIntoDatabase;
use App\Jobs\JobUpdateClasseSemestrialAverageIntoDatabase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;

class UpdateClasseAveragesIntoDatabaseBatcherListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateClasseAveragesIntoDatabaseEvent $event)
    {

        $batch = Bus::batch(
            [
                new JobUpdateClasseSemestrialAverageIntoDatabase($event->classe, $event->semestre, $event->school_year_model),
                new JobUpdateClasseAnnualAverageIntoDatabase($event->classe, $event->school_year_model),
            ])

            ->then(function(Batch $batch) use ($event){

                ClasseMarksWasCompletedEvent::dispatch($event->user, $event->classe, $event->subject);
            })
            ->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);

            })

            ->finally(function(Batch $batch){

                // NewAddParentRequestEvent::dispatch();

            })->dispatch();
    }
}
