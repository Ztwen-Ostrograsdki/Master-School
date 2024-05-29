<?php

namespace App\Listeners;

use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Jobs\JobClasseMarksDeleter;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Models\UpdatePupilsMarksBatches;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class ClasseMarksDeletionBatcherListener
{

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ClasseMarksDeletionCreatedEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);

        $subject_id = null;

        $all_subjects = false;

        $semestre = null;

        $all_semestres = false;

        if($event->subject !== "all" && $event->subject !== null && isset($event->subject->id)){

            $subject_id = $event->subject->id;

        }
        else{

            $all_subjects = true;
        }

        if($event->semestre !== "all" && $event->semestre !== null){

            $semestre = $event->semestre;

        }
        else{

            $all_semestres = true;
        }

        $user_batch = UpdatePupilsMarksBatches::create([

            'user_id' => $event->user->id,
            'classe_id' => $event->classe->id,
            'subject_id' => $subject_id,
            'all_subjects' => $all_subjects,
            'all_semestres' => $all_semestres,
            'school_year_id' => $event->school_year_model->id,
            'semestre' => $semestre,
            'method_type' => "deletion",
            'finished' => false,

        ]);
        
        $batch = Bus::batch([

            [
                new JobClasseMarksDeleter($event->classe, $event->school_year_model, $event->data),
            ],

            [
                new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),
            
                new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),
            ],

            ])->then(function(Batch $batch) use ($event){

                ClasseMarksWasUpdatedIntoDBSuccessfullyEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);

            })

            ->finally(function(Batch $batch){


            })->name('marks_deletion')->dispatch();

        $user_batch->update(['batch_id'=> $batch->id]);
    }
}
