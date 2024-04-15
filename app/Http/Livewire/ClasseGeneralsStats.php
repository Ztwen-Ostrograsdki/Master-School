<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\Subject;
use Livewire\Component;

class ClasseGeneralsStats extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'classeSubjectUpdated' => 'reloadData',
        'classePupilListUpdated' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'semestreWasChanged',
    ];
    public $classe_id;
    public $pupil_id;
    public $teacher_id;
    public $classe;
    public $semestre_type = 'Semestre';
    public $semestre_selected = 1;
    public $subject_selected = null;
    public $count = 0;
    public $openDynamicStats = true;

    public function mount()
    {

    }

    public function render()
    {
        $subjects = [];
        $stats = [];

        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }

        if(session()->has('semestre_type') && session('semestre_type')){
            $semestre_type = session('semestre_type');
            session()->put('semestre_type', $semestre_type);
            $this->semestre_type = $semestre_type;
        }
        else{
            session()->put('semestre_type', $this->semestre_type);
        }

        if($classe){

            $this->classe = $classe;

            if($this->subject_selected){

                $subject = Subject::find($this->subject_selected);

                $subjects[] = $subject;
            }
            else{

                $subjects = $classe->subjects;
            }

            $pupils = $classe->getNotAbandonnedPupils($school_year_model->id);

            $stats = $classe->getClasseStats($this->semestre_selected, $school_year_model->school_year, $this->subject_selected);
        }
        return view('livewire.classe-generals-stats',compact('stats', 'subjects'));
    }


   

    public function semestreWasChanged($semestre_selected = null)
    {
        session()->put('semestre_selected', $semestre_selected);
        $this->semestre_selected = $semestre_selected;
    }

    public function reloadData($data = null)
    {
        $this->count = 1;
    }
}
