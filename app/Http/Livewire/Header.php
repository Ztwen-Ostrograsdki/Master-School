<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;

class Header extends Component
{
    protected $listeners = [];
    public $user;
    public $username;


    public function mount()
    {
        $this->getData();
        $this->getUserData();
    }
    public function render()
    {
        return view('livewire.header');
    } 



    public function booted()
    {
        $this->getUserData();
    }

    public function newUserConnected()
    {
        return $this->user = Auth::user();
    }


    public function getUserData()
    {
        $user = Auth::user();
        if($user){
            $this->user = Auth::user();
        }
    }

    public function getData()
    {
    }

    public function userDataEdited($user_id)
    {
        if(Auth::user() && $user_id == Auth::user()->id){
            return $this->user = Auth::user();
        }
    }


    public function openModalForMyNotifications()
    {
        $this->emit('openModalForMyNotifications');
    }
}
