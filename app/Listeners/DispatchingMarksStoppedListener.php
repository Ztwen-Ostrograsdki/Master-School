<?php

namespace App\Listeners;

use App\Events\DispatchingMarksStoppingFailedEvent;
use App\Events\MarksStoppingDispatchedEvent;
use App\Events\MarksStoppingDispatchingEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobMarksStoppedManager;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DispatchingMarksStoppedListener
{
    use ModelQueryTrait;
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MarksStoppingDispatchingEvent $event)
    {

        $jobs = [];

        $classes = [];

        $excepts = $event->excepts;

        if($event){

            if($event->classe){

                $classes[] = $event->classe;

                $jobs[] = new JobMarksStoppedManager($event->classe, $event->classe->level, $event->school_year_model, $event->semestre, $event->subject);

            }
            else{

                if($event->level){

                    $classes = $event->school_year_model->classes()->where('classes.level_id', $event->level->id)->whereNotIn('classes.id', $excepts)->get();

                    foreach($classes as $classe){

                        $jobs[] = new JobMarksStoppedManager($classe, $level, $event->school_year_model, $event->semestre, $event->subject);

                    }

                }
                else{

                    $classes = $event->school_year_model->classes()->whereNotIn('classes.id', $excepts)->get();

                    foreach($classes as $classe){

                        $jobs[] = new JobMarksStoppedManager($classe, $classe->level, $event->school_year_model, $event->semestre, $event->subject);

                    }

                }

            }

            
        }

        $batch = Bus::batch(

                $jobs

            )->then(function(Batch $batch) use ($event){

                MarksStoppingDispatchedEvent::dispatch();

            })
            ->catch(function(Batch $batch, Throwable $er){

                DispatchingMarksStoppingFailedEvent::dispatch();

            })
            ->finally(function(Batch $batch){


        })->name('finilize_marks_stoppeds_manager')->dispatch();
    
    }
}
