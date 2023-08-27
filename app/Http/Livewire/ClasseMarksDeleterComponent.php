<?php

namespace App\Http\Livewire;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ClasseMarksDeleterComponent extends Component
{
    protected $listeners = ['ThrowClasseMarksDeleterLiveEvent' => 'openModal'];

    public $classe_id;

    public $years = [];

    public $targeted_pupil;

    public $subject_id;

    public $marks = [];

    public $epe_marks;

    public $start;

    public $end;

    public $dev_marks;

    public $type = 'epe';

    public $semestre_id = 1;

    public $subject;

    public $title = "Rafraichissement des notes de classe";

    public $classe;

    public $semestre_type = 'Semestre';

    public $school_year;

    public $hasErrorsHere = false;


    use ModelQueryTrait;

    use DateFormattor;


    protected $rules = ['subject_id' => 'required|int'];

    public function render()
    {
        $types_of_marks = [
            'devoir' => 'Devoirs',
            'epe' => 'Interrogations',
            'participation' => 'Participations'

        ];

        $pupils = [];

        $period_string = 'Indéfinie...';

        $subjects = [];

        if($this->classe){

            $pupils = $this->classe->getPupils();

            $subjects = $this->classe->subjects;

        }

        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $this->semestre_type = 'Trimestre';
        }

        if($this->start && $this->end){

            $period_string = 'Du '. $this->__getDateAsString($this->start, false) . ' Au ' . $this->__getDateAsString($this->end, false);

        }

        $school_years = SchoolYear::orderBy('school_year', 'desc')->get();

        return view('livewire.classe-marks-deleter-component', compact('semestres', 'school_years', 'types_of_marks', 'subjects', 'pupils', 'types_of_marks', 'period_string'));
    }


    public function updatedSubjectId($subject_id)
    {
        $subject = Subject::find($subject_id);

        $this->subject = $subject;

    }

    public function updatedClasseId($classe_id)
    {
        $classe = Classe::find($classe_id);

        $this->classe = $classe;

    }


    public function openModal($classe_id, $school_year, $semestre, $subject, $type = null)
    {

        $school_year_model = $this->getSchoolYear($school_year);

        $classe = $school_year_model->findClasse($classe_id);

        $this->years = explode(' - ', $school_year_model->school_year);

        if($classe){

            $this->classe = $classe;

            $this->classe_id = $classe->id;

            $this->school_year_id = $school_year_model->id;

            $teacher = auth()->user()->teacher;

            $subject = $teacher->speciality();

            $this->subject = $subject;

            $this->subject_id = $subject->id;

            $this->semestre_id = session('semestre_selected');

            // $this->end = Carbon::now()->timestamp;


            $this->dispatchBrowserEvent('modal-classeMarksDeleter');

        }

    }

    public function updatedStart($start)
    {
        $this->resetErrorBag(['start', 'end']);

        $this->start = $start;

        $this->validatePeriods($this->start, $this->end);
    }


    public function updatedEnd($end)
    {
        $this->resetErrorBag(['end', 'start']);

        $this->end = $end;

        $this->validatePeriods($this->start, $this->end);
    }


    public function validatePeriods($start, $end)
    {
        $errors = null;

        if($start && $end){

            if(in_array(Carbon::parse($start)->year, $this->years) && in_array(Carbon::parse($end)->year, $this->years)){

                $timestamp_start = Carbon::parse($start)->timestamp;

                $timestamp_end = Carbon::parse($end)->timestamp;

                $v = $timestamp_end - $timestamp_start;

                if($v <= 0){

                    $errors = true;

                    $this->addError('start', "La période définie est incorrecte!");
                    $this->addError('end', "La période définie est incorrecte!");
                }
                else{
                    // return true;
                }

            }
            else{
                $errors = true;

                $this->addError('start', "L'année est incorrecte!");
                $this->addError('end', "L'année est incorrecte!");

            }
        }

        $this->hasErrorsHere = $errors;


    }


    public function submit()
    {

        if($this->classe){

            


        }

    }
}
