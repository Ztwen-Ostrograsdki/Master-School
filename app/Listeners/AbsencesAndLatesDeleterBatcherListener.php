<?php

namespace App\Listeners;

use App\Events\AbsencesAndLatesDeleterEvent;
use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClassePresenceLateWasCompletedEvent;
use App\Jobs\JobDeleteAbsencesAndLates;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class AbsencesAndLatesDeleterBatcherListener
{
   

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(AbsencesAndLatesDeleterEvent $event)
    {
         $batch = Bus::batch([

            new JobDeleteAbsencesAndLates($event->user, $event->classe, $event->semestre, $event->school_year_model, $event->subject_id, $event->pupil_id, $event->target)

            ])->then(function(Batch $batch) use ($event){

                ClassePresenceLateWasCompletedEvent::dispatch($event->user, $event->classe);

            })
            ->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe);
                
            })

            ->finally(function(Batch $batch){


        })->dispatch();
    }
}
