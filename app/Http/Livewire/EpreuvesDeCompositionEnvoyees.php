<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Livewire\Component;

class EpreuvesDeCompositionEnvoyees extends Component
{

    use ModelQueryTrait;

    public $semestre_selected = 1;

    public function render()
    {
        $aes = [];

        $subjects = [];

        $semestre_type = 'Semestre';

        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $semestre_type = 'Trimestre';

        }

        $school_year_model = $this->getSchoolYear();

        $subjects = $school_year_model->subjects;


        return view('livewire.epreuves-de-composition-envoyees', compact('semestres', 'semestre_type', 'school_year_model', 'subjects'));
    }

    public function updatedSemestreSelected($semestre)
    {
        $this->semestre_selected = $semestre;

        session()->put('semestre_selected', $semestre);
    }
}
