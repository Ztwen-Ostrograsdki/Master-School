<?php

namespace App\Http\Livewire;

use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Events\ThrowClasseMarksConvertionEvent;
use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Livewire\Component;

class ClasseMarksConvertionConfirmation extends Component
{
    protected $listeners = ['ThrowClasseMarksConverter' => 'openModal'];

    protected $rules = ['convertion_type' => 'required|string'];

    public $classe_id;

    public $targeted_pupil;

    public $school_year_model;

    public $subject_id;

    public $pupil_id;

    public $pupil;

    public $semestre_id = 1;

    public $semestre = 1;

    public $subject;

    public $title = "Conversion des notes de classe";

    public $convertion_type_message = "Conversion des notes d'Interrogations en notes de Participations";

    public $convertion_type = 'epe-to-participation';

    public $classe;

    public $semestre_type = 'Semestre';

    public $hasErrorsHere = false;


    use ModelQueryTrait;

    use DateFormattor;


    public function render()
    {

        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $this->semestre_type = 'Trimestre';
        }


       return view('livewire.classe-marks-convertion-confirmation');
    }


    public function updatedConvertionType($convertion_type)
    {

        $this->convertion_type = $convertion_type;

        $this->setConvertionMessage();

    }


    public function openModal($classe_id, $convertion_type = 'epe-to-participation', $semestre = null, $school_year_id = null, $pupil_id = null)
    {
        $auth = auth()->user();

        if($auth->isAdminAs('master') || $auth->teacher){

            $school_year_model = $this->getSchoolYear($school_year_id);

            $classe = $school_year_model->findClasse($classe_id);

            if($classe){

                $not_secure = $auth->ensureThatTeacherCanAccessToClass($classe_id);

                if($not_secure){

                    $this->classe = $classe;

                    $this->classe_id = $classe->id;

                    $this->pupil_id = $pupil_id;

                    $this->school_year_model = $school_year_model;

                    $this->school_year_id = $school_year_model->id;

                    $teacher = auth()->user()->teacher;

                    if($teacher){

                        $subject = $teacher->speciality();

                        $this->subject = $subject;

                        $this->subject_id = $subject->id;

                    }


                    $this->semestre_id = session('semestre_selected');

                    $this->setConvertionMessage();

                    if($pupil_id){

                        $pupil = $school_year_model->findPupil($pupil_id);

                        if($pupil){

                            $this->pupil = $pupil;

                            $this->pupil_id = $pupil_id;

                            $this->title = "Conversion des notes de classe de l'apprenant " . $pupil->getName();

                        }

                    }

                    $this->dispatchBrowserEvent('modal-classeMarksConvertion');


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


    public function setConvertionMessage($convertion_type = null)
    {

        $convertion_type = $this->convertion_type;

        if($convertion_type == 'epe-to-participation'){

            $message = "Conversion des notes d'Interrogations en notes de Participations";

        }
        elseif($convertion_type == 'participation-to-epe'){

            $message = "Conversion des notes de Participations en notes d'Interrogations";

        }
        else{

            $message = "Le type de conversion n'a pas été précisé!";

        }

        $this->convertion_type_message = $message;


    }

    public function confirmed()
    {

        if($this->classe){

            $classe = $this->classe;

            $school_year_model = $this->school_year_model;

            $semestre = $this->semestre_id;

            $subject = $this->subject;

            $pupil_id = $this->pupil_id;

            $convertion_type = $this->convertion_type;

            $this->dispatchBrowserEvent('hide-form');

            $user = auth()->user();

            if($subject && $classe && $convertion_type && $semestre && $school_year_model){

                ThrowClasseMarksConvertionEvent::dispatch($classe, $convertion_type, $semestre, $school_year_model, $subject, $pupil_id, $user);

                $this->dispatchBrowserEvent('hide-form');

                $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS LANCE AVEC SUCCES', 'type' => 'success']);
            }

        }
    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');

        $this->reset('classe', 'classe_id', 'school_year_model', 'semestre', 'semestre_id', 'subject', 'convertion_type', 'pupil_id', 'subject_id');

        $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS ANNULE AVEC SUCCES', 'message' => "Le processus de conversion des notes a été annulé! ", 'type' => 'info']);

    }


}

