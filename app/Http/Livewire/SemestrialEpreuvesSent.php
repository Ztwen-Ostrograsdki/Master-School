<?php

namespace App\Http\Livewire;

use Livewire\Component;

class SemestrialEpreuvesSent extends Component
{

    public $counter = 0;


    public function render()
    {
        return view('livewire.semestrial-epreuves-sent');
    }

    public function reloadData()
    {
        $this->counter = rand(0, 14);
    }
}
