<?php

namespace App\Listeners;

use App\Events\ClassePupilsDataWasUpdatedFromFileEvent;
use App\Events\InitiateClassePupilsDataUpdatingFromFileEvent;
use App\Jobs\JobUpdateClassePupilsDataFromFile;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UpdateClassePupilsDataFromFileListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InitiateClassePupilsDataUpdatingFromFileEvent $event)
    {
        $batch = Bus::batch([

            new JobUpdateClassePupilsDataFromFile($event->classe, $event->data, $event->user, $event->pupil_id),

            ])->then(function(Batch $batch) use ($event){

                ClassePupilsDataWasUpdatedFromFileEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
