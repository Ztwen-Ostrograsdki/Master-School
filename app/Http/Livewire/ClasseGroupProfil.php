<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClasseGroup;
use App\Models\School;
use Livewire\Component;

class ClasseGroupProfil extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'classeUpdated' => 'reloadClasseData',
        'newLevelCreated' => 'reloadClasseData'
    ]; 

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


    public function editClasseSubjects($classe_id = null)
    {
        $school_year = session('school_year_selected');
        $classe = Classe::where('id', $classe_id)->first();
        if($classe){
            $this->emit('manageClasseSubjectsLiveEvent', $classe->id);
        }

    }
    
    public function editClasseName($classe_id)
    {
        $classe = Classe::where('id', $classe_id)->first();
        $this->classe_id = $classe->id;
        $this->classeName = $classe->name;
        $this->editingClasseName = true;
    }


    public function reloadClasseData($school_year = null)
    {
        $this->counter = 1;
    }
}
