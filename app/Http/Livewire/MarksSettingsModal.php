<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Livewire\Component;

class MarksSettingsModal extends Component
{

    protected $listeners = ['onMarksSettingsEvent' => 'openModal'];
    public $classe_id;
    public $classe;
    public $semestre_type = 'Semestre';
    public $semestre_id = 1;
    public $subject_selected;
    public $mark_index;
    public $mark_type = 'epe';

    public function render()
    {
        $subjects = [];
        $marks_indexes = [];
        $classes = [];
        $types_of_marks = [
            'devoir' => 'Devoirs',
            'epe' => 'Interrogations',
            'participation' => 'Participations'

        ];
        $semestres = [1, 2];
        $school = School::first();
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'Trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }

            if($this->classe){
                $marks_indexes = $this->classe->getClasseMarksIndexes($this->subject_selected, $this->semestre_id, null, $this->mark_type);
            }

            if($marks_indexes == []){
                $marks_indexes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10, 11, 12, 13, 14, 15, 16, 17, 18, 19, 20];
            }

        }
        $school_years = SchoolYear::all();
        $subjects = Subject::all();
        
        return view('livewire.marks-settings-modal', compact('semestres', 'school_years', 'types_of_marks', 'subjects', 'marks_indexes'));
    }



    public function openModal($classe_id = null, $semestre = null, $subject_id = null)
    {
        $this->classe_id = $classe_id;
        $this->semestre_id = $semestre;
        $this->subject_selected = $subject_id;

        $this->classe = Classe::find($classe_id);
        $this->dispatchBrowserEvent('modal-marksSettings');
    }


    public function updatedSubjectSelected($subject_id)
    {
        $this->subject_selected = $subject_id;
    }


    public function updatedSemestreId($semestre_id)
    {
        $this->semestre_id = $semestre_id;
    }

    public function updatedMarkType($type)
    {
        $this->mark_type = $type;
    }




}
