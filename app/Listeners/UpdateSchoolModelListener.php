<?php

namespace App\Listeners;

use App\Events\SchoolDataWasUpdatedSuccessfullyEvent;
use App\Events\UpdateSchoolModelEvent;
use App\Jobs\JobUpdateSchoolModel;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UpdateSchoolModelListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateSchoolModelEvent $event)
    {

        $batch = Bus::batch([

            new JobUpdateSchoolModel($event->user),

            ])->then(function(Batch $batch) use ($event){

                SchoolDataWasUpdatedSuccessfullyEvent::dispatch($event->user);

            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
