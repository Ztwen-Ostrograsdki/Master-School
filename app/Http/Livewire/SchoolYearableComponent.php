<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Route;
use Livewire\Component;


class SchoolYearableComponent extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData', 
        'NewCalendarAddedLiveEvent' => 'reloadData', 
        'newTeacherHasBeenAdded' => 'reloadData', 
        'newPupilHasBeenAdded' => 'reloadData', 
        'newClasseCreated' => 'reloadData', 
        'newClasseGroupCreated' => 'reloadData', 
        'classeUpdated' => 'reloadData', 
        'pupilUpdated' => 'reloadData', 
        'UpdatedSchoolYearData' => 'reloadData', 
    ];

    public $dataContent = [
        "App\Models\User" => 'Les utilisateurs',
        "App\Models\Classe" => 'Les Classes ou groupes pédagogiques',
        "App\Models\ClasseGroup" => 'Les promotions',
        "App\Models\Teacher" => 'Les Enseignants',
        "App\Models\Subject" => 'Les Matières',
        "App\Models\Pupil" => 'Les Apprenants',
        "App\Models\Parentable" => 'Les Parents',
    ];

    public $classMapping = "App\Models\Pupil";


    public $counter = 1;

    public $occurence = 0;

    public $theLevel = 'Secondaire';

    public $levelName = 'secondary';

    public $levelTable = ['secondary' => 'Secondaire', 'primary' => 'Primaire'];



    public function render()
    {
        $routeName = Route::currentRouteName();

        $data = [];

        if($routeName == 'data_manager_secondary'){

            $this->levelName = "secondary";
        }
        elseif($routeName == 'data_manager_primary'){
            
            $this->levelName = "primary";
        }

        if(session()->has('school_year_manager_section') && session('school_year_manager_section') !== null){
            
            $this->classMapping = session('school_year_manager_section');

        }

        if($this->classMapping){

            $data = $this->classMapping::all();

        }

        return view('livewire.school-yearable-component', compact('data'));
    }


    public function updatedClassMapping($classMapping)
    {
        session()->put('school_year_manager_section', $classMapping);

        $this->classMapping = $classMapping;
    }


    public function reloadData()
    {
        $this->counter = rand(1, 22);
    }
}
