<?php

namespace App\Listeners;

use App\Events\PupilsMarksUpdatingFailedEvent;
use App\Events\UpdatePupilsMarksUpdatingRequestsEvent;
use App\Events\UserTryingToUpdatePupilMarkEvent;
use App\Jobs\JobUpdatingPupilsMarksManager;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class AdminGetNotificationAboutTheUserWhoTryingToUpdatePupilMarkListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserTryingToUpdatePupilMarkEvent $event)
    {

        $batch = Bus::batch([

            new JobUpdatingPupilsMarksManager($event->mark, $event->mark_editor, $event->new_value, $event->others_data),

            ])->then(function(Batch $batch) use ($event){

                UpdatePupilsMarksUpdatingRequestsEvent::dispatch();

            })
            ->catch(function(Batch $batch, Throwable $er){

                PupilsMarksUpdatingFailedEvent::dispatch($event->mark, $event->mark_editor, $event->new_value, $event->others_data);

            })

            ->finally(function(Batch $batch){


            })->name('trying_pupil_mark_updating')->dispatch();
    }
}
