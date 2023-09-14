<?php

namespace App\Http\Livewire;

use App\Events\MarksRestorationEvent;
use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Livewire\Component;

class MarksRestorerComponent extends Component
{
    protected $listeners = ['ThrowMarksRestorationLiveEvent' => 'openModal'];

    public $classe_id;

    public $years = [];

    public $targeted_pupil;

    public $school_year_model;

    public $school_year_id;

    public $subject_id;

    public $pupil_id;

    public $pupil;

    public $marks = [];

    public $epe_marks;

    public $start;

    public $end;

    public $dev_marks;

    public $type = 'epe';

    public $semestre_id = 1;

    public $subject;

    public $title = "Restauration des notes de classe";

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
            'participation' => 'Participations',
            'bonus' => 'Bonus',
            'sanction' => 'Sanctions',

        ];

        $pupils = [];

        $period_string = null;

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

        return view('livewire.marks-restorer-component', compact('semestres', 'school_years', 'types_of_marks', 'subjects', 'pupils', 'types_of_marks', 'period_string'));
    }


    public function updatedSubjectId($subject_id)
    {
        if($subject_id == 'all'){

            $this->subject = 'all';

        }
        else{

            $subject = Subject::find($subject_id);

            $this->subject = $subject;

        }

    }

    public function updatedClasseId($classe_id)
    {
        $classe = Classe::find($classe_id);

        $this->classe = $classe;

    }


    public function openModal($classe_id, $school_year, $semestre, $subject, $type = null, $pupil_id = null)
    {
        $auth = auth()->user();


        if($auth->isAdminAs('master') || $auth->teacher){

            $school_year_model = $this->getSchoolYear($school_year);

            $classe = $school_year_model->findClasse($classe_id);

            $this->years = explode(' - ', $school_year_model->school_year);

            if($classe){

                $not_secure = $auth->ensureThatTeacherCanAccessToClass($classe_id);

                if($not_secure){

                    $this->classe = $classe;

                    $this->classe_id = $classe->id;

                    $this->pupil_id = $pupil_id;

                    $this->school_year_model = $school_year_model;

                    $this->school_year_id = $school_year_model->id;

                    $teacher = auth()->user()->teacher;

                    $subject = $teacher->speciality();

                    $this->subject = $subject;

                    $this->type = $type;

                    $this->subject_id = $subject->id;

                    $this->end = Carbon::today()->toDateString();

                    $this->semestre_id = session('semestre_selected');

                    if($pupil_id){

                        $pupil = $school_year_model->findPupil($pupil_id);

                        if($pupil){

                            $this->pupil = $pupil;

                            $this->pupil_id = $pupil_id;

                            $this->title = "Restauration des notes de classe de l'apprenant " . $pupil->getName();

                        }

                    }

                    $this->dispatchBrowserEvent('modal-classeMarksRestorer');


                }
                else{

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ACCES REFUSE!', 'message' => "Vous nêtes pas authorisé à accéder à cette page! ", 'type' => 'warning']);

                }

            }

        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ACCES NON AUTHORISE', 'message' => "Vous nêtes pas authorisé à accéder à cette page! ", 'type' => 'warning']);

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

            $classe = $this->classe;

            $school_year_model = $this->school_year_model;

            $semestre = $this->semestre_id;

            $subject = $this->subject;

            $type = $this->type;

            $start = $this->start;

            $end = $this->end;

            $pupil_id = $this->pupil_id;

            $this->dispatchBrowserEvent('hide-form');

            MarksRestorationEvent::dispatch($classe->id, $school_year_model->id, $semestre, $subject, $type, $start, $end, $pupil_id);

        }
    }


}
