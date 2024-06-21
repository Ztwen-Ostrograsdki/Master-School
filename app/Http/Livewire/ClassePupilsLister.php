<?php

namespace App\Http\Livewire;

use App\Events\DeletePupilsFromDataBaseEvent;
use App\Events\DetachPupilsFromSchoolYearEvent;
use App\Exports\ExportPupils;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ClassePupilsLister extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
        'UpdatedGlobalSearch' => 'reloadClasseDataOnSearch',
        'ClassePupilsListUpdatingLiveEvent' => 'the_loading',
        'ClassePupilsListUpdatedLiveEvent' => 'the_loaded',
        'ReloadClasseListDataAbandonLiveEvent' => 'reloadClasseData',
        'ClasseDataWasUpdated' => 'reloadClasseData',
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

    public $is_loading = false;

    public $teacher_profil = false;


    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);
        
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

        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);
        
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

        $is_loading = false;
        $editingPupilName = false;
        $pupil_id = $this->pupil_id;

        view()->share('classe', $classe);
        view()->share('pupils', $pupils);
        view()->share('pupil_id', $pupil_id);
        view()->share('is_loading', false);
        view()->share('editingPupilName', $editingPupilName);
        view()->share('classe_subject_selected', $classe_subject_selected);

        $pdf = PDF::loadView('livewire.classe-pupils-lister', [$classe, $pupils, $classe_subject_selected, $is_loading, $editingPupilName, $pupil_id]);

        return $pdf->save('pdf.pdf');
        
    }


    public function the_loading()
    {
        $this->is_loading = true;
    }


    public function the_loaded()
    {
        $this->is_loading = false;

        $this->counter = rand(14, 20);

        $this->emit('classeUpdated');
    }


    public function reloadClasseDataOnSearch($value)
    {
        $this->search = $value;
    }



    public function updatedSearch($value)
    {
        $this->search = $value;
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
        $school_year_model = $this->getSchoolYear();

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

    public function migrateTo($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $this->emit('MovePupilToNewClasse', $pupil->id);
        }
        
    }
    
    public function lockMarksUpdating($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        $pupil->lockPupilMarksUpdating();

    }


    public function unlockMarksUpdating($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        $pupil->unlockPupilMarksUpdating();

    }

    public function lockMarksInsertion($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        $pupil->lockPupilMarksInsertion();

    }

    public function unlockMarksInsertion($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        
        $pupil->unlockPupilMarksInsertion();

    }

    public function printClasseList()
    {

    }
    
    public function reloadClasseData($school_year = null)
    {
        $this->reset('pupil_id', 'editingPupilName', 'pupilFirstName', 'pupilLastName');

        $this->counter = 1;
    }

    public function detachPupil($pupil_id)
    {
        $school_year_model = $this->getSchoolYear();

        $pupil = $school_year_model->findPupil($pupil_id);

        $pupils = [];

        if($pupil){

            $pupils[] = $pupil;

            $user = auth()->user();

            $classe = $school_year_model->findClasse($this->classe_id);

            $name = $pupil->getName();

            DetachPupilsFromSchoolYearEvent::dispatch($user, $school_year_model, $classe, $pupils, false, false);

            $this->dispatchBrowserEvent('Toast', ['title' => 'SUPPRESSION LANCEE', 'message' => "Le retrait de l'apprenant $name de la classe a été lancée avec succès!", 'type' => 'success']);

        }

    }

    public function deletePupil($pupil_id)
    {
        $school_year_model = $this->getSchoolYear();

        $pupil = $school_year_model->findPupil($pupil_id);

        if($pupil){

            $user = auth()->user();

            $classe = $school_year_model->findClasse($this->classe_id);

            $name = $pupil->getName();

            $forceDelete = true;

            $from_data_base = true;

            $pupils = [$pupil];

            DeletePupilsFromDataBaseEvent::dispatch($user, $school_year_model, $classe, $pupils, $from_data_base, $forceDelete);

            $this->dispatchBrowserEvent('Toast', ['title' => 'SUPPRESSION LANCEE', 'message' => "La suppression définitive de l'apprenant $name de la base de donée a été lancée avec succès!", 'type' => 'success']);

        }

    }

    public function toAbandonned($pupil_id)
    {
        $this->emit('setPupilToAbandonned', $pupil_id);
    }


    public function toExcel($classeName = null)
    {

        $classe = Classe::find($this->classe_id);

        $school_year_model = $this->getSchoolYear();

        // return Excel::download(new ExportPupils($classe), 'Liste-classe-de-' . $classe->name . '-' . str_replace(' - ', '-', $school_year_model->school_year) . '.xlsx');

    }

}
