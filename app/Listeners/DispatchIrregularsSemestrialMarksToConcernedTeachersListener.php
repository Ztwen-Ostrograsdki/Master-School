<?php

namespace App\Listeners;

use App\Events\DispatchIrregularsSemestrialMarksToConcernedTeachersEvent;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DispatchIrregularsSemestrialMarksToConcernedTeachersListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DispatchIrregularsSemestrialMarksToConcernedTeachersEvent $event)
    {
        $jobs = [];


        if($event && $event->teachers){

            $jobs[] = new JobForDispatchingIrregularsTeachersAndPupilsOnSemestrial($event->classe, $pupils, $event->school_year_model, $teacher);
            

        }

        $batch = Bus::batch(

            $jobs

            )->then(function(Batch $batch) use ($event, $teachers){

                // DispatchIrregularsSemestrialMarksToConcernedTeachersEvent::dispatch($teachers)

            })
            ->catch(function(Batch $batch, Throwable $er){


            })

            ->finally(function(Batch $batch){


        })->name('dispatch_irregular_classe_semestrial_marks_to_concerned_teachers')->dispatch();
    
    }
}
