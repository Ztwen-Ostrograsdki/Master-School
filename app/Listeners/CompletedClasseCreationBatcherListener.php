<?php

namespace App\Listeners;

use App\Events\CompletedClasseCreationEvent;
use App\Jobs\JobCompletedClasseCreation;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class CompletedClasseCreationBatcherListener
{
   
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
    */
    
    public function handle(CompletedClasseCreationEvent $event)
    {
        
        $batch = Bus::batch([

            new JobCompletedClasseCreation($event->classe, $event->school_year_model, $event->user),


            ])->then(function(Batch $batch) use ($event){

                

            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
