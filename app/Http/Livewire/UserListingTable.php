<?php

namespace App\Http\Livewire;

use App\Events\PrepareUserDeletingEvent;
use App\Events\UserConfirmedEmailEvent;
use App\Models\Parentable;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Component;

class UserListingTable extends Component
{
    protected $listeners = [
        'refreshDataFromUsers' => 'refreshData', 
        'UpdatedGlobalSearch' => 'updatedSearch', 
        'UpdateTheActiveSection' => 'reloadSectionData',
        'UserConfirmedEmailLiveEvent' => 'refreshData',
        'NewUserCreatedLiveEvent' => 'refreshData',
        'OnlineUsersLiveEvent' => 'getOnlineUsers',
        'UserWasDeletedLiveEvent' => 'refreshData',
        'UserDeletionFailedLiveEvent' => 'refreshData',
    ];

    public $active_section = null;

    public $search = '';

    public $counter = 0;

    public $sections = [];

    public $onlines_users = [];

    public function render()
    {
        $users = [];

        $this->sections = config('app.users_displaying_sections');

        if(session()->has('users_section_selected') && session('users_section_selected')){
            
            $this->active_section = session('users_section_selected');

        }

        if($this->active_section == null){

            $users = User::all();
        }
        elseif($this->active_section == 'confirmeds'){

            $users = User::whereNotNull('email_verified_at')->get();
        }
        elseif($this->active_section == 'unconfirmeds'){

            $users = User::whereNull('email_verified_at')->get();
        }
        elseif($this->active_section == 'blockeds'){

            $users = User::where('blocked', true)->orWhere('locked', true)->get();
        }
        elseif($this->active_section == 'not_blockeds'){

            $users = User::where('blocked', false)->orWhere('locked', false)->get();
        }
        elseif($this->active_section == 'admins'){

            $users = [];

            $all = User::whereNotNull('email_verified_at')->get();

            foreach($all as $u){

                if($u->administrator){

                    $users[] = $u;

                }

            }

        }
        elseif($this->active_section == 'connecteds'){

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

        }
        elseif($this->active_section == 'admins_keys'){

            $users = [];

            $all = User::whereNotNull('email_verified_at')->get();

            foreach($all as $u){

                if($u->administrator && $u->hasAdminKey()){

                    $users[] = $u;

                }

            }

        }
        elseif($this->active_section == 'parents'){

            $users = [];

            $parentors = Parentable::all();

            foreach($parentors as $p){

                if($p->user){

                    $users[] = $p->user;

                }

            }

        }
        elseif($this->active_section == 'teachers'){

            $users = [];

            $teachers = Teacher::all();

            foreach($teachers as $t){

                if($t->user){

                    $users[] = $t->user;

                }

            }

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
    }

    public function reloadSectionData($section)
    {
        $this->active_section = $section;

        // session()->put('users_section_selected', $section);
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

        if($user && !$user->isAdminAs('master')){

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


    public function deleteUser($user_id)
    {
        $user = User::find($user_id);

        if($user){

            PrepareUserDeletingEvent::dispatch($user, false);

        }
    }

    public function forceDeleteUser($user_id)
    {
        $user = User::find($user_id);

        if($user){

            PrepareUserDeletingEvent::dispatch($user, true);

        }
    }
}
