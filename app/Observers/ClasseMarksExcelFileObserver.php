<?php

namespace App\Observers;

use App\Events\ClasseExcelsFilesWasUpdatedEvent;
use App\Models\ClasseMarksExcelFile;

class ClasseMarksExcelFileObserver
{
    /**
     * Handle the ClasseMarksExcelFile "created" event.
     *
     * @param  \App\Models\ClasseMarksExcelFile  $classeMarksExcelFile
     * @return void
     */
    public function created(ClasseMarksExcelFile $classeMarksExcelFile)
    {

        ClasseExcelsFilesWasUpdatedEvent::dispatch($classeMarksExcelFile->user);
    }

    /**
     * Handle the ClasseMarksExcelFile "updated" event.
     *
     * @param  \App\Models\ClasseMarksExcelFile  $classeMarksExcelFile
     * @return void
     */
    public function updated(ClasseMarksExcelFile $classeMarksExcelFile)
    {
        $user = auth()->user();

        ClasseExcelsFilesWasUpdatedEvent::dispatch($user);
    }

    /**
     * Handle the ClasseMarksExcelFile "deleted" event.
     *
     * @param  \App\Models\ClasseMarksExcelFile  $classeMarksExcelFile
     * @return void
     */
    public function deleted(ClasseMarksExcelFile $classeMarksExcelFile)
    {
        $user = auth()->user();

        ClasseExcelsFilesWasUpdatedEvent::dispatch($user);
    }

   
}
