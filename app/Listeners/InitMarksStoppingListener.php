<?php

namespace App\Listeners;

use App\Events\InitiateMarksStoppingEvent;
use App\Events\MarksStoppingDispatchedEvent;
use App\Events\MarksStoppingDispatchingEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobUpdateClasseAnnualAverageIntoDatabase;
use App\Jobs\JobUpdateClasseSemestrialAverageIntoDatabase;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class InitMarksStoppingListener
{

    use ModelQueryTrait;
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(InitiateMarksStoppingEvent $event)
    {

        $jobs = [];

        $classes = [];

        $excepts = $event->excepts;

        $init_jobs = [];

        if($event){

            if($event->classe){

                $classes[] = $event->classe;

            }
            else{

                if($event->level){

                    $classes = $event->school_year_model->classes()->where('classes.level_id', $event->level->id)->whereNotIn('classes.id', $excepts)->get();

                }
                else{

                    $classes = $event->school_year_model->classes()->whereNotIn('classes.id', $excepts)->get();

                }

            }

            foreach($classes as $classe){

                if($event->semestre){

                    $init_jobs[] = new JobUpdateClasseSemestrialAverageIntoDatabase($classe, $event->semestre, $event->school_year_model);
                
                }
                else{

                    $semestres = $this->getSemestres();

                    foreach($semestres as $semm){

                        $init_jobs[] = new JobUpdateClasseSemestrialAverageIntoDatabase($classe, $semm, $event->school_year_model);

                    }

                }

                $init_jobs[] = new JobUpdateClasseAnnualAverageIntoDatabase($classe, $event->school_year_model);

            }

        }

        $batch = Bus::batch(

                [$init_jobs]

            )->then(function(Batch $batch) use ($event){

                MarksStoppingDispatchingEvent::dispatch($event->classe, $event->level, $event->school_year_model, $event->semestre, $event->subject, $event->excepts);

            })
            ->catch(function(Batch $batch, Throwable $er){


            })
            ->finally(function(Batch $batch){


        })->name('init_marks_stoppeds_manager')->dispatch();
    
    }
}
