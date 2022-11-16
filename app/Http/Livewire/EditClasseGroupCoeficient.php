<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Coeficient;
use App\Models\Subject;
use Livewire\Component;

class EditClasseGroupCoeficient extends Component
{
    use ModelQueryTrait;
    protected $listeners = ['editClasseGroupCoeficientLiveEvent' => 'openModal'];
    public $classe_group_id;
    public $classe_group;
    public $classe_id;
    public $subject_id;
    public $value = 1;
    public $coeficient = null;

    protected $rules = [
        'value' => 'required|numeric|min:1',

    ];


    public function render()
    {
        $subjects = [];
        if ($this->classe_group) {
            $subjects = $this->classe_group->subjects;
        }
        return view('livewire.edit-classe-group-coeficient', compact('subjects'));
    }


    public function submit()
    {
        $this->validate();
        if ($this->subject_id) {
            if ($this->coeficient) {
                $updated = $this->coeficient->update(['subject_id' => $this->subject_id, 'coef' => $this->value]);
                if($updated){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "La table des coéfiscients a été mise à jour avec succès!", 'type' => 'success']);
                    $this->dispatchBrowserEvent('hide-form');
                    $this->reset('value', 'subject_id');
                    $this->emit('classeGroupUpdated');
                }
                else{
                    return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une Erreure s'est produite, Veuillez vérifier votre requête et réessayer!", 'type' => 'error']);
                }
            }
            else{
                $school_year_model = $this->getSchoolYear();
                $coeficient = Coeficient::create([
                    'classe_group_id' => $this->classe_group->id,
                    'subject_id' => $this->subject_id,
                    'school_year' => $school_year_model->id,
                    'coef' => $this->value,

                ]);

                if ($coeficient) {
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "La table des coéfiscients a été mise à jour avec succès!", 'type' => 'success']);
                    $this->dispatchBrowserEvent('hide-form');
                    $this->emit('classeGroupUpdated');
                    $this->reset('value', 'subject_id');

                }
                else{
                    return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une Erreure s'est produite, Veuillez vérifier votre requête et réessayer!", 'type' => 'error']);
                }


            }
        }

    }



    public function openModal($classe_group_id, $subject_id, $coeficient_id = null)
    {
        if($classe_group_id){
            $classe_group = ClasseGroup::find($classe_group_id);
            if($classe_group){
                $this->classe_group = $classe_group;
                if($subject_id){
                    $subject = Subject::find($subject_id);
                    if ($subject && $subject->level_id == $classe_group->level_id) {
                        $this->subject_id = $subject->id;
                    }

                }

                if ($coeficient_id) {
                    $coeficient = Coeficient::find($coeficient_id);
                    if($coeficient){
                        $this->coeficient = $coeficient;
                        $this->value = $coeficient->coef;
                    }
                    else{
                        return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Les données sont corrompues. Veuillez vérifier votre requête et réessayer!", 'type' => 'error']);

                    }
                }
                $this->dispatchBrowserEvent('modal-editClasseGroupCoeficients');

            }

        }
        else{
            return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure ', 'message' => "Les données sont corrompues. Veuillez sélectionner une promotion valide et réessayer!", 'type' => 'error']);

        }







    }
}
