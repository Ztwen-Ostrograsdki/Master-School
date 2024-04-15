<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Header extends Component
{
    protected $listeners = [

        'ReloadComponentEvent' => 'reloadData',
        'RedirectoLoginPage' => 'toLoginPage',

    ];

    public $counter = 0;

    public function render()
    {
        $target = $this->counter + rand(15, 22578);

        $user = null;

        $username = null;

        $auth = auth()->user();

        if($auth){

            $user = $auth;

            $username = $auth->name;

        }

        return view('livewire.header', compact('target', 'user', 'username'));
    } 

    public function reloadData()
    {
        $this->counter = rand(1, 1275);
    }



    public function toLoginPage()
    {
        Session::flush();

        return redirect()->route('connexion');
    }

    public function newUserConnected()
    {
        
    }


    public function userDataEdited($user_id)
    {

    }


    public function openModalForMyNotifications()
    {
        $this->emit('openModalForMyNotifications');
    }
}
