<?php

namespace App\Listeners;

use App\Events\ForcingUserDisconnectionEvent;
use App\Events\PrepareUserDeletingEvent;
use App\Events\UserDeletionFailedEvent;
use App\Events\UserWasDeletedEvent;
use App\Jobs\JobUserAccountBlockingManager;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class DeleteUserListener
{

    
    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(PrepareUserDeletingEvent $event)
    {
        ForcingUserDisconnectionEvent::dispatch($event->user);

        $batch = Bus::batch([

            new JobUserAccountBlockingManager($event->user, true),

            new JobDeleteUser($event->user, $event->forceDelete),

            ])->then(function(Batch $batch) use ($event){

                UserWasDeletedEvent::dispatch($event->user);

            })
            ->catch(function(Batch $batch, Throwable $er){

                UserDeletionFailedEvent::dispatch($event->user, $er);
                
            })

            ->finally(function(Batch $batch){


        })->name('user_deletion')->dispatch();
    }
}
