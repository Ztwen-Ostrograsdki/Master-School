<?php

namespace App\Http\Livewire;

use App\Events\AbsencesAndLatesDeleterEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\Subject;
use Livewire\Component;

class ResetAbsencesAndLatesConfirmation extends Component
{

    protected $listeners = ['ConfirmAbsencesAndLatesReset' => 'openModal'];

    use ModelQueryTrait;


    public $title = "Vous êtes sur le point de supprimer les absences|retards enregistrés!";

    public $target = 'absences';

    public $semestre;

    public $subject_id;

    public $subject;

    public $subjects = [];

    public $pupil_id;

    public $classe;

    public $pupil;

    public $classe_id;

    public function render()
    {
        return view('livewire.reset-absences-and-lates-confirmation');
    }



    public function openModal($target, $classe_id, $semestre, $subject_id, $pupil_id = null)
    {
        $classe = Classe::find($classe_id);

        $subject = Subject::find($subject_id);

        $user = auth()->user();

        if($classe && $subject){

            $this->classe = $classe;

            $this->subject = $subject;

            $this->target = $target;

            $this->semestre = $semestre;

            $this->subject_id = $subject_id;

            $this->classe_id = $classe_id;

            $this->pupil_id = $pupil_id;

            $this->subjects = $classe->subjects;

            if($pupil_id){

                $pupil = Pupil::find($pupil_id);

                if($pupil){

                    $this->pupil = $pupil;

                    $this->setTitle($pupil);

                }

            }
            else{

                $this->setTitle();

            }

            $this->dispatchBrowserEvent('modal-resetAbsencesAndLatesConfirmation');

        }

    }

    public function confirmForLates()
    {
        $this->confirm('lates');

    }

    public function confirmForAbsences()
    {
        $this->confirm('absences');

    }


    public function updatedSubjectId($subject_id)
    {
        if($subject_id && is_numeric($subject_id)){

            $subject = Subject::find($subject_id);

            if($subject){

                $this->subject = $subject;

                $this->setTitle($this->pupil_id);
            }

        }
        else{

            $this->subject_id = 'all';

            $this->subject = null;

            $this->setTitle($this->pupil_id);
        }
    }


    public function confirm($target)
    {

        $classe = $this->classe;

        $pupil = $this->pupil;

        $subject = $this->subject;

        $school_year_model = $this->getSchoolYear();

        $semestre = $this->semestre;

        $user = auth()->user();

        // AbsencesAndLatesDeleterEvent::dispatch($user, $classe, $semestre, $school_year_model, $subject_id, $pupil_id, $target);


        $this->dispatchBrowserEvent('hide-form');

        $this->dispatchBrowserEvent('Toast', ['title' => "OPERATION LANCEE", 'message' => "L'opération a bien été lancé avec succès!", 'type' => 'success']);

        $this->reset('classe_id', 'classe', 'pupil', 'pupil_id', 'subject_id', 'subject', 'target', 'title');
        
    }


    public function setTitle($pupil = null)
    {
        $target = $this->target;

        $classe = $this->classe;

        $subject = $this->subject;

        if($subject){

            $subject_name = ' en ' . $subject->name;

        }
        elseif($this->subject_id == "all"){

            $subject_name = " dans toutes les matières";

        }

        if($pupil){

            if($target == 'absences'){

                $title = "Vous êtes sur le point de supprimer toutes les absences enregistrées" . $subject_name . " de l'apprenant " . $pupil->getName();

            }
            elseif($target == 'lates'){

                $title = "Vous êtes sur le point de d'activer tous les retards enregistrés" . $subject_name . " de l'apprenant" . $pupil->getName();

            }
        }
        else{

            if($target == 'absences'){

                $title = "Vous êtes sur le point de supprimer toutes les absences enregistrées" . $subject_name ." de la classe de " . $classe->name;

            }
            elseif($target == 'lates'){

                $title = "Vous êtes sur le point de supprimer tous les retards enregistrés" . $subject_name ." de la classe de " . $classe->name;

            }
            
        }

        $this->title = $title;
    }

    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');

        $this->reset('classe_id', 'classe', 'pupil', 'pupil_id', 'subject_id', 'subject', 'target', 'title');

        $this->dispatchBrowserEvent('Toast', ['title' => "OPERATION ANNULEE", 'message' => "L'opération a bien été annulée avec succès!", 'type' => 'success']);
    }
}
