<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserListing extends Component
{
    public $active_section = null;


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
        if(session()->has('users_section_selected') && session('users_section_selected')){

            $this->active_section = session('users_section_selected');

        }
        return view('livewire.user-listing');
    }



    public function updatedActiveSection($section)
    {
        $this->active_section = $section;

        $this->emit('UpdateTheActiveSection', $section);



    }
}
