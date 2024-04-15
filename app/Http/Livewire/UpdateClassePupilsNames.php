<?php

namespace App\Http\Livewire;

use App\Events\InitiateClassePupilsNamesUpdateEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Livewire\Component;

class UpdateClassePupilsNames extends Component
{

    protected $listeners = ['UpdateClassePupilsNamesLiveEvent' => 'openModal'];


    public $classe_id;

    public $targeted_pupil;

    public $upd_firstname;

    public $upd_lastName;

    public $names_data = [];

    public $olders = [];

    public $title = "Mise à jour des noms et prénoms des élèves";

    public $classe;

    use ModelQueryTrait;


    protected $rules = ['upd_lastName' => 'required|string'];

    public function render()
    {
        $pupils = [];

        if($this->classe){

            $pupils = $this->classe->getPupils();

        }

        return view('livewire.update-classe-pupils-names', compact('pupils'));
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

                    $pupils = $classe->getPupils();

                    foreach($pupils as $p){

                        $this->olders[$p->id] = $p->upd_lastName;

                    }

                    $this->dispatchBrowserEvent('modal-updateClassePupilsNames');

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
        $names_data = $this->names_data;

        if($names_data && $this->upd_lastName == null){

            $user = auth()->user();

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($this->classe_id);

            if(auth()->user()->isAdminAs('master')){
                
                if($not_secure){

                    if($names_data !== []){

                        InitiateClassePupilsNamesUpdateEvent::dispatch($names_data, $user);

                        $this->dispatchBrowserEvent('hide-form');

                        $this->reset('names_data', 'classe', 'targeted_pupil', 'upd_lastName');

                    }
                   
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ACCES REFUSE', 'message' => "Vous ne pouvez effectuer une telle requête!", 'type' => 'error']);

                }

            }
            
        }

    }


    public function pushIntoNamesData($pupil_id)
    {
        $this->reset('targeted_pupil');

        $names_data = $this->names_data;

        ucwords(trim($this->upd_lastName));

        $error = false;

        if($this->upd_lastName){

            if(!$error){

                if(isset($names_data[$pupil_id]) && array_key_exists($pupil_id, $names_data)){

                    unset($names_data[$pupil_id]);

                    $names_data[$pupil_id] = $this->upd_lastName;

                }
                else{

                    $names_data[$pupil_id] = ucwords(trim($this->upd_lastName));
                        
                }

                $this->names_data = $names_data;

                $this->reset('upd_lastName');

            }
            else{

                return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les noms doivent être des caractères alphabétiques!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'NOMS VIDES', 'message' => "Veuillez insérer des noms d'abord!", 'type' => 'warning']);
        }


    }


    public function editNamesData($pupil_id)
    {
        $this->targeted_pupil = $pupil_id;

        if(isset($this->names_data[$pupil_id])){

            $this->upd_lastName = $this->names_data[$pupil_id];

        }
        else{

            $this->upd_lastName = $this->olders[$pupil_id];

        }
    }

    public function retrievedPupilFromNamesData($pupil_id)
    {
        $names_data = $this->names_data;

        $this->targeted_pupil 
                            ? (
                                $pupil_id == $this->targeted_pupil 
                                ? $this->reset('targeted_pupil') 
                                : $this->targeted_pupil = $this->targeted_pupil
                               ) 
                            : $this->reset('targeted_pupil');

        unset($names_data[$pupil_id]);

        $this->reset('upd_lastName');

        $this->names_data = $names_data;

    }

    /**
     * To clean the last inserting marks names_data
     */
    public function toback()
    {
        $this->resetErrorBag();

        $this->reset('upd_lastName', 'targeted_pupil');
    }

    /**
     * To clean all marks insert names_data
     */
    public function flushNamesDataTabs()
    {
        $this->reset('upd_lastName', 'targeted_pupil');
    }
}

