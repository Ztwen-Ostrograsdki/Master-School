<?php

namespace App\Listeners;

use App\Events\ClasseMarksWasFailedEvent;
use App\Events\ClasseMarksWasUpdatedIntoDBSuccessfullyEvent;
use App\Events\InitiateClasseDataUpdatingEvent;
use App\Events\MarksNullActionsEvent;
use App\Jobs\JobFlushAveragesIntoDataBase;
use App\Jobs\JobMarksNullActionsManager;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class MarksNullActionsBatcherListener
{

    public $pupil;

    public $classe;

    public $subject;

    public $user;

    public $school_year_model;

    public $semestre = 1;

    public $action;


    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(MarksNullActionsEvent $event)
    {
        InitiateClasseDataUpdatingEvent::dispatch($event->user, $event->classe);
        
        $batch = Bus::batch([

            new JobMarksNullActionsManager($event->action, $event->classe, $event->semestre, $event->subject, $event->school_year_model, $event->pupil, $event->user),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, $event->semestre),

            new JobFlushAveragesIntoDataBase($event->user, $event->classe, $event->school_year_model, null),

            ])->then(function(Batch $batch) use ($event){

                ClasseMarksWasUpdatedIntoDBSuccessfullyEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                ClasseMarksWasFailedEvent::dispatch($event->user, $event->classe, $event->subject);

            })

            ->finally(function(Batch $batch){


            })->dispatch();
    }
}
