<?php

namespace App\Http\Livewire;

use App\Helpers\Redirectors\RedirectorsDriver;
use App\Models\User;
use App\Rules\PasswordChecked;
use Illuminate\Support\Carbon;
use Livewire\Component;

class AdminAuthorization extends Component
{

    public $code;
    public $user;
    public $default_key;

    protected $rules = [
        'code' => 'required|string',
    ];

    public function mount()
    {
        $this->user = User::find(auth()->user()->id);

        if($this->user->hasAdminKey()){

            $this->default_key = $this->user->userAdminKey->key;
        }
        else{

            $this->user->__generateAdminKey();
            
            return $this->mount();
        }
        
    }

    public function updated($code)
    {
        $this->validateOnly($code);
    }

    public function render()
    {
        return view('livewire.admin-authorization');
    }


    public function authentify()
    {
        $this->user->__hydrateAdminSession();

        $this->validate();

        if($this->user->isAdminAs('master')){

            $this->validate(['code' => new PasswordChecked($this->default_key, true)]);

            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'success', 'title' => "ACCES ADMIN AUTHENTIFIE", 'message' => "Authentification réussie"]);

            $this->user->__regenerateAdminSession();

            $this->user->__regenerateAdminKey();

            RedirectorsDriver::userRedirectorDriver($this->user);

        }
        else{

            $this->validate(['code' => new PasswordChecked($this->default_key)]);


            if($this->keyIsExpires()){

                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'title' => 'Authentification échouée', 'message' => "Cette clé a déjà expiré. Veuillez renseigner la nouvelle clé."]);

                $this->addError('code', "Cette clé n'est plus valable. Taper la nouvelle qui vient d'être générer!");

                $this->user->__hydrateAdminSession();

                $this->user->__regenerateAdminKey();

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'success', 'title' => "ACCES ADMIN AUTHENTIFIE", 'message' => "Authentification réussie"]);

                $this->user->__regenerateAdminSession();

                $this->user->__regenerateAdminKey();

                RedirectorsDriver::userRedirectorDriver($this->user);
            }

        }
    }


    public function keyIsExpires()
    {
        $now = Carbon::now();

        $e = $this->user->userAdminKey->updated_at;

        $times = $now->diffInMinutes($e);

        if($times > 5){
            
            return true;
        }
        return false;
    }



}
