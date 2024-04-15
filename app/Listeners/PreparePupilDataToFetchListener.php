<?php

namespace App\Listeners;

use App\Events\PreparePupilDataToFetchEvent;
use App\Events\PupilDataAreReadyToFetchEvent;
use App\Jobs\JobGetPupilDataToFetch;
use App\Models\Pupil;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class PreparePupilDataToFetchListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PreparePupilDataToFetchEvent $event)
    {

        $batch = Bus::batch([

            new JobGetPupilDataToFetch($event->user, $event->level)

            ])->then(function(Batch $batch) use ($event){

                $this->data = Pupil::where('level_id', $event->level->id)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                PupilDataAreReadyToFetchEvent::dispatch($event->user, $this->data);

            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
