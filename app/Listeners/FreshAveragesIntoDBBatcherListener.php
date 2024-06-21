<?php

namespace App\Listeners;

use App\Events\FreshAveragesIntoDBEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobFlushAveragesIntoDataBase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class FreshAveragesIntoDBBatcherListener
{
    use ModelQueryTrait;
   
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(FreshAveragesIntoDBEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);
        
        $jobs = [];

        $school_year_model = $event->school_year_model;

        $classe  = $event->classe;

        if($event->allSemestres){

            $semestres = $this->getSemestres();

            foreach($semestres as $sem){

                if(! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id, $sem)){

                    $jobs[] = new JobFlushAveragesIntoDataBase($event->user, $classe, $school_year_model, $sem);

                }

            }
        }
        else{

            $jbs = [];

            if(! is_marks_stopped($classe->level_id, $school_year_model->id, $event->semestre)){

                $jbs[] = new JobFlushAveragesIntoDataBase($event->user, $classe, $school_year_model, $event->semestre);

            }

            $jbs[] = new JobFlushAveragesIntoDataBase($event->user, $classe, $school_year_model, null);

            $jobs = $jbs;

        }

        $batch = Bus::batch($jobs)->then(function(Batch $batch) use ($event){

                UpdateClasseAveragesIntoDatabaseEvent::dispatch($event->user, $event->classe, $event->semestre, $event->school_year_model, $event->allSemestres);

            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();



        
    }
}
