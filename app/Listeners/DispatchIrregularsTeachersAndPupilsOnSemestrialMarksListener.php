<?php

namespace App\Listeners;

use App\Events\DispatchIrregularsSemestrialMarksToConcernedTeachersEvent;
use App\Events\DispatchIrregularsTeachersAndPupilsOnSemestrialMarksEvent;
use App\Jobs\JobForDispatchingIrregularsTeachersAndPupilsOnSemestrial;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DispatchIrregularsTeachersAndPupilsOnSemestrialMarksListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(DispatchIrregularsTeachersAndPupilsOnSemestrialMarksEvent $event)
    {
        $jobs = [];

        $teachers = [];

        if($event && $event->data){

            $irregulars_teachers = $event->data['irregulars_teachers'];

            foreach($irregulars_teachers as $teacher){

                $teachers[] = $teacher;

                $pupils = [];

                $irregulars_pupils = $event->data['irregulars_pupils'];

                foreach($irregulars_pupils as $pupil_id => $pupil){

                    if(isset($pupil[$teacher->speciality()->id])){

                        $pupils[$pupil_id] = $pupil['pupil'];

                    }

                }

                $jobs[$teacher->id] = new JobForDispatchingIrregularsTeachersAndPupilsOnSemestrial($event->classe, $pupils, $event->semestre, $event->school_year_model, $teacher);

            }

        }

        if($teachers && count($teachers)){

            foreach($teachers as $teacher){

                $job = $jobs[$teacher->id];

                $batch = Bus::batch(

                    [$job]

                    )->then(function(Batch $batch) use ($event, $teacher){

                        DispatchIrregularsSemestrialMarksToConcernedTeachersEvent::dispatch($event->classe, $semestre, [$teacher]);

                    })
                    ->catch(function(Batch $batch, Throwable $er){


                    })

                    ->finally(function(Batch $batch){

                })->name('dispatch_irregular_classe_semestrial_marks')->dispatch();

            }

        }
    }
}
