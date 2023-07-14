<?php

namespace App\Http\Livewire;

use App\Models\ClasseGroup;
use Livewire\Component;

class ClasseGroupCoefListe extends Component
{
    protected $listeners = [
        'classeUpdated' => 'reloadClasseGroupData',
        'classeGroupUpdated' => 'reloadClasseGroupData',
        'classeGroupSubjectsUpdated' => 'reloadClasseGroupData',
        'schoolYearChangedLiveEvent' => 'reloadClasseGroupData',
        'newClasseCreated' => 'reloadClasseGroupData',
        'newLevelCreated' => 'reloadClasseGroupData',
        'QuotaTableUpdated' => 'reloadClasseGroupData',
    ]; 
    public $classe_group_id;


    public function render()
    {
        $classe_group = ClasseGroup::find($this->classe_group_id);

        return view('livewire.classe-group-coef-liste', compact('classe_group'));
    }

     public function reloadClasseGroupData($school_year = null)
    {
        $this->counter = 1;
    }


    public function editClasseGroupCoeficient($classe_group_id, $subject_id, $coeficient_id = null)
    {
        $this->emit('editClasseGroupCoeficientLiveEvent', $classe_group_id, $subject_id, $coeficient_id);
    }


    public function manageQuotaFrom($quotaModel_id = null, $subject_id = null, $classe_id = null, $classe_group_id = null)
    {
        $this->emit('ManageQuotaLiveEvent', $quotaModel_id, $subject_id, $classe_id,  $classe_group_id);
    }
}
