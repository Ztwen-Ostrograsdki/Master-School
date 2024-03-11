<?php

namespace App\Exports;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;

class ExportPupils implements FromView
{

    use ModelQueryTrait;

    public $classe;

    public function __construct(Classe $classe)
    {
        $this->classe = $classe;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->classe->getPupils();
    }


    public function view(): View
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe->id);
        
        $school_years = SchoolYear::all();
        
        $pupils = [];

        $is_loading = false;

        $editingPupilName = false;

        $pupil_id = null;

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){

            $subject_id = intval(session('classe_subject_selected'));

            if($classe && in_array($subject_id, $classe->subjects->pluck('id')->toArray())){

                session()->put('classe_subject_selected', $subject_id);

                $classe_subject_selected = $subject_id;
            }
            else{
                $classe_subject_selected = null;
            }
        }
        else{
            $classe_subject_selected = null;
        }

        if($classe){
            
            $pupils = $classe->getPupils($school_year_model->id);
        }

        return view('livewire.classe-pupils-lister', compact('classe', 'pupils', 'classe_subject_selected', 'is_loading', 'editingPupilName', 'pupil_id'));
    }
}
