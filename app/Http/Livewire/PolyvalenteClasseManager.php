<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PolyvalenteClasseManager extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
    ];
    
    
    public $counter = 0;
    public $selected;
    public $selectedAction;
    public $checkeds = [];
    public $selecteds = [];
    public $activeData = [];
    public $download_pdf_z = false;
    public $search = null;

    public $pupilFirstName;
    public $pupilLastName;
    public $pupil_id;
    public $editingPupilName = false;

    public $levels = ['Secondaire' => 'secondary', 'Primaire' => 'primary'];


    public function mount($level)
    {
        if($level){
            $this->level = $this->levels[ucfirst($level)];
        }
        else{
            return abort(404);
        }

    }


    public function resetSearch()
    {
        $this->reset('search');
    }

    public function render()
    {
        $school_year_model = $this->getSchoolYear();
        $pupils = [];
        $classe = null;
        if($this->level){
            $level = Level::where('name', $this->level)->firstOrFail();
            $target = '%' . 'polyvalente' . '%';
            $classe = Classe::where('name', 'like', $target)->where('level_id', $level->id)->first();
            if($classe){
                if($this->search){
                    $pupils = $classe->getPupils($school_year_model->id, $this->search);
                }
                else{
                    $pupils = $classe->getPupils($school_year_model->id);
                }
            }
        }
        return view('livewire.polyvalente-classe-manager', compact('classe', 'pupils', 'school_year_model'));
    }

    public function deletePupil($pupil_id)
    {
        return false;
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $pupil->delete();
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "l'apprenant $pupil->name envoyé dans la corbeille!", 'type' => 'success']);
        $this->emit('classeUpdated');
        $this->emit('classePupilListUpdated');
    }


    public function reloadClasseDataOnSearch($value)
    {
        $this->search = $value;
    }

    public function updatedSearch($value)
    {
        $this->search = $value;
    }

    public function forceDeletePupil($pupil_id)
    {
        return false;
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            DB::transaction(function($e) use ($pupil){
                $school_year_model = $this->getSchoolYear();
                $marks = $pupil->marks;
                $classes = $pupil->classes;

                $pupil->marks()->each(function($mark) use ($school_year_model){
                    $school_year_model->marks()->detach($mark->id);
                    $mark->delete();
                });


                $pupil->classes()->each(function($classe) use ($pupil){
                    $classe->classePupils()->detach($pupil->id);
                });

                $pupil->related_marks()->each(function($r){
                    $r->delete();
                });

                $pupil->absences()->delete();
                $pupil->lates()->delete();
                $school_year_model->pupils()->detach($pupil->id);
                $pupil->forceDelete();

            });
            DB::afterCommit(function() use ($pupil){
                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "l'apprenant $pupil->name a été supprimé définitivement!", 'type' => 'success']);
                $this->emit('classeUpdated');
                $this->emit('classePupilListUpdated');
            });
        }
        
    }

   


    public function addNewPupilTo()
    {
        $school_year_model = $this->getSchoolYear();
        // $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        if(true){

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
        }

    } 

    public function changePupilSexe($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $sexe = $pupil->sexe;
            if($pupil->sexe == 'male'){
                $pupil->update(['sexe' => 'female']);
            }
            else{
                $pupil->update(['sexe' => 'male']);
            }
            $this->emit('classeUpdated');
            $this->emit('classePupilListUpdated');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Une ereure est survenue!", 'type' => 'error']);
        }
    } 

    public function editPupilName($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $this->pupil_id = $pupil->id;
            $this->pupilFirstName = $pupil->firstName;
            $this->pupilLastName = $pupil->lastName;
            $this->editingPupilName = true;
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "L'apprenant est introuvable!", 'type' => 'error']);
        }
    }
    public function cancelEditingPupilName()
    {
        $this->reset('pupil_id', 'editingPupilName', 'pupilFirstName', 'pupilLastName');
    }
    
    public function updatePupilName()
    {
        $pupilNameHasAlreadyTaken = Pupil::where('lastName', $this->pupilLastName)->where('firstName', $this->pupilFirstName)->first();
        $pupil = Pupil::find($this->pupil_id);
        if(!$pupilNameHasAlreadyTaken && $pupil){
            $p = $pupil->update(
                [
                    'firstName' => strtoupper($this->pupilFirstName),
                    'lastName' => $this->pupilLastName,
                ]
            );
            if($p){
                $this->reset('pupil_id', 'editingPupilName', 'pupilFirstName', 'pupilLastName');
                $this->resetErrorBag();
                $this->emit('classeUpdated');
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "Opération déroulée avec succès!", 'type' => 'success']);
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Opération de mise à jour a échoué!", 'type' => 'error']);
        }
    }




    public function reloadClasseData()
    {
        $this->counter = rand(0, 14);
    }
}
