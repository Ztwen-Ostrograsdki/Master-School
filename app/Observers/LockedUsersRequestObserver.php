<?php

namespace App\Observers;

use App\Events\RefreshLockedRequestListEvent;
use App\Events\UserSentLockedRequestEvent;
use App\Models\Administrator;
use App\Models\LockedUsersRequest;

class LockedUsersRequestObserver
{
    /**
     * Handle the LockedUsersRequest "created" event.
     *
     * @param  \App\Models\LockedUsersRequest  $lockedUsersRequest
     * @return void
     */
    public function created(LockedUsersRequest $lockedUsersRequest)
    {
        $admin = Administrator::where('status', 'master')->first();

        if($admin){

            $auth = $admin->user;

            UserSentLockedRequestEvent::dispatch($auth, $lockedUsersRequest->user);

        }
        else{

            UserSentLockedRequestEvent::dispatch($lockedUsersRequest->user, $lockedUsersRequest->user);

        }

    }

    /**
     * Handle the LockedUsersRequest "updated" event.
     *
     * @param  \App\Models\LockedUsersRequest  $lockedUsersRequest
     * @return void
     */
    public function updated(LockedUsersRequest $lockedUsersRequest)
    {
        $admin = Administrator::where('status', 'master')->first();

        if($admin){

            $auth = $admin->user;

            UserSentLockedRequestEvent::dispatch($auth, $lockedUsersRequest->user);

        }
        else{

            UserSentLockedRequestEvent::dispatch($lockedUsersRequest->user, $lockedUsersRequest->user);

        }
    }

    /**
     * Handle the LockedUsersRequest "deleted" event.
     *
     * @param  \App\Models\LockedUsersRequest  $lockedUsersRequest
     * @return void
     */
    public function deleted(LockedUsersRequest $lockedUsersRequest)
    {
        $auth = auth()->user();

        RefreshLockedRequestListEvent::dispatch($auth);
    }

    /**
     * Handle the LockedUsersRequest "restored" event.
     *
     * @param  \App\Models\LockedUsersRequest  $lockedUsersRequest
     * @return void
     */
    public function restored(LockedUsersRequest $lockedUsersRequest)
    {
        //
    }

    /**
     * Handle the LockedUsersRequest "force deleted" event.
     *
     * @param  \App\Models\LockedUsersRequest  $lockedUsersRequest
     * @return void
     */
    public function forceDeleted(LockedUsersRequest $lockedUsersRequest)
    {
        //
    }
}
