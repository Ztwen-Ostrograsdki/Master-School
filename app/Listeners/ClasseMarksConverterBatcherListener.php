<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\ThrowClasseMarksConvertionEvent;
use App\Jobs\JobClasseMarksConvertion;
use App\Jobs\JobFlushAveragesIntoDataBase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ClasseMarksConverterBatcherListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ThrowClasseMarksConvertionEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);
        
        $batch = Bus::batch([

            new JobClasseMarksConvertion($event->classe, $event->convertion_type, $event->semestre, $event->school_year_model, $event->subject, $event->pupil_id, $event->user),

            // new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),

            // new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),

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
