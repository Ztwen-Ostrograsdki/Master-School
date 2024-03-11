<?php

namespace App\Http\Livewire;

use App\Events\ClasseRefereesManagerEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\PrincipalTeacher;
use App\Models\Responsible;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageClasseReferees extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'ManageClasseRefereesLiveEvent' => 'openModal', 
    ];

    public $classe_id;

    public $title = "";

    public $classe;

    public $pupils1 = [];

    public $pupils2 = [];

    public $school_year_model;

    public $school_year_id;

    public $teacher_id;

    public $respo1_id;

    public $respo2_id;

    public $for_respo1 = false;

    public $for_respo2 = false;

    public $for_pp = false;

    public $counter = 0;

    protected $targets = ['r1', 'r2', 'pp'];

    public $target = null;

    public function render()
    {

        $school_years = SchoolYear::all();

        $teachers = [];

        $pupils = [];

        if($this->classe){

            $teachers = $this->classe->getClasseCurrentTeachers();

            $pupils = $this->classe->getClassePupils();
        }
        return view('livewire.manage-classe-referees', compact('school_years', 'pupils', 'teachers'));
    }


    public function openModal($classe_id, $target = null, $school_year = null)
    {
        $this->resetErrorBag();

        $this->reset('classe_id', 'classe', 'for_pp', 'for_respo2', 'for_respo1', 'target', 'respo2_id', 'respo1_id', 'teacher_id', 'title');

        $this->school_year_model = $this->getSchoolYear($school_year);

        $this->school_year_id = $this->school_year_model->id;

        if($classe_id){

            $classe = $this->school_year_model->findClasse($classe_id);

            if($target){

                $model = $classe->currentRespo();

                if(!$model){

                    $model = $classe->classeResponsiblesInitiator($this->school_year_model->id);

                }

                if($model){

                    $this->target = $target;

                    $this->classe = $classe;

                    $this->classe_id = $this->classe->id;

                    $this->setTitle($target);

                    if($this->classe->pupil_respo1()){

                        $this->respo1_id = $this->classe->pupil_respo1()->id;
                    }
                    else{

                        $this->respo1_id = null;
                    }

                    if($this->classe->pupil_respo2()){

                        $this->respo2_id = $this->classe->pupil_respo2()->id;
                    }
                    else{

                        $this->respo2_id = null;
                    }

                    if($this->classe->hasPrincipal()){

                        $this->teacher_id = $this->classe->hasPrincipal()->teacher_id;
                    }
                    else{

                        $this->teacher_id = null;
                    }

                    $this->dispatchBrowserEvent('modal-manageClasseReferees');

                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE SERVEUR', 'message' => "Une erreure s'est produite lors de l'initiation des données en basse de données!", 'type' => 'error']);

                }

            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'CIBLE OBJET INDEFINI', 'message' => "Veuillez choisir la cible que vous souhaitez éditer!", 'type' => 'error']);
            }
        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE INDEFINIE', 'message' => "Veuillez choisir la classe dont vous souhaitez éditer des données d'administration!", 'type' => 'error']);

        } 
    }



    public function submit()
    {
        $target = $this->target;

        $for_respo1 = $this->for_respo1;

        $for_respo2 = $this->for_respo2;

        $for_pp = $this->for_pp;

        $classe = $this->classe;

        $make = true;

        if($for_respo1){

            if($this->respo1_id == 'remove'){

                $make = $this->removeRespo1($classe);

            }
            else{

                $make = $this->definedRespo1($classe);

            }

            if($make){

                $this->emit('ClassesUpdatedLiveEvent');

                $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION EFFECTUEE', 'message' => "Mise à jour du premier responsable de la classe effectuée avec succès!", 'type' => 'success']);

                $this->resetErrorBag();

                $this->dispatchBrowserEvent('hide-form');

                $this->reset('classe_id', 'classe', 'for_pp', 'for_respo2', 'for_respo1', 'target', 'respo2_id', 'respo1_id', 'teacher_id', 'title');
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION ECHOUEE', 'message' => "La mise à jour n'a pas pu s'est déroulée: une erreure est survenue, Veuillez réessayer!", 'type' => 'error']);
            }

        }
        elseif($for_respo2){

            if($this->respo1_id == 'remove'){

                $make = $this->removeRespo2($classe);

            }
            else{

                $make = $this->definedRespo2($classe);
            }

            if($make){

                $this->emit('ClassesUpdatedLiveEvent');

                $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION EFFECTUEE', 'message' => "Mise à jour du second responsable de la classe effectuée avec succès!", 'type' => 'success']);

                $this->resetErrorBag();

                $this->dispatchBrowserEvent('hide-form');

                $this->reset('classe_id', 'classe', 'for_pp', 'for_respo2', 'for_respo1', 'target', 'respo2_id', 'respo1_id', 'teacher_id', 'title');
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION ECHOUEE', 'message' => "La mise à jour n'a pas pu s'est déroulée: une erreure est survenue, Veuillez réessayer!", 'type' => 'error']);
            }

        }
        elseif($for_pp){

            if($this->teacher_id && $this->teacher_id == 'remove'){

                $make = $this->removeReferee($classe);
            }
            else{

                $make = $this->definedPP($classe);

            }

            if($make){

                $this->emit('ClassesUpdatedLiveEvent');

                $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION EFFECTUEE', 'message' => "Mise à jour du PP de la classe effectuée avec succès!", 'type' => 'success']);

                $this->resetErrorBag();

                $this->dispatchBrowserEvent('hide-form');

                $this->reset('classe_id', 'classe', 'for_pp', 'for_respo2', 'for_respo1', 'target', 'respo2_id', 'respo1_id', 'teacher_id', 'title');
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION ECHOUEE', 'message' => "La mise à jour n'a pas pu s'est déroulée: une erreure est survenue, Veuillez réessayer!", 'type' => 'error']);
            }

        }

    }


    /**
     * To set the value of the title
     * @param string $target 
     */
    public function setTitle($target)
    {

        if($target == 'r1'){

            $this->for_respo1 = true;

            $this->reset('for_respo2', 'for_pp');

            $this->title = "Définition du PREMIER RESPO de la " . $this->classe->name;

        }
        elseif($target == 'r2'){

            $this->for_respo2 = true;

            $this->reset('for_respo1', 'for_pp');

            $this->title = "Définition du SECOND RESPO de la " . $this->classe->name;

        }
        elseif($target == 'pp'){

            $this->for_pp = true;

            $this->reset('for_respo1', 'for_respo2');

            $this->title = "Définition du PROF PRINCIPAL de la " . $this->classe->name;

        }
    }

    public function removeReferee($classe)
    {
        $this->validate(['teacher_id' => 'required|string']);

        $make = true;

        if($this->teacher_id && $this->teacher_id == 'remove'){

            $principalModel = $classe->getCurrentPrincipalAsModel();

            if($principalModel && $principalModel->teacher_id){

                $make = $principalModel->delete();

                return $make;

            }

        }
        else{

            $this->addError('teacher_id', 'Valeur du champ est incorrecte');

        }
    }

    public function removeRespo1($classe)
    {
        $this->validate(['respo1_id' => 'required|string']);

        $make = true;

        if($this->respo1_id && $this->respo1_id == 'remove'){

            $respo_model = $classe->currentRespo();

            if($respo_model && $respo_model->respo_1){

                $make = $respo_model->update(['respo_1' => null]);

                return $make;

            }

        }
        else{

            $this->addError('respo1_id', 'Valeur du champ est incorrecte');

        }
    }


    public function removeRespo2($classe)
    {
        $this->validate(['respo2_id' => 'required|string']);

        $make = true;

        if($this->respo2_id && $this->respo2_id == 'remove'){

            $respo_model = $classe->currentRespo();

            if($respo_model && $respo_model->respo_2){

                $make = $respo_model->update(['respo_2' => null]);

                return $make;

            }

        }
        else{

            $this->addError('respo2_id', 'Valeur du champ est incorrecte');

        }
    }

    public function definedRespo1($classe)
    {
        $this->validate(['respo1_id' => 'required|numeric']);

        $current_respo1 = $classe->pupil_respo1();

        $respo_model = $classe->currentRespo();

        $make = true;

        if($current_respo1){

            if($current_respo1->id !== $this->respo1_id){

                $current_respo2 = $classe->pupil_respo2();

                if(!$current_respo2){

                    $make = $respo_model->update(['respo_1' => $this->respo1_id]);

                }
                else{

                    if($current_respo2->id !== $this->respo1_id){

                        $make = $respo_model->update(['respo_1' => $this->respo1_id]);

                    }
                    else{

                        $this->addError('respo1_id', 'Role dupliqué');


                        $this->dispatchBrowserEvent('Toast', ['title' => 'ROLE DUPLIQUE', 'message' => "Cet apprenant ne peut avoir plus d'une fonction, il est déja second responsable! Veuillez lui retier cette fonction en premier lieu avant de le définir comme premier responsabale de la classe", 'type' => 'warning']);

                    }

                }

            }
            else{

                //DO ANYTHINK BECAUSE THE RESPO WASNT CHANGED

            }

        }
        else{

            $make = $respo_model->update(['respo_1' => $this->respo1_id]);
        }

        return $make;
    }

    public function definedRespo2($classe)
    {
        $this->validate(['respo2_id' => 'required|numeric']);

        $current_respo2 = $classe->pupil_respo2();

        $respo_model = $classe->currentRespo();

        $make = true;

        if($current_respo2){

            if($current_respo2->id !== $this->respo2_id){

                $current_respo1 = $classe->pupil_respo1();

                if(!$current_respo1){

                    $make = $respo_model->update(['respo_2' => $this->respo2_id]);

                }
                else{

                    if($current_respo1->id !== $this->respo2_id){

                        $make = $respo_model->update(['respo_2' => $this->respo2_id]);

                    }
                    else{

                        $this->addError('respo2_id', 'Role dupliqué');


                        $this->dispatchBrowserEvent('Toast', ['title' => 'ROLE DUPLIQUE', 'message' => "Cet apprenant ne peut avoir plus d'une fonction, il est déja le premier responsable! Veuillez lui retier cette fonction en premier lieu avant de le définir comme second responsabale de la classe", 'type' => 'warning']);

                    }

                }

            }
            else{

                //DO ANYTHINK BECAUSE THE RESPO WASNT CHANGED

            }

        }
        else{

            $make = $respo_model->update(['respo_2' => $this->respo2_id]);
        }

        return $make;
    }


    public function definedPP($classe)
    {
        $make = true;

        $principal = $classe->currentPrincipal();

        if($principal){

            if($principal !== $this->teacher_id){

                $make = $principal->update(['teacher_id' => $this->teacher_id]);

            }
            else{

                //DO ANYTHINK BECAUSE THE PRINCIPAL WASNT CHANGED

            }

        }
        else{

            $make = PrincipalTeacher::create(['teacher_id' => $this->teacher_id, 'classe_id' => $classe->id, 'school_year_id' => $this->school_year_id]);
        }


        return $make;

    }

   
}
