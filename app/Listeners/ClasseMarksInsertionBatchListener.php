<?php

namespace App\Listeners;

use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Events\ClasseMarksWasCompletedEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\NewAddParentRequestEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Events\UpdatePupilsMarksInsertionProgressEvent;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Jobs\JobInsertClassePupilMarksTogether;
use App\Jobs\ZtwenJob;
use App\Models\UpdatePupilsMarksBatches;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;
use Illuminate\Support\Facades\DB;

class ClasseMarksInsertionBatchListener
{
    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(ClasseMarksInsertionCreatedEvent $event)
    {

        // UpdatePupilsMarksBatches::whereNotNull('total_marks')->delete();

        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);


            $total_marks = $event->data['total_marks'];

            $user_batch = UpdatePupilsMarksBatches::create([

                'user_id' => $event->user->id,
                'subject_id' => $event->subject->id,
                'classe_id' => $event->classe->id,
                'school_year_id' => $event->school_year_model->id,
                'semestre' => $event->semestre,
                'method_type' => "insertion",
                'finished' => false,
                'total_marks' => $total_marks

            ]);

            $batch = Bus::batch([

                
                    new JobInsertClassePupilMarksTogether($event->data, $event->related, $event->related_data),
                

                ])->then(function(Batch $batch) use ($event, $user_batch){

                    UpdatePupilsMarksInsertionProgressEvent::dispatch($event->user);

                    UpdateClasseAveragesIntoDatabaseEvent::dispatch($event->user, $event->classe, $event->semestre, $event->school_year_model);

                })->catch(function(Batch $batch, Throwable $er){

                    ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);
                    
                })->finally(function(Batch $batch) use ($event){

                    // UpdatePupilsMarksInsertionProgressEvent::dispatch($event->user);


            })->name('marks_insertion')->dispatch();

        $user_batch->update(['batch_id'=> $batch->id]);


    }
}
