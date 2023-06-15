<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserListingTable extends Component
{
    protected $listeners = ['refreshDataFromUsers' => 'refreshData'];

    public $target = 'all';
    public $counter = 0;

    public function render()
    {
        $users = [];

        if($this->target == 'all'){
            $users = User::all();
        }
        elseif($this->target == 'confirmed'){
            $users = User::whereNotNull('email_verified_at')->get();
        }
        else{
            $users = [];

        }
        return view('livewire.user-listing-table', compact('users'));
    }



    public function markEmailAsVerified($user_id)
    {
        $user = user::find($user_id);
        $user->markEmailAsVerified();
    }

    public function markEmailAsUnverified($user_id)
    {
        $user = User::find($user_id);
        $user->markEmailAsOnlyUnverified();
    }


    public function manageAdminStatus($user_id)
    {
        $this->emit('manageAdminStatus', $user_id);
    }


    public function blockerManager($user_id)
    {
        $user = User::find($user_id);
        if($user){
            if(!$user->blocked && !$user->locked){
                $user->update(['locked' => true, 'blocked' => true]);
            }
            else{
                $user->update(['locked' => false, 'blocked' => false]);
            }
        }
        $this->refreshData();
    }

    public function refreshData()
    {
        $this->counter = 1;
    }
}
