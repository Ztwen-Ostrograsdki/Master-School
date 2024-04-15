<?php

namespace App\Exports;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;

class ExportPupils implements FromView, ShouldAutoSize
{

    use ModelQueryTrait;

    public $classe;
    public $semestre;
    public $subject;
    public $school_year_model;
    public $withRank;
    public $simpleFormat = true;
    public $pupil_id;

    public $semestre_type = 'Semestre';

    public $school_year;

    public $classe_subject_selected;

    public $subject_selected;

    public $semestre_selected = 1;

    public $classe_marks = [];

    public $edit_mark_value = 0;

    public $edit_mark_type = 'epe';

    public $editing_mark = false;

    public $invalid_mark = false;

    public $edit_key;

    public $mark_key;

    public $targetedMark;

    public $count = 0;

    public $search = '';

    public $computedRank = false;

    public $teacher_profil = false;

    public $relaodNow = false;

    public $is_loading = false;


    public function __construct(Classe $classe, $school_year_model, $semestre, $subject, $search = '', $withRank = false)
    {
        $this->classe = $classe;

        $this->semestre_type = session('semestre_type');

        $this->classe_id = $classe->id;

        $this->subject_selected = $subject;

        $this->school_year_model = $school_year_model;

        $this->search = $search;

        $this->withRank = $withRank;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return $this->classe->getPupils();
    }


    // public function view(): View
    public function older()
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

        return view('livewire.classe-pupils-marks-lister-formated', compact('classe', 'pupils', 'classe_subject_selected', 'is_loading', 'editingPupilName', 'pupil_id'));
    }



    public function view(): View
    {
        $pupils = [];

        $printing = false;

        $marks = [];

        $noMarks = false;

        $modality = null;

        $modalitiesActivated = null;

        $hasModalities = false;

        $averageEPETab = [];

        $averageTab = [];

        $ranksTab = [];

        $classe_subject_coef = 1;


        $marks_lenght = 1;

        $devMaxLenght = 1;

        $classe_subjects = [];

        $school_year_model = $this->school_year_model;

        $classe = $school_year_model->findClasse($this->classe_id);

        if($classe){

            $classe_subjects = $classe->subjects;

        }

        if($classe){

            $pupils = $classe->getNotAbandonnedPupils($school_year_model->id, $this->search);

            $marks = $classe->getMarks($this->subject, $this->semestre, 2, $school_year_model->school_year);

            $averageEPETab = $classe->getMarksAverage($this->subject, $this->semestre, $school_year_model->school_year, 'epe');

            $averageTab = $classe->getAverage($this->subject, $this->semestre, $school_year_model->school_year);

            if($this->withRank){

                $ranksTab = $classe->getClasseRank($this->subject, $this->semestre, $school_year_model->school_year);
            }
            else{

                $ranksTab = [];

            }

            $classe_subject_coef = $classe->get_coefs($this->subject, $school_year_model->id, true);


            $devMaxLenght = $classe->getMarksTypeLenght($this->subject, $this->semestre, $school_year_model->school_yea, 'devoir');


            if($devMaxLenght <= 2){

                $devMaxLenght = 2;
            }

            if($this->semestre && $this->subject_selected){

                $semestre = $this->semestre;

                $modality = $this->subject_selected->getAverageModalityOf($classe->id, $school_year_model->school_year, $semestre);

                $modalitiesActivated = $classe->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('activated', true)->count() > 0;

                $hasModalities = $classe->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->count() > 0;

                if($modality){

                    $modality = $modality->modality;
                }
                else{

                    $modality = null;
                }
            }

        }


        $calendar_profiler = $school_year_model->calendarProfiler();

        $current_period = $calendar_profiler['current_period'];

        $simpleFormat = $this->simpleFormat;

        $is_loading = $this->simpleFormat;

        return view('livewire.classe-pupils-marks-lister-formated', 
                    compact(
                        'classe',
                        'current_period',
                        'pupils', 'marks', 'devMaxLenght', 'noMarks', 'modality', 'modalitiesActivated', 'hasModalities', 'averageEPETab', 'averageTab', 'classe_subject_coef', 'ranksTab', 'classe_subjects', 'school_year_model', 'printing', 'simpleFormat', 'is_loading'
                    )
                );

    }
}
