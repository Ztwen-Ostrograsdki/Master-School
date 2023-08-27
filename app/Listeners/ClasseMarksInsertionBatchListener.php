<?php

namespace App\Listeners;

use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Events\ClasseMarksWasCompletedEvent;
use App\Events\NewAddParentRequestEvent;
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

        $batch = Bus::batch([new JobInsertClassePupilMarksTogether($event->data)])

                    ->then(function(Batch $batch) use ($event){

                        // ClasseMarksWasCompletedEvent::dispatch($event->user, $event->classe, $event->subject);
                    })
                    ->catch(function(Batch $batch, Throwable $er){

                        ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);

                    })

                    ->finally(function(Batch $batch){


                    })->dispatch();
    }
}
