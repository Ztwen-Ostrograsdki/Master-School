<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\InitiateSettingsOnMarksEvent;
use App\Jobs\JobProccessingSettingsOnMarks;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class InitiateSettingsOnMarksBatcherListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InitiateSettingsOnMarksEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);
        
        $batch = Bus::batch([

            new JobProccessingSettingsOnMarks($event->user, $event->classe, $event->school_year_model, $event->data)

            ])->then(function(Batch $batch) use ($event){

                ClasseMarksWasUpdatedIntoDBSuccessfullyEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
