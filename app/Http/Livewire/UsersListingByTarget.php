<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UsersListingByTarget extends Component
{

    protected $listeners = ['refreshDataFromUsers' => 'refreshData'];

    public $target = 'blocked';
    public $counter = 0;


    public function mount($target)
    {
        if($target){
            $this->target = $target;
        }
        else{
            return abort('404', "La section demandée est introuvable!");
        }
    }

    public function render()
    {
        $users = [];

        if($this->target == 'bloques'){
            $users = User::where('blocked', true)->orWhere('locked', true)->get();
        }
        elseif($this->target == 'confirmed'){
            $users = User::whereNotNull('email_verified_at')->get();
        }
        else{
            $users = [];

        }
        return view('livewire.users-listing-by-target', compact('users'));
    }



    public function markEmailAsVerified($user_id)
    {
        $user = User::find($user_id);

        $user->markEmailAsVerified();
    }

    public function markEmailAsUnverified($user_id)
    {
        $user = user::find($user_id);
        
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
            $user->__unlockOrLockThisUser();
        }
        $this->refreshData();
    }


    public function generateAndSendKeyToUser($user_id)
    {
        $user = User::find($user_id);

        if($user){
            $request = $user->__generateUnlockedToken();
            if($request){
                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'success', 'message' => "Clé générée avec succès!",  'title' => 'CLE ENVOYEE']);
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "La clé n'a pu être générée!",  'title' => 'Erreur']);
            }

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['type' => 'error', 'message' => "Veuillez renseigner des données valides!",  'title' => 'Erreur']);
        }

        $this->refreshData();

    }

    public function refreshData()
    {
        $this->counter = 1;
    }
}
