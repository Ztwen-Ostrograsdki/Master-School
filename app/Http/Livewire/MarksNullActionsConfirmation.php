<?php

namespace App\Http\Livewire;

use App\Events\MarksNullActionsEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\Subject;
use Livewire\Component;

class MarksNullActionsConfirmation extends Component
{

    protected $listeners = ['MarksNullActionsConfirmationEvent' => 'openModal'];

    use ModelQueryTrait;


    public $title = "Vous êtes sur le point de supprimer des notes!";

    private $actions = ['delete' => 'dl', 'activate' => 'a', 'desactivate' => 'd', 'normalize|standardize' => 's'];

    public $action = 'dl';

    public $semestre;

    public $subject_id;

    public $subject;

    public $pupil_id;

    public $classe;

    public $pupil;

    public $classe_id;



    public function render()
    {
        return view('livewire.marks-null-actions-confirmation');
    }


    public function openModal($action = 'dl', $classe_id, $semestre, $subject_id, $pupil_id = null)
    {
        $classe = Classe::find($classe_id);

        $subject = Subject::find($subject_id);

        if($classe && $subject){

            $this->classe = $classe;

            $this->subject = $subject;

            $this->action = $action;

            $this->semestre = $semestre;

            $this->subject_id = $subject_id;

            $this->classe_id = $classe_id;

            $this->pupil_id = $pupil_id;

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


            $this->dispatchBrowserEvent('modal-marksNullActionsConfirmation');

        }

    }


    public function confirm()
    {

        $action = $this->action;

        $classe = $this->classe;

        $pupil = $this->pupil;

        $subject = $this->subject;

        $school_year_model = $this->getSchoolYear();

        $semestre = $this->semestre;

        $user = auth()->user();

        MarksNullActionsEvent::dispatch($action, $classe, $semestre, $subject, $school_year_model, $pupil, $user);

        $this->dispatchBrowserEvent('hide-form');

        $this->dispatchBrowserEvent('Toast', ['title' => "OPERATION LANCEE", 'message' => "L'opération a bien été lancé avec succès!", 'type' => 'success']);

        $this->reset('classe_id', 'classe', 'pupil', 'pupil_id', 'subject_id', 'subject', 'action', 'title');

    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');

        $this->reset('classe_id', 'classe', 'pupil', 'pupil_id', 'subject_id', 'subject', 'action', 'title');

        $this->dispatchBrowserEvent('Toast', ['title' => "OPERATION ANNULEE", 'message' => "L'opération a bien été annulée avec succès!", 'type' => 'success']);
    }


    public function setTitle($pupil = null)
    {
        $action = $this->action;

        $classe = $this->classe;

        $subject = $this->subject;

        if($pupil){

            if($action == 'dl'){

                $title = "Vous êtes sur le point de supprimer les notes zéro de la matière " . $subject->name . " de l'apprenant " . $pupil->getName();

            }
            elseif($action == 'a'){

                $title = "Vous êtes sur le point de d'activer les notes zéro de la matière " . $subject->name . " de l'apprenant" . $pupil->getName();

            }
            elseif($action == 'd'){

                $title = "Vous êtes sur le point de désactiver les notes zéro de la matière " . $subject->name . " de l'apprenant" . $pupil->getName() . ". Ces notes nulles ne seront donc pas prises en comptes et seront muettes";

            }
            elseif($action == 's'){

                $title = "Vous êtes sur le point de désactiver les notes zéro de la matière " . $subject->name . " de l'apprenant" . $pupil->getName() . ". Ces notes nulles seront donc considérées comme toutes notes";

            }
        }
        else{

            if($action == 'dl'){

                $title = "Vous êtes sur le point de supprimer les notes zéro de la matière " . $subject->name ." de la classe de " . $classe->name;

            }
            elseif($action == 'a'){

                $title = "Vous êtes sur le point de rendre obligatoires (elles seront considérées comme étant des notes obligatoires pour le calcule des moyennes) les notes zéro de la matière " . $subject->name ." de la classe de " . $classe->name;

            }
            elseif($action == 'd'){

                $title = "Vous êtes sur le point de désactiver les notes zéro de la matière " . $subject->name ." de la classe de " . $classe->name . ". Ces notes nulles ne seront donc pas prises en comptes et seront muettes";

            }
            elseif($action == 's'){

                $title = "Vous êtes sur le point de normaliser les notes zéro de la matière " . $subject->name ." de la classe de " . $classe->name . ". Ces notes nulles seront donc considérées comme toutes notes";

            }
        }

        $this->title = $title;
    }
}
