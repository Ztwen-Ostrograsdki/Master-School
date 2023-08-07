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
        'UpdatedGlobalSearch' => 'reloadClasseDataOnSearch',
        'GlobalDataUpdated' => 'reloadClasseData',
    ];
    
    
    public $counter = 0;
    public $selected;
    public $selectedAction;
    public $checkeds = [];
    public $selecteds = [];
    public $activeData = [];
    public $download_pdf_z = false;
    public $search = null;
    public $slug;
    public $level;
    public $classe_id;

    public $pupilFirstName;
    public $pupilLastName;
    public $pupil_id;
    public $editingPupilName = false;



    public function mount($slug)
    {
         if($slug){

            $this->slug = $slug;

            $target = '%' . mb_substr($slug, 0, 3) . '%';

            $level = Level::where('name', 'like', $target)->first();

            if($level){

                $this->level = $level;
            }
           
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

            $level = $this->level;

            $target = '%' . 'polyvalente' . '%';

            $classe = Classe::where('name', 'like', $target)->where('level_id', $level->id)->first();
            
            if($classe){

                $this->classe_id = $classe->id;

                if($this->search){

                    $pupils = Pupil::where('level_id', $level->id)
                            ->where('classe_id', $classe->id)
                            ->where('firstName', 'like', '%' . $search . '%')
                             ->orWhere('lastName', 'like', '%' . $search . '%')
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc')
                             ->get();
                }
                else{

                    $pupils = Pupil::where('level_id', $level->id)
                            ->where('classe_id', $classe->id)
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc')
                             ->get();
                }
            }
        }
        return view('livewire.polyvalente-classe-manager', compact('classe', 'pupils', 'school_year_model'));
    }

    public function migrateTo($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $this->emit('MovePupilToNewClasse', $pupil->id);
        }
        
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
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $pupil->pupilDeleter(null, true);
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

    public function addNewPupilTo()
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();

        if($classe){

            $this->emit('addNewPupilToClasseLiveEvent', $classe->id);
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
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
}
