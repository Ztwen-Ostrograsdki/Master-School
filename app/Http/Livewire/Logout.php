<?php

namespace App\Http\Livewire;

use App\Events\UserLeavingChannelEvent;
use App\Events\UsersOnlineEvent;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Livewire\Component;

class Logout extends Component
{
    public function render()
    {
        return view('livewire.logout');
    }

    public $email;
    public $password;
    protected $rules = [
        'email' => 'required|email|between:5,255',
        'password' => 'required|string',
    ];


    public function logout()
    {
        $auth = auth()->user();

        // UsersOnlineEvent::dispatch($auth);

        // UserLeavingChannelEvent::dispatch($auth);

        Auth::guard('web')->logout();

        $this->dispatchBrowserEvent('hide-form');

        $this->dispatchBrowserEvent('Toast', ['title' => 'Deconnexion réussie!!!', 'message' => "Vous serez redirigé !", 'type' => 'success']);

        Session::flush();

        return redirect()->route('connexion');
    }

    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');

        Session::flush();
    }

}
