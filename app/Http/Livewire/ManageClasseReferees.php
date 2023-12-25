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

    public $counter = 0;

    protected $rules = [
        
    ];

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


    public function openModal($classe_id, $school_year = null)
    {
        $this->school_year_model = $this->getSchoolYear($school_year);

        $this->school_year_id = $this->school_year_model->id;

        if($classe_id){

            $classe = $this->school_year_model->findClasse($classe_id);

            $this->classe = $classe;

            $this->title = "Définition du PP et des RESPO de la " . $classe->name;

            $this->classe_id = $this->classe->id;

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
    }


    public function submit()
    {
        $principal = $this->classe->hasPrincipal();

        $school_year_model = $this->getSchoolYear();

        $this->resetErrorBag();

        $make = false;

        $teacher = [];

        $pupils = [];

        if($this->teacher_id){

            if($principal){

                // $make = $principal->update(['teacher_id' => $this->teacher_id]);


                $teacher = ['create' => null, 'update' => $this->teacher_id];


                $make = true;
            }
            else{

                $teacher = ['create' => $this->teacher_id, 'update' => null];


                $make = true;

                // $make = PrincipalTeacher::create(['teacher_id' => $this->teacher_id, 'classe_id' => $this->classe_id, 'school_year_id' => $this->school_year_id]);
            }
            
            if(!$this->respo2_id && !$this->respo1_id){

                // if($make){

                //     $this->resetErrorBag();

                //     $this->dispatchBrowserEvent('hide-form');

                //     $this->reset('respo2_id', 'respo2_id', 'classe_id', 'classe');

                //     $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION EFFECTUEE', 'message' => "Mise à jour lancée avec succès!", 'type' => 'success']);
                // }
                // else{

                //     $this->addError('teacher_id', 'Vérifiez la valeur de ce champ!');

                //     $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE SERVEUR', 'message' => "Une erreure inconnue a été decellée", 'type' => 'warning']);
                // }
            }
        }

        $model = $this->classe->currentRespo();

        $make_for_respo = false;

        if(!$model){


            $this->classe->classeResponsiblesInitiator($school_year_model->id);

        }

        $model = $this->classe->currentRespo();

        if($model){

            if($this->respo1_id){

                if($this->respo2_id !== null){

                    if($this->respo1_id !== $this->respo2_id){

                        $pupils = ['respo1' => $this->respo1_id, 'respo2' => $this->respo2_id];

                        $make_for_respo = true;

                        // $update = $model->update(['respo_1' => $this->respo1_id, 'respo_2' => $this->respo2_id]);

                        
                    }
                    else{

                        $this->addError('respo1_id', 'Role dupliqué');

                        $this->addError('respo2_id', 'Role dupliqué');

                        $this->dispatchBrowserEvent('Toast', ['title' => 'ROLE DUPLIQUE', 'message' => "Le même apprenant ne peut avoir plus d'une fonction!", 'type' => 'error']);

                        return false;
                    }
                }
                else{

                    if($model->respo_2 !== $this->respo1_id){

                        // $update = $model->update(['respo_1' => $this->respo1_id]);

                        $pupils = ['respo1' => $this->respo1_id, 'respo2' => null];

                        $make_for_respo = true;

                    }
                    else{

                        $this->addError('respo1_id', 'Role dupliqué');

                        $this->addError('respo2_id', 'Role dupliqué');

                        $this->dispatchBrowserEvent('Toast', ['title' => 'ROLE DUPLIQUE', 'message' => "Cet apprenant ne peut avoir plus d'une fonction, il est déja second responsable! Veuillez lui retier cette fonction en premier lieu", 'type' => 'warning']);

                        return false;

                    }
                }

            }
            elseif($this->respo2_id){

                if($model->respo_1 !== $this->respo2_id){

                    $pupils = ['respo1' => null, 'respo2' => $this->respo2_id];

                    $make_for_respo = true;

                    // $update = $model->update(['respo_2' => $this->respo2_id]);

                    
                }
                else{

                    $this->addError('respo2_id', 'Role dupliqué');

                    $this->dispatchBrowserEvent('Toast', ['title' => 'ROLE DUPLIQUE', 'message' => "Cet apprenant ne peut avoir plus d'une fonction, il est déja premier responsable! Veuillez lui retier cette fonction en premier lieu", 'type' => 'warning']);

                    return false;

                }
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE SERVEUR', 'message' => "La requête est inconnue", 'type' => 'warning']);
        }


        if($pupils || $teacher){

            $data = ['principal' => $teacher, 'respos' => $pupils];
            
            $user = auth()->user();

            ClasseRefereesManagerEvent::dispatch($this->classe, $data, $school_year_model, $user);

            $this->resetErrorBag();

            $this->dispatchBrowserEvent('hide-form');

            $this->reset('respo2_id', 'respo2_id', 'classe_id', 'classe');

            $this->dispatchBrowserEvent('Toast', ['title' => 'OPERARION LANCEE', 'message' => "Mise à jour lancée avec succès!", 'type' => 'success']);

        }

    }

   
   
}
