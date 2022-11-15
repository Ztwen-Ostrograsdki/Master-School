<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\School;
use Livewire\Component;

class ClasseGroupProfil extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'classeUpdated' => 'reloadClasseGroupData',
        'classeGroupUpdated' => 'reloadClasseGroupData',
        'schoolYearChangedLiveEvent' => 'reloadClasseGroupData',
        'newClasseCreated' => 'reloadClasseGroupData',
        'newLevelCreated' => 'reloadClasseGroupData'
    ]; 

    public $editingClasseGroupeName = false;
    public $slug;
    public $counter = 0;

    public function mount($slug = null)
    {
        $this->school_year_model = $this->getSchoolYear();
        if($slug){
            $this->slug = $slug;
        }
        else{
            return abort(404);
        }
    }



    public function render()
    {
        $school = School::first();
        $semestres = [1, 2];
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        $classe_group = ClasseGroup::where('name', urldecode($this->slug))->first();
        
        return view('livewire.classe-group-profil', compact('classe_group'));
    }


    public function addNewsClassesToThisClasseGroup($classe_group_id)
    {
        $this->emit('manageClasseGroupLiveEvent', $classe_group_id);
    }

    public function removeClasseFromThisGroup($classe_id)
    {
        $classe = Classe::find($classe_id);

        if($classe){
            if($classe->classe_group->slug == $this->slug || $classe->classe_group->name == $this->slug){
                $updated = $classe->update(['classe_group_id' => null]);
                if($updated){
                    $this->emit('classeGroupUpdated');
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "la classe $classe->name é été dissociée de cette promotion avec succès! ", 'type' => 'success']);
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une erreure inconnue s'est produite lors de la mise à jour! ", 'type' => 'error']);

                }
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "La classe selectionnée n'etait pas liée à cette promotion! ", 'type' => 'warning']);

            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "La classe selectionnée n'existe pas! ", 'type' => 'error']);
        }
    }
    
    public function editClasseGroupName($classe_group_id)
    {
        // $classe = Classe::where('id', $classe_id)->first();
        // $this->classe_id = $classe->id;
        // $this->classeName = $classe->name;
        $this->editingClasseGroupeName = true;
    }


    public function reloadClasseGroupData($school_year = null)
    {
        $this->counter = 1;
    }
}
