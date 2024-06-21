<?php

namespace App\Observers;

use App\Events\NewUserCreatedEvent;
use App\Events\UserAccountBlockedEvent;
use App\Models\User;

class UserObserver
{
    /**
     * Handle the User "created" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function created(User $user)
    {
        NewUserCreatedEvent::dispatch($user);
    }

    /**
     * Handle the User "updating" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function updating(User $user)
    {
        if($user->isDirty(['locked', 'blocked']) && ($user->locked == true || $user->blocked == true)){

            UserAccountBlockedEvent::dispatch($user);

        }
    }

    // /**
    //  * Handle the User "updated" event.
    //  *
    //  * @param  \App\Models\User  $user
    //  * @return void
    //  */
    // public function updated(User $user)
    // {
    //     if($user->isDirty(['locked', 'blocked']) && ($user->locked == true || $user->blocked == true)){

    //         UserAccountBlockedEvent::dispatch($user);

    //     }
    // }

    /**
     * Handle the User "deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function deleted(User $user)
    {
        //
    }

    /**
     * Handle the User "restored" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function restored(User $user)
    {
        //
    }

    /**
     * Handle the User "force deleted" event.
     *
     * @param  \App\Models\User  $user
     * @return void
     */
    public function forceDeleted(User $user)
    {
        //
    }
}
