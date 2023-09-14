<?php

namespace App\Listeners;

use App\Events\FlushAveragesIntoDataBaseEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobFlushAveragesIntoDataBase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class FlushAveragesIntoDataBaseBatcherListener
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
    public function handle(FlushAveragesIntoDataBaseEvent $event)
    {
        $batch = Bus::batch(
            [
                new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),

                new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),

            ])

            ->then(function(Batch $batch) use ($event){

                UpdateClasseAveragesIntoDatabaseEvent::dispatch($event->user, $event->classe, $event->semestre, $event->school_year_model);

            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){

                

            })->dispatch();
    }
}
