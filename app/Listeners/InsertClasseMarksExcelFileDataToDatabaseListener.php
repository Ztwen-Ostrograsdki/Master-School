<?php

namespace App\Listeners;

use App\Events\ClasseMarksToSimpleExcelFileCompletedEvent;
use App\Events\InsertClasseMarksExcelFileDataToDatabaseEvent;
use App\Jobs\JobMigrateClasseMarksExcelFileDataToDatabase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class InsertClasseMarksExcelFileDataToDatabaseListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InsertClasseMarksExcelFileDataToDatabaseEvent $event)
    {
        $batch = Bus::batch([

            new JobMigrateClasseMarksExcelFileDataToDatabase($event->classe, $event->file_name, $file_path, $event->school_year_model, $event->semestre, $event->subject, $event->user),

        ])->then(function(Batch $batch) use ($event){

            ClasseMarksToSimpleExcelFileCompletedEvent::dispatch($event->user, $file_name);

        })->catch(function(Batch $batch, Throwable $er){


        })->finally(function(Batch $batch){


        })->dispatch();
    }
    }
}
