<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasFailedEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\InitiateClasseParticipationMarksEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Jobs\JobUpdateClasseParticipationMarks;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UpdateClasseParticipationMarksListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InitiateClasseParticipationMarksEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);

        $batch = Bus::batch([

            new JobUpdateClasseParticipationMarks($event->classe, $event->school_year_model, $event->semestre, $event->subject, $event->user)

            ])->then(function(Batch $batch) use ($event){

                UpdateClasseAveragesIntoDatabaseEvent::dispatch($event->user, $event->classe, $event->semestre, $event->school_year_model);

            })->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);
                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
