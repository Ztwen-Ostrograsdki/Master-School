<?php

namespace App\Listeners;

use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobClasseMarksDeleter;
use App\Jobs\JobFlushAveragesIntoDataBase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ClasseMarksDeletionBatcherListener
{
    /**
     * Create the event listener.
     *
     * @return void
     */
    public function __construct()
    {
        
    }

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ClasseMarksDeletionCreatedEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);
        
        $batch = Bus::batch([

            new JobClasseMarksDeleter($event->classe, $event->school_year_model, $event->data),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),

            ])->then(function(Batch $batch) use ($event){

                ClasseMarksWasUpdatedIntoDBSuccessfullyEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
