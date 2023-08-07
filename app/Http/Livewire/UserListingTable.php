<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserListingTable extends Component
{
    protected $listeners = ['refreshDataFromUsers' => 'refreshData', 'UpdatedGlobalSearch' => 'updatedSearch', 'UpdateTheActiveSection' => 'updatedActiveSection'];

    public $active_section = null;

    public $search = '';

    public $counter = 0;

    public $sections = [
        null => "Tous les utilisateurs",
        'blockeds' => "Tous les utilisateurs Bloqués",
        'confirmeds' => "Tous les utilisateurs Confirmés",
        'unconfirmeds' => "Tous les utilisateurs Non Confirmés",
        'blockeds_unconfirmeds' => "Tous les utilisateurs Bloqués Non Confirmés",
        'blockeds_confirmeds' => "Tous les utilisateurs Bloqués Confirmé",

    ];

    public function render()
    {
        $users = [];

        if(session()->has('users_section_selected') && session('users_section_selected')){
            
            $this->active_section = session('users_section_selected');

        }

        if($this->active_section == null){

            $users = User::all();
        }
        elseif($this->active_section == 'confirmed'){

            $users = User::whereNotNull('email_verified_at')->get();
        }
        else{

            $users = [];

        }
        return view('livewire.user-listing-table', compact('users'));
    }


    public function updatedSearch($search)
    {
        $this->search = $search;
    }

    public function updatedActiveSection($section)
    {
        $this->active_section = $section;

        session()->put('users_section_selected', $section);
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
