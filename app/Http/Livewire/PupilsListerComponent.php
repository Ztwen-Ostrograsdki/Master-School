<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PupilsListerComponent extends Component
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
    public $classe_group_id_selected;
    public $classe_id_selected;
    public $sexe_selected;
    public $theLevel;
    public $level;
    public $editingPupilName = false;

    public $levels = ['Secondaire' => 'secondary', 'Primaire' => 'primary'];





    public function mount($level)
    {
        if($level){
            $this->level = $level;
            $this->theLevel = $this->levels[ucfirst($level)];
        }
        else{
            return abort(404);
        }

    }

    public function resetSearch()
    {
        $this->reset('search');
    }

    public function updatedSearch($value)
    {
        $this->search = $value;
    }

    public function updatedSexeSelected($sexe)
    {
        $this->sexe_selected = $sexe;
    }

    public function updatedClasseIdSelected($classe_id)
    {
        $this->reset('search', 'classe_group_id_selected');
        $this->classe_id_selected = $classe_id;
    }


    public function updatedClasseGroupIdSelected($classe_group_id)
    {
        $this->reset('search', 'classe_id_selected');
        $this->classe_group_id_selected = $classe_group_id;
    }

    public function render()
    {
        $school_year_model = $this->getSchoolYear();
        $pupils = [];
        $classes = [];
        $classe_groups = $school_year_model->classe_groups()->orderBy('classe_groups.name', 'asc')->get();
        $classes = $school_year_model->classes()->orderBy('classes.name', 'asc')->get();
        if($this->theLevel){
            $level = Level::where('name', $this->theLevel)->firstOrFail();
            if($this->search && mb_strlen($this->search) >= 2){
                $pupils = Pupil::where('level_id', $level->id)->where('firstName', 'like', '%' . $this->search . '%')->orWhere('lastName', 'like', '%' . $this->search . '%')->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
            }
            else{

                $sexe = $this->sexe_selected;

                if($this->classe_id_selected){

                    $classe = $school_year_model->classes()->where('classes.id', $this->classe_id_selected)->first();

                    if($this->sexe_selected && $classe){

                        $pupils = $classe->getPupils($school_year_model->id, null, $this->sexe_selected);
                    }
                    elseif($classe){
                        $pupils = $classe->getPupils($school_year_model->id);
                    }

                }
                elseif($this->classe_group_id_selected){

                    $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id_selected)->first();
                    $pupils_ids = [];    

                    if($classe_group){
                        $classes_cg = $classe_group->classes;
                        if(count($classes_cg) > 0){
                            foreach($classes_cg as $classe){
                                $pupils_ids = $classe->getPupils($school_year_model->id, null, null, true);
                            }
                        } 
                    }

                    if($sexe && $classe_group){
                        $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                    }
                    else{
                        $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                    }

                }
                elseif($sexe){
                    $pupils = Pupil::where('level_id', $level->id)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }
                else{
                    $pupils = Pupil::where('level_id', $level->id)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }


                

            }
            
        }
        return view('livewire.pupils-lister-component', compact('pupils', 'school_year_model', 'classes', 'classe_groups'));
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
