<?php

namespace App\Http\Livewire;

use App\Events\UserAccountBlockedEvent;
use App\Models\LockedUsersRequest;
use Livewire\Component;

class AdminNotificationsCenterComponent extends Component
{
    protected $listeners = [
        'UserSentLockedRequestLiveEvent' => 'reloadLockedRequests',
        'RefreshLockedRequestListLiveEvent' => 'reloadLockedRequests',

    ];

    public $counter = 0;


    public function render()
    {
        $last_locked_request = null;

        $all_notifications = 0;

        $lockedRequests = LockedUsersRequest::orderBy('updated_at', 'desc')->get();

        if(count($lockedRequests)){

            $last_locked_request = $lockedRequests->first();

        }

        $all_notifications = $all_notifications + count($lockedRequests);

        return view('livewire.admin-notifications-center-component', compact('lockedRequests', 'last_locked_request', 'all_notifications'));
    }


    public function reloadLockedRequests()
    {
        $this->counter = rand(11, 23);
    }

    public function deleteLockedRequest($lockedRequest_id)
    {
        LockedUsersRequest::find($lockedRequest_id)->delete();

        $this->reloadLockedRequests();
    }

    public function solvedLockedRequest($lockedRequest_id)
    {
        $locked = LockedUsersRequest::find($lockedRequest_id);

        if($locked){

            $user = $locked->user;

            if($user && $user->isBlocked()){

                UserAccountBlockedEvent::dispatch($user);

            }

        }

        $this->reloadLockedRequests();
    }
}
