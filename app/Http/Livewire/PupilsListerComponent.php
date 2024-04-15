<?php

namespace App\Http\Livewire;

use App\Events\PreparePupilDataToFetchEvent;
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
        'GlobalDataUpdated' => 'reloadClasseData',
        'DataAreReadyToFetchLiveEvent' => 'fetchData',
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

    public $slug;

    public $level;

    public $taking = 30;

    public $editingPupilName = false;

    public $levels = [];

    public $pupil_type_selected = 'continued';

    public $data = [];


    public function fetchData($data)
    {
        $this->data = json_encode($data['data']);
    }

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


    public function valideSemestre()
    {
        $school_year_model = $this->getSchoolYear();

        $school_year_model->marksWasAlreadyStopped();
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

    public function updatedPupilTypeSelected($pupil_type_selected)
    {
        $this->reset('search', 'pupil_type_selected');

        $this->pupil_type_selected = $pupil_type_selected;
    }

    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $pupils = [];

        $total = 0;

        $classes = [];

        $pupil_type_selected = $this->pupil_type_selected;

        $classe_groups = $school_year_model->classe_groups()->orderBy('classe_groups.name', 'asc')->get();
        
        $classes = $school_year_model->classes()->orderBy('classes.name', 'asc')->get();
        
        if($this->level){

            $level = $this->level;

            if($this->search && mb_strlen($this->search) >= 2){

                if($pupil_type_selected == 'all'){

                    $pupils = Pupil::where('level_id', $level->id)->where('firstName', 'like', '%' . $this->search . '%')->orWhere('lastName', 'like', '%' . $this->search . '%')->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                }
                elseif ($pupil_type_selected == 'abandonned') {
                    
                    $pupils = Pupil::where('level_id', $level->id)->where('abandonned', true)->where('firstName', 'like', '%' . $this->search . '%')->orWhere('lastName', 'like', '%' . $this->search . '%')->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }
                elseif ($pupil_type_selected == 'continued') {
                    
                    $pupils = Pupil::where('level_id', $level->id)->where('abandonned', false)->where('firstName', 'like', '%' . $this->search . '%')->orWhere('lastName', 'like', '%' . $this->search . '%')->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }
            }
            else{

                $sexe = $this->sexe_selected;

                if($this->classe_id_selected){

                    $classe = $school_year_model->classes()->where('classes.id', $this->classe_id_selected)->first();

                    if($this->sexe_selected && $classe){

                        if($pupil_type_selected == 'all'){

                            $pupils = $classe->getPupils($school_year_model->id, null, $this->sexe_selected);

                        }
                        elseif ($pupil_type_selected == 'abandonned') {
                            
                            $pupils = $classe->getAbandonneds($school_year_model->id, null, $this->sexe_selected);
                        }
                        elseif ($pupil_type_selected == 'continued') {

                            $pupils = $classe->getNotAbandonnedPupils($school_year_model->id, null, $this->sexe_selected);
                        }

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

                    if($sexe){

                        if($pupil_type_selected == 'all'){

                            $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                        }
                        elseif($pupil_type_selected == 'abandonned'){

                            $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->where('pupils.abandonned', true)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                        }
                        elseif($pupil_type_selected == 'continued'){

                            $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->where('pupils.abandonned', false)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                        }

                        
                    }
                    else{

                        if($pupil_type_selected == 'all'){

                            $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                        }
                        elseif($pupil_type_selected == 'abandonned'){

                            $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->where('pupils.abandonned', true)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                        }
                        elseif($pupil_type_selected == 'continued'){

                            $pupils = Pupil::where('level_id', $level->id)->whereIn('pupils.id', $pupils_ids)->where('pupils.abandonned', false)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                        }
                    }

                }
                elseif($sexe){

                    if($pupil_type_selected == 'all'){

                            $pupils = Pupil::where('level_id', $level->id)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                        }
                        elseif($pupil_type_selected == 'abandonned'){

                            $pupils = Pupil::where('level_id', $level->id)->where('pupils.abandonned', true)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                        }
                        elseif($pupil_type_selected == 'continued'){

                            $pupils = Pupil::where('level_id', $level->id)->where('pupils.abandonned', false)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                        }

                    $pupils = Pupil::where('level_id', $level->id)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }
                else{

                    if($pupil_type_selected == 'all'){

                        $pupils = Pupil::where('level_id', $level->id)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                    }
                    elseif($pupil_type_selected == 'abandonned'){

                        $pupils = Pupil::where('level_id', $level->id)->where('pupils.abandonned', true)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                    }
                    elseif($pupil_type_selected == 'continued'){

                        $pupils = Pupil::where('level_id', $level->id)->where('pupils.abandonned', false)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                    }

                    if($this->data){

                        // $pupils = $this->data;

                    }
                    else{

                        // $user = auth()->user();

                        // PreparePupilDataToFetchEvent::dispatch($user, $level);

                        // $pupils = Pupil::where('level_id', $level->id)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                    }

                    
                }

            }
            
        }
        return view('livewire.pupils-lister-component', compact('pupils', 'total', 'school_year_model', 'classes', 'classe_groups'));
    }



    public function reloadClasseDataOnSearch($value)
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


    public function migrateTo($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $this->emit('MovePupilToNewClasse', $pupil->id);
        }
        
    }


    public function unclassed($pupil_id)
    {


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




    public function reloadClasseData()
    {
        $this->counter = rand(0, 14);
    }
}
