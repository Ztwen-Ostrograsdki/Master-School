<?php

namespace App\Listeners;

use App\Events\UserConnectedEvent;
use App\Events\UsersOnlineEvent;
use App\Jobs\JobDeleteUserLockRequests;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Support\Facades\Bus;

class UserConnectedListener
{
   

    /**
     * Handle the event.
     *
     * @param  object  $event
     * @return void
     */
    public function handle(UserConnectedEvent $event)
    {
        $batch = Bus::batch([

            new JobDeleteUserLockRequests($event->user)

            ])->then(function(Batch $batch) use ($event){

                //EMIT CONNECTED EVENT TO ADMIN

                UsersOnlineEvent::dispatch($event->user);

            })->catch(function(Batch $batch, Throwable $er){

                
            })->finally(function(Batch $batch){


        })->dispatch();
    }
}
