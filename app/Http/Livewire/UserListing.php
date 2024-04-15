<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserListing extends Component
{
    public $active_section = null;


    public $sections = [];

    public function render()
    {
        $this->sections = config('app.users_displaying_sections'); 

        if(session()->has('users_section_selected') && session('users_section_selected')){

            $this->active_section = session('users_section_selected');

        }
        return view('livewire.user-listing');
    }



    public function updatedActiveSection($section)
    {
        session()->put('users_section_selected', $section);

        $this->emit('UpdateTheActiveSection', $section);

    }


    public function deletedAdminActivesKeys()
    {

    }

    public function lockedAdminRoutes()
    {
        
    }
}
