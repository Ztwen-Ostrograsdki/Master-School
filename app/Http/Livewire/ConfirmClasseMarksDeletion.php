<?php

namespace App\Http\Livewire;

use App\Events\ClasseMarksDeletionCreatedEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\Subject;
use Livewire\Component;

class ConfirmClasseMarksDeletion extends Component
{
    use ModelQueryTrait;

    protected $listeners = ['ConfirmClasseMarksDeletionLiveEvent' => 'openModal'];

    public $pupil;

    public $pupil_id;

    public $school_year_model;

    public $start;

    public $end;

    public $type = 'epe';

    public $semestre;

    public $subject;

    public $title = "Confirmation suppression des notes de classe";

    public $classe;

    public $semestre_type = 'Semestre';

    public function render()
    {
        return view('livewire.confirm-classe-marks-deletion');
    }



    public function confirmed()
    {
        $this->dispatchBrowserEvent('hide-form');

        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'SUPPRESSION LANCEE AVEC SUCCES', 'message' => "Le processus de suppression des notes a été lancé et s'exécutera en arrière plan! ", 'type' => 'success']);

        $school_year_model = $this->getSchoolYear($this->school_year);

        $user = auth()->user();

        ClasseMarksDeletionCreatedEvent::dispatch($user, $this->classe, $school_year_model, $this->semestre, $this->subject, $this->type, $this->start, $this->end, $this->pupil_id);
    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');

        $this->reset('classe', 'school_year_model', 'semestre', 'subject', 'start', 'end', 'type');

        $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS ANNULE AVEC SUCCES', 'message' => "Le processus de suppression des notes a été annulé! ", 'type' => 'info']);

    }

    public function openModal($classe_id, $school_year, $semestre, $subject_id, $type, $start, $end, $pupil_id = null)
    {
        $classe = Classe::find($classe_id);

        if($subject_id && is_numeric($subject_id)){

            $subject = Subject::find($subject_id);

        }
        else{

            if(is_array($subject_id)){

                $subject = Subject::find($subject_id['id']);
            }
            else{

                $subject = $subject_id;

            }

        }

        if($classe){

            $this->classe = $classe;

            $this->school_year = $school_year;

            $this->semestre = $semestre;

            $this->subject = $subject;

            $this->type = $type;

            $this->end = $end;

            $this->start = $start;

            if($pupil_id){

                $pupil = Pupil::find($pupil_id);

                if($pupil){

                    $this->pupil = $pupil;

                    $this->pupil_id = $pupil_id;

                    $this->title = "Confirmation de suppression des notes de classe de l'apprenant " . $pupil->getName();

                }

            }

            $this->dispatchBrowserEvent('modal-ConfirmClasseMarksDeletion');

        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE INTROUVABLE', 'message' => "Le processus de suppression des notes a échoué! ", 'type' => 'error']);

        }

    }
}
