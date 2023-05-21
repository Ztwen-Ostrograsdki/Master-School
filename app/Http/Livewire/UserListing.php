<?php

namespace App\Http\Livewire;

use App\Models\User;
use Livewire\Component;

class UserListing extends Component
{
    public $active_section = 'all';

    public function render()
    {
        return view('livewire.user-listing');
    }



    public function setUsersActiveSection($section)
    {
        $this->active_section = $section;
        session()->put('users_section_selected', $section);
    }
}
