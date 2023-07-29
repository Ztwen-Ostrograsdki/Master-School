<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Barryvdh\DomPDF\PDF;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClassePupilsLister extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
        'UpdatedGlobalSearch' => 'reloadClasseDataOnSearch',
    ];
    
    
    public $classe_id;

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


    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        
        $school_years = SchoolYear::all();
        
        $pupils = [];

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
            $subject_id = intval(session('classe_subject_selected'));
            if($classe && in_array($subject_id, $classe->subjects->pluck('id')->toArray())){
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
            
            $pupils = $classe->getPupils($school_year_model->id, $this->search);
        }

        return view('livewire.classe-pupils-lister', compact('classe', 'pupils', 'classe_subject_selected'));
    }


    public function downloadPDF()
    {
        
    }


    public function deletePupil($pupil_id)
    {
       
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


    public function insertRelatedMark($pupil_id, $semestre = null, $school_year = null)
    {
        $subject_id = session('classe_subject_selected');
        if($subject_id){
            $semestre = session('semestre_selected');
            $school_year_model = $this->getSchoolYear();

            $this->emit('insertRelatedMarkLiveEvent', $pupil_id, $subject_id, $semestre, $school_year_model->id);
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une matière en premier!", 'type' => 'error']);

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

    public function multiplePupilInsertions()
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        if($classe){
            $this->emit('insertMultiplePupils', $classe->id);
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


    public function importPupilsIntoClasse()
    {
        $this->emit('ImportPupilsIntoClasse', $this->classe_id);
    }



    public function printClasseList()
    {

    }
    
    public function reloadClasseData($school_year = null)
    {
        $this->reset('pupil_id', 'editingPupilName', 'pupilFirstName', 'pupilLastName');
        $this->counter = 1;
    }

}
