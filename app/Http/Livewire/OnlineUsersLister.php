<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class OnlineUsersLister extends Component
{
    protected $listeners = [
        'OnlineUsersLiveEvent' => 'getOnlineUsers',
        'UserLeavingChannelLiveEvent' => 'userLeavingChannel',
        'UserJoiningChannelLiveEvent' => 'userJoiningChannel',
    ];

    public $counter = 0;

    public $onlines_users = [];

    public function render()
    {
        $users = [];

        $onlines_users = $this->onlines_users;

        if($onlines_users){

            foreach($onlines_users as $user_id => $u){

                $user = User::find($user_id);

                if($user){

                    $users[] = $user;

                }

            }


        }

        return view('livewire.online-users-lister', compact('users'));
    }



    public function getOnlineUsers($users = [])
    {
        $ids = [];

        if($users !== []){

            foreach($users as $u){

                $ids[$u['id']] = $u;

            }


        }
        $this->onlines_users = $ids;

    }


    public function userLeavingChannel($user)
    {
        // dd($user);
    }

    public function userJoiningChannel($user)
    {
        // dd($user);
    }




    public function refreshData()
    {
        $this->counter = 1;
    }
}
