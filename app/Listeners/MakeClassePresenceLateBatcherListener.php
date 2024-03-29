<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClassePresenceLateWasCompletedEvent;
use App\Events\MakeClassePresenceLateEvent;
use App\Jobs\JobMakeClassePresenceLate;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class MakeClassePresenceLateBatcherListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MakeClassePresenceLateEvent $event)
    {
        $batch = Bus::batch([

            new JobMakeClassePresenceLate($event->user, $event->classe, $event->data)

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
