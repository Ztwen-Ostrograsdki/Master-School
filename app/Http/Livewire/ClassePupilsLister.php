<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use Livewire\Component;
use App\Models\SchoolYear;

class ClassePupilsLister extends Component
{
    use ModelQueryTrait;
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
    ];
    
    
    public $classe_id;

    public $counter = 0;
    public $selected;
    public $selectedAction;
    public $checkeds = [];
    public $selecteds = [];
    public $activeData = [];

    public $pupilFirstName;
    public $pupilLastName;
    public $pupil_id;
    public $editingPupilName = false;


    public function render()
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();

        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        $school_years = SchoolYear::all();
        $pupils = [];

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
            $subject_id = intval(session('classe_subject_selected'));
            if(in_array($subject_id, $classe->subjects->pluck('id')->toArray())){
                session()->put('classe_subject_selected', $subject_id);
                $classe_subject_selected = $subject_id;
            }
            else{
                $classe_subject_selected = null;
            }
        }
        else{
            $classe_subject_selected = null;
        }

        if($classe){
            $all_pupils = $classe->pupils()->orderBy('firstName', 'asc')->get();
            foreach($all_pupils as $p){
                if($p->school_years){
                    $pupil_of_selected_school_year = $p->school_years()->where('school_year', $school_year)->first();
                    if($pupil_of_selected_school_year){
                        $pupils[] = $p;
                    }
                }
            }
        }

        return view('livewire.classe-pupils-lister', compact('classe', 'pupils', 'classe_subject_selected'));
    }


    public function deletePupil($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $pupil->delete();
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "l'apprenant $pupil->name envoyé dans la corbeille!", 'type' => 'success']);
        $this->emit('classeUpdated');
        $this->emit('classePupilListUpdated');
    }

    public function forceDeletePupil($pupil_id)
    {
        $school_year_model = $this->getSchoolYear();
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $marks = $pupil->marks;
            $classes = $pupil->classes;
            if(count($marks) > 0){
                foreach($marks as $mark){
                    $school_year_model->marks()->detach($mark->id);
                    $mark->delete();
                }
            }
            if(count($classes) > 0){
                foreach($classes as $classe){
                    $classe->pupils()->detach($pupil->id);
                }
            }
            $pupil->absences()->delete();
            $pupil->lates()->delete();
            $school_year_model->pupils()->detach($pupil->id);
            $pupil->forceDelete();
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "l'apprenant $pupil->name a été supprimé définitivement!", 'type' => 'success']);
        $this->emit('classeUpdated');
        $this->emit('classePupilListUpdated');
    }

    public function editClasseSubjects($classe_id = null)
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        if($classe){
            $this->emit('manageClasseSubjectsLiveEvent', $classe->id);
        }

    }

    public function addNewPupilTo()
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        if($classe){
            $this->emit('addNewPupilToClasseLiveEvent', $classe->id);
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
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




    
    public function reloadClasseData($school_year = null)
    {
        $this->reset('pupil_id', 'editingPupilName', 'pupilFirstName', 'pupilLastName');
        $this->counter = 1;
    }
}
