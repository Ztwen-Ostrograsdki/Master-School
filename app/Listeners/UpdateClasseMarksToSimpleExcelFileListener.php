<?php

namespace App\Listeners;

use App\Events\ClasseMarksToSimpleExcelFileCompletedEvent;
use App\Events\PrepareClasseMarksExcelFileDataInsertionToDatabaseEvent;
use App\Events\UpdateClasseMarksToSimpleExcelFileEvent;
use App\Jobs\JobMigrateClasseMarksExcelFileDataToDatabase;
use App\Jobs\JobUpdateClasseMarksToSimpleExcelFile;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UpdateClasseMarksToSimpleExcelFileListener
{
   

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateClasseMarksToSimpleExcelFileEvent $event)
    {
        $batch = Bus::batch([

            new JobUpdateClasseMarksToSimpleExcelFile($event->classe, $event->file_name, $event->file_path, $event->file_sheet, $event->school_year_model, $event->semestre, $event->subject, $event->user, $event->pupil_id),

            new JobMigrateClasseMarksExcelFileDataToDatabase($event->classe, $event->file_name, $event->file_path, $event->school_year_model, $event->semestre, $event->subject, $event->user),

        ])->then(function(Batch $batch) use ($event){

            ClasseMarksToSimpleExcelFileCompletedEvent::dispatch($event->user, $file_name);

        })->catch(function(Batch $batch, Throwable $er){


        })->finally(function(Batch $batch){


        })->dispatch();
    }
}
