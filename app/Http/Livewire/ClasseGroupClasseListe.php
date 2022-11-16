<?php

namespace App\Http\Livewire;

use App\Models\ClasseGroup;
use Livewire\Component;

class ClasseGroupClasseListe extends Component
{
    protected $listeners = [
        'classeUpdated' => 'reloadClasseGroupData',
        'classeGroupUpdated' => 'reloadClasseGroupData',
        'schoolYearChangedLiveEvent' => 'reloadClasseGroupData',
        'newClasseCreated' => 'reloadClasseGroupData',
        'newLevelCreated' => 'reloadClasseGroupData'
    ]; 
    public $classe_group_id;


    public function render()
    {
        $classe_group = ClasseGroup::find($this->classe_group_id);
        return view('livewire.classe-group-classe-liste', compact('classe_group'));
    }

    public function reloadClasseGroupData($school_year = null)
    {
        $this->counter = 1;
    }
}
