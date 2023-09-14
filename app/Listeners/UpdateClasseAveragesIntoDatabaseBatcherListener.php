<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasCompletedEvent;
use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\ParentAccountBlockedEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
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

        if($event->allSemestres){

            $semestres = $this->getSemestres();

            foreach($semestres as $sem){

                $jobs[] = new JobUpdateClasseSemestrialAverageIntoDatabase($event->classe, $sem, $event->school_year_model);
            }

            $jobs[] = new JobUpdateClasseAnnualAverageIntoDatabase($event->classe, $event->school_year_model);

        }
        else{

            $jobs = [
                new JobUpdateClasseSemestrialAverageIntoDatabase($event->classe, $event->semestre, $event->school_year_model),

                new JobUpdateClasseAnnualAverageIntoDatabase($event->classe, $event->school_year_model)
            ];

        }

        $batch = Bus::batch($jobs)->then(function(Batch $batch) use ($event){

                ClasseMarksWasUpdatedIntoDBSuccessfullyEvent::dispatch($event->user);

            })->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, null);

            })->finally(function(Batch $batch){

        })->dispatch();
    }
}
