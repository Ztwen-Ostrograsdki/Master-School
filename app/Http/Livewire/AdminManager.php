<?php

namespace App\Http\Livewire;

use App\Models\Administrator;
use App\Models\User;
use Livewire\Component;

class AdminManager extends Component
{
    protected $listeners = ['manageAdminStatus' => 'openModal'];


    public $user_id;
    public $user;


    public function render()
    {
        $status = config('app.admin_abilities');
        
        return view('livewire.admin-manager', compact('status'));
    }

    public function submit($status)
    {
        if($status && $this->user){

            $user_status = $this->user->administrator;

            if($user_status){

                $user_status->update(['status' => $status]);

                $this->dispatchBrowserEvent('hide-form');

                $this->emit('refreshDataFromUsers');
            }
            else{

                $make = Administrator::create(['status' => $status, 'user_id' => $this->user->id]);

                if($make){

                    $this->dispatchBrowserEvent('hide-form');

                    $this->emit('refreshDataFromUsers');

                }
            }

        }

    }

    public function deleteFromAdmin()
    {
        if($this->user){

            $user_status = $this->user->administrator;

            if($user_status){

                $user_status->delete();

                $this->dispatchBrowserEvent('hide-form');

                $this->emit('refreshDataFromUsers');
            }
            else{
                
            }

        }

    }





    public function openModal($user_id)
    {
        $user = User::find($user_id);
        
        $this->user = $user;
        
        $this->dispatchBrowserEvent('modal-manageAdminStatus');

    }
}
