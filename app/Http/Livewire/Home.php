<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Home extends Component
{
    protected $listeners = [];

    public function render()
    {
        return view('livewire.home');

    }

}
