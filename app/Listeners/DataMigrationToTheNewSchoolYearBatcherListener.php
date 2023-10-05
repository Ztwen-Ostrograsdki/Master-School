<?php

namespace App\Listeners;

use App\Events\DataMigratedToTheNewSchoolYearEvent;
use App\Events\MigrateDataToTheNewSchoolYearEvent;
use App\Jobs\JobDataMigrationToTheNewSchoolYear;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DataMigrationToTheNewSchoolYearBatcherListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MigrateDataToTheNewSchoolYearEvent $event)
    {
        
        $batch = Bus::batch([

            new JobDataMigrationToTheNewSchoolYear($event->school_year_model, $event->user)

            ])->then(function(Batch $batch) use ($event){

                DataMigratedToTheNewSchoolYearEvent::dispatch($event->user, true);

            })
            ->catch(function(Batch $batch, Throwable $er){

                DataMigratedToTheNewSchoolYearEvent::dispatch($event->user, false);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
