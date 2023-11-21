<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasFailedEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Events\UpdateClasseSanctionsEvent;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Jobs\JobUpdateClasseSanctions;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UpdateClasseSanctionsListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    
    public function handle(UpdateClasseSanctionsEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);

        $batch = Bus::batch([

            new JobUpdateClasseSanctions($event->classe, $event->user, $event->school_year_model, $event->semestre, $event->subject, $event->activated),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),


            ])->then(function(Batch $batch) use ($event){

                UpdateClasseAveragesIntoDatabaseEvent::dispatch($event->user, $event->classe, $event->semestre, $event->school_year_model);

            })->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);
                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
