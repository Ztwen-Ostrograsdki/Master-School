<?php

namespace App\Http\Livewire;

use App\Events\InitiateClassePupilsMatriculeUpdateEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use Livewire\Component;

class UpdateClassePupilsLTPKMatricule extends Component
{
    protected $listeners = ['UpdateClassePupilsLTPKMatriculeLiveEvent' => 'openModal'];


    public $classe_id;

    public $targeted_pupil;

    public $ltpk_matricule;

    public $matricule_data = [];

    public $olders = [];

    public $title = "Mise à jour des matricules des élèves de classe";

    public $classe;

    use ModelQueryTrait;


    protected $rules = ['ltpk_matricule' => 'required|string'];

    public function render()
    {
        $pupils = [];

        if($this->classe){

            $pupils = $this->classe->getNotAbandonnedPupils();

        }

        return view('livewire.update-classe-pupils-l-t-p-k-matricule', compact('pupils'));
    }


    public function openModal(int $classe_id)
    {

        if($classe_id){

            $school_year_model = $this->getSchoolYear();

            $classe = $school_year_model->findClasse($classe_id);

            $this->classe = $classe;

            $user = auth()->user();

            $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);

            if($classe){

                if($not_secure || ($user->isAdminAs('master'))){

                    $this->classe_id = $classe_id;

                    $pupils = $classe->getNotAbandonnedPupils();

                    foreach($pupils as $p){

                        // $this->matricule_data[$p->id] = $p->ltpk_matricule;

                        $this->olders[$p->id] = $p->ltpk_matricule;

                    }

                    $this->dispatchBrowserEvent('modal-updateClassePupilsLTPKMatricule');

                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas mettre à jour les données!", 'type' => 'warning']);

                }
            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VIDE', 'message' => "Vous ne pouvez pas mettre à jour les données: la classe est vide!", 'type' => 'warning']);


            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Veuillez sélectionner une classe, un apprenant et une matière valides", 'type' => 'warning']);

        }
    }


    public function submit()
    {
        $matricule_data = $this->matricule_data;

        if($matricule_data && $this->ltpk_matricule == null){

            $user = auth()->user();

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($this->classe_id);

            if(auth()->user()->isAdminAs('master')){
                
                if($not_secure){

                    if($matricule_data !== []){

                        InitiateClassePupilsMatriculeUpdateEvent::dispatch($matricule_data, $user);

                        $this->dispatchBrowserEvent('hide-form');

                        $this->reset('matricule_data', 'classe', 'targeted_pupil', 'ltpk_matricule');

                    }
                   
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ACCES REFUSE', 'message' => "Vous ne pouvez effectuer une telle requête!", 'type' => 'error']);

                }

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'SEMESTRE DEJA FERME OU INDISPONIBLE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment, il est possible que le semestre ait déjà été cloturé (arrêt des notes) ou qu'il n'ait pas encore démarré ou que le programme n'est pas encore été pré-défini!", 'type' => 'info']);

            }
        }

    }


    public function pushIntoMatriculeData($pupil_id)
    {
        $this->reset('targeted_pupil');

        $matricule_data = $this->matricule_data;

        $error = false;

        if($this->ltpk_matricule){

            if(!$error){

                if(isset($matricule_data[$pupil_id]) && array_key_exists($pupil_id, $matricule_data)){

                    unset($matricule_data[$pupil_id]);

                    $matricule_data[$pupil_id] = $this->ltpk_matricule;

                }
                else{

                    $matricule_data[$pupil_id] = $this->ltpk_matricule;
                        
                }

                $this->matricule_data = $matricule_data;

                $this->reset('ltpk_matricule');

            }
            else{

                return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les matricule doivent être des valeurs numériques compris entre réglémentaires!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'MATRICULES VIDES', 'message' => "Veuillez insérer des notes d'abord!", 'type' => 'warning']);
        }


    }


    public function editMatriculeData($pupil_id)
    {
        $this->targeted_pupil = $pupil_id;

        if(isset($this->matricule_data[$pupil_id])){

            $this->ltpk_matricule = $this->matricule_data[$pupil_id];

        }
        else{

            $this->ltpk_matricule = $this->olders[$pupil_id];

        }
    }

    public function retrievedPupilFromMatriculeData($pupil_id)
    {
        $matricule_data = $this->matricule_data;

        $this->targeted_pupil 
                            ? (
                                $pupil_id == $this->targeted_pupil 
                                ? $this->reset('targeted_pupil') 
                                : $this->targeted_pupil = $this->targeted_pupil
                               ) 
                            : $this->reset('targeted_pupil');

        unset($matricule_data[$pupil_id]);

        $this->reset('ltpk_matricule');

        $this->matricule_data = $matricule_data;

    }

    /**
     * To clean the last inserting marks matricule_data
     */
    public function toback()
    {
        $this->resetErrorBag();

        $this->reset('ltpk_matricule', 'targeted_pupil');
    }

    /**
     * To clean all marks insert matricule_data
     */
    public function flushMatriculeDataTabs()
    {
        $this->reset('ltpk_matricule', 'targeted_pupil');
    }
}

