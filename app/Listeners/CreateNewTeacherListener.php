<?php

namespace App\Listeners;

use App\Events\NewTeacherWasCreatedEvent;
use App\Events\PreparingToCreateNewTeacherEvent;
use App\Events\TeacherCreatingOrUpdatingFailedEvent;
use App\Jobs\JobCreateNewTeacher;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class CreateNewTeacherListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PreparingToCreateNewTeacherEvent $event)
    {
        $batch = Bus::batch([

            new JobCreateNewTeacher($event->contacts, $event->level_id, $event->name, $event->nationality, $event->marital_status, $event->school_year_model, $event->subject, $event->surname, $event->user, $event->updating, $event->updating_subject, $event->old_subject),


            ])->then(function(Batch $batch) use ($event){

                NewTeacherWasCreatedEvent::dispatch($event->user);
                
            })->catch(function(Batch $batch, Throwable $er){

                $message = "L'enseignant que vous avez tenté de créé et lié au compte " . $event->email . " dont le pseudo est " . $event->pseudo . " n'a pas être traité. Une erreure est survenue!";

                TeacherCreatingOrUpdatingFailedEvent::dispatch($event->user, $message);

                
            })->finally(function(Batch $batch){


        })->name('new_teacher_creation')->dispatch();
    }
}
