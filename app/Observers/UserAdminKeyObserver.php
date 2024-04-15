<?php

namespace App\Observers;

use App\Events\UserAdminSessionKeyExpiredEvent;
use App\Jobs\JobAdminSessionKeyDestroyer;
use App\Models\UserAdminKey;
use Illuminate\Support\Carbon;

class UserAdminKeyObserver
{
    public function created(UserAdminKey $key)
    {
        $user = $key->user;

        // JobAdminSessionKeyDestroyer::dispatch($key, $user)->delay(Carbon::now()->addMinutes(30));
    }


    public function deleted(UserAdminKey $key)
    {
        
        // UserAdminSessionKeyExpiredEvent::dispatch($key->user);
    }

    public function deleting(UserAdminKey $key)
    {
        UserAdminSessionKeyExpiredEvent::dispatch($key->user);
    }
}
