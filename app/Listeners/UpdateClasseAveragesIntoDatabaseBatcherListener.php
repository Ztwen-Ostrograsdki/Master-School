<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasCompletedEvent;
use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\ParentAccountBlockedEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Jobs\JobUpdateClasseAnnualAverageIntoDatabase;
use App\Jobs\JobUpdateClasseSemestrialAverageIntoDatabase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Bus;

class UpdateClasseAveragesIntoDatabaseBatcherListener
{
    use ModelQueryTrait;
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UpdateClasseAveragesIntoDatabaseEvent $event)
    {
        // InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);

        $jobs = [];

        $updates_jobs = [];

        $flush_jobs = [];

        if($event->allSemestres){

            $semestres = $this->getSemestres();

            foreach($semestres as $sem){

                $updates_jobs[] = new JobUpdateClasseSemestrialAverageIntoDatabase($event->classe, $sem, $event->school_year_model);

                $flush_jobs[] = new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->sem);

            }

            $updates_jobs[] = new JobUpdateClasseAnnualAverageIntoDatabase($event->classe, $event->school_year_model);

            $flush_jobs[] = new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null);

            

        }
        else{

            $jobs = [

                [
                    new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),
                                
                    new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null)
                ],

                [
                    new JobUpdateClasseSemestrialAverageIntoDatabase($event->classe, $event->semestre, $event->school_year_model),
                
                    new JobUpdateClasseAnnualAverageIntoDatabase($event->classe, $event->school_year_model)
                ],
            ];

        }

        if($event->allSemestres){

            $jobs = [ $flush_jobs, $updates_jobs ];

        }

        $batch = Bus::batch($jobs)->then(function(Batch $batch) use ($event){

                ClasseMarksWasUpdatedIntoDBSuccessfullyEvent::dispatch($event->user);

            })->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, null);

            })->finally(function(Batch $batch){

        })->name('updating_marks_into_database')->dispatch();
    }
}
