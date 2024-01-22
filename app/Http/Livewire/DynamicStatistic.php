<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Helpers\Operators\Computator;
use App\Models\Classe;
use App\Models\School;
use App\Models\Subject;
use Livewire\Component;

class DynamicStatistic extends Component
{
    protected $listeners = ['getNewDataDynamicStatisic' => 'getData'];

    public $classe_id;
    public $teacher_id;
    public $showList = true|false;
    public $school_year_model;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $subject_selected;
    public $subject = 'la matière';
    public $semestre_selected = 1;
    public $type = 'devoir';
    public $mark_index = 1;
    public $counter = 0;
    public $subject_id;
    public $intervalles = 'N<7;7<=N<9;9<=N<10;10<=N<12;N>=12';
    public $size = 0;
    public $stats = [];

    protected $rules = [
        'classe_id' => 'required|numeric',
        'subject_selected' => 'required|numeric',
        'semestre_selected' => 'required|numeric',
        'type' => 'required|string',
        'mark_index' => 'required|numeric',
        'intervalles' => 'required|string',
    ];

    use ModelQueryTrait;
    use Computator;




    public function render()
    {
        $subjects = [];
        $classes = [];
        $this->school_year_model = $this->getSchoolYear();
        $maxLenght = 1;


        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }


        $classe = $this->school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        if($classe){
            if($this->teacher_id){
                $teacher = $this->school_year_model->teachers()->where('teachers.id', $this->teacher_id)->first();
                $classes = $teacher->getTeachersCurrentClasses();
                $subjects[] = $teacher->speciality();
                $subject_id = $teacher->speciality()->id;
                
                if(in_array($subject_id, $classe->subjects()->pluck('subjects.id')->toArray())){
                    session()->put('classe_subject_selected', $subject_id);
                    $this->subject_selected = $subject_id;
                }
            }
            else{
                $classes = $this->school_year_model->classes;
                if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
                    $subject_id = intval(session('classe_subject_selected'));
                    if(in_array($subject_id, $classe->subjects()->pluck('subjects.id')->toArray())){
                        session()->put('classe_subject_selected', $subject_id);
                        $this->subject_selected = $subject_id;
                    }
                }
                $subjects = $classe->subjects;
            }
            


            if($this->subject_selected && $this->semestre_selected){
                if($this->type == 'devoir'){
                    $maxLenght = $classe->getMarksTypeLenght($this->subject_selected, $this->semestre_selected, $this->school_year_model->school_yea, 'devoir');
                }
                else{
                    $maxLenght = $classe->getMarksTypeLenght($this->subject_selected, $this->semestre_selected, $this->school_year_model->school_yea, 'epe');
                }
            }

        }

        $types_of_marks = [
            'devoir' => 'Devoirs',
            'epe' => 'Interrogations'

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
        }

        return view('livewire.dynamic-statistic', compact('classes', 'classe', 'subjects', 'semestres', 'types_of_marks', 'maxLenght'));
    }

    public function toggleListing()
    {
        $this->showList = !$this->showList;
    }


    public function updatedSemestre($semestre_id)
    {
        $this->reset('stats');

        $this->semestre_selected = $semestre_id;
    }

    public function updatedSubject($subject_id)
    {
        $this->reset('stats');

        $this->subject_selected = $subject_id;

    }


    public function updatedType($type)
    {
        $this->reset('stats');

        $this->type = $type;
    }

    public function updatedClasseId($classe_id)
    {
        $this->reset('stats');

        $this->classe_id = intval($classe_id);
    }


    public function getStats()
    {
        $this->validate();

        $marks_values = [];
        $all_matches = [];

        $marks = $this->school_year_model->marks()->where('classe_id', $this->classe_id)->where('type', $this->type)->where('mark_index', $this->mark_index)->where('semestre', $this->semestre_selected)->where('subject_id', $this->subject_selected)->get();

        $this->subject = Subject::find($this->subject_selected)->name;

        foreach($marks as $mark){
            $marks_values[] = $mark->value;
        }

        $intervalles_tabs = explode(';', $this->intervalles);

        $withList = $this->showList;

        $stats = $this->getClasseStats($intervalles_tabs, $this->classe_id, $this->type, $this->mark_index, $this->semestre_selected, $this->subject_selected, $marks_values, $withList);

        $this->stats = $stats;

    }
}
