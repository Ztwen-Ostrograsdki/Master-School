<?php

namespace App\Listeners;

use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Events\ClasseMarksWasCompletedEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\NewAddParentRequestEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Jobs\JobInsertClassePupilMarksTogether;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ClasseMarksInsertionBatchListener
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
    public function handle(ClasseMarksInsertionCreatedEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);

        $batch = Bus::batch([

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),

            new JobInsertClassePupilMarksTogether($event->data)

            ])->then(function(Batch $batch) use ($event){

                UpdateClasseAveragesIntoDatabaseEvent::dispatch($event->user, $event->classe, $event->semestre, $event->school_year_model);

            })->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);
                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
