<?php

namespace App\Listeners;

use App\Events\ReloadClassesPromotionAndPositionEvent;
use App\Jobs\JobReloadClassesPromotionAndPosition;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ReloadClassesPromotionAndPositionBatcherListener
{
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ReloadClassesPromotionAndPositionEvent $event)
    {
        $batch = Bus::batch([

            new JobReloadClassesPromotionAndPosition($event->school_year_model, $event->user),


            ])->then(function(Batch $batch) use ($event){

                
            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
