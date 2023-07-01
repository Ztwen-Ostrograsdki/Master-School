<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClasseGroup;
use App\Models\Level;
use App\Models\School;
use App\Models\SchoolYear;
use Livewire\Component;

class AdminAsideComponent extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'classePupilListUpdated' => 'reloadData',
        'newClasseCreated' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'newClasseGroupCreated' => 'reloadData',
        'schoolHasBeenCreated' => 'reloadData',
        'newLevelCreated' => 'reloadData',
    ];

    public $counter = 0;
    public $has_school = false;

   public function mount()
   {

   }

    public function render()
    {
        $school_name = null;

        $has_school = count(School::all());
        $school_years = count(SchoolYear::all());
        $levels = [];
        $classes = [];
        $pupils = [];
        $teachers = [];
        $classe_groups = [];

        if($school_years > 0 && $has_school > 0){
            $this->has_school = true;
            $school_name = School::all()->first()->name;
            $school_year_model = $this->getSchoolYear();
            $levels = Level::all();
            $classes = $school_year_model->classes;
            $pupils = $school_year_model->pupils;
            $classe_groups = $school_year_model->classe_groups;
            $teachers = $school_year_model->teachers;
        }

        

        return view('livewire.admin-aside-component', compact('levels', 'pupils', 'teachers', 'classes', 'school_name', 'classe_groups'));
    }

    public function addNewPupil()
    {
        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->first();
        if($classe){
            $this->emit('addNewPupilToClasseLiveEvent', $classe->id);
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
        }

    }

    public function createNewClasse()
    {
        $this->emit('createNewClasseLiveEvent');
    }


    public function addNewClasseGroup()
    {
        $this->emit('createNewClasseGroupLiveEvent');
    }


    public function reloadData($school_year = null)
    {
        $this->counter = 1;
    }

    public function throwSchoolBuiding()
    {
        $this->emit('throwSchoolBuiding');

    }
}
