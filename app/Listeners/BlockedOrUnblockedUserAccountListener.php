<?php

namespace App\Listeners;

use App\Events\ForcingUserDisconnectionEvent;
use App\Events\UserAccountBlockedEvent;
use App\Jobs\JobUserAccountBlockingManager;
use Illuminate\Bus\Batch;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class BlockedOrUnblockedUserAccountListener
{
    

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserAccountBlockedEvent $event)
    {
        if($event->user->isBlocked()){

            ForcingUserDisconnectionEvent::dispatch($event->user);

        }

        $batch = Bus::batch([

            new JobUserAccountBlockingManager($event->user)

            ])->then(function(Batch $batch) use ($event){

                if($event->user->isBlocked()){

                    ForcingUserDisconnectionEvent::dispatch($event->user);

                }
                else{

                    $event->user->deleteLockedRequest();

                }

            })->catch(function(Batch $batch, Throwable $er){


            })->finally(function(Batch $batch){


            })->dispatch();
    }
}
