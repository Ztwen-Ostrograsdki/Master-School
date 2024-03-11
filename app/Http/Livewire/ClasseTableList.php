<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Livewire\Component;

class ClasseTableList extends Component
{

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'newClasseCreated' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'classeGroupUpdated' => 'reloadData',
        'classeGroupSubjectsUpdated' => 'reloadData',
        'newClasseCreated' => 'reloadData',
        'newLevelCreated' => 'reloadData',
        'ClassesUpdatedLiveEvent' => 'reloadData',
     ];

    public $counter = 0;
    public $classe_group_id = null;
    public $level_id = null;
    public $selecteds = [];
    public $search = '';
    public $title = 'Le prof...';


    use ModelQueryTrait;



    public function render()
    {
        $classes = [];
        $school_year_model = $this->getSchoolYear();

        $levels = $school_year_model->levels;

        if($this->search && mb_strlen($this->search) > 2){
            $classes = $this->getClassesOnSearch();
            $classe_groups = $school_year_model->classe_groups;
        }
        else{
            if($this->level_id){
                $classe_groups = $school_year_model->classe_groups()->where('classe_groups.level_id', $this->level_id)->get();
            }
            else{
                $classe_groups = $school_year_model->classe_groups;
            }

            if($this->classe_group_id || $this->level_id){
                $classes = $this->getClasses();
            }
            else{
                $classes = $school_year_model->classes()->orderBy('name', 'asc')->get();
            }
        }

       

        return view('livewire.classe-table-list', compact('classes', 'levels', 'classe_groups', 'school_year_model'));
    }



    public function resetSelectedData()
    {
        $this->reset('classe_group_id', 'level_id');
    }

    public function updatedLevelId($level_id)
    {
        $this->level_id = $level_id;
        $this->reset('classe_group_id');
    }

    public function updatedClasseGroupId($classe_group_id)
    {
        $this->classe_group_id = $classe_group_id;
    }

    public function updatedSearch($search)
    {
        $this->search = $search;
    }


    public function getClassesOnSearch()
    {
        $school_year_model = $this->getSchoolYear();
        $search = $this->search;
        $target = '%' . $search . '%';

        $classes = $school_year_model->classes()->where('classes.name', 'like', $target)->get();
        

        return $classes;

    }


    public function getClasses()
    {
        $school_year_model = $this->getSchoolYear();
        $classes = [];

        $levels = $school_year_model->levels;

        if($this->level_id){
            if($this->classe_group_id){
                $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id)->first();
                if($classe_group){
                    $classes = $classe_group->classes;
                }
            }
            else{
                $classes = $school_year_model->classes()->where('classes.level_id', $this->level_id)->get();
            }
        }
        elseif($this->classe_group_id){
            $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id)->first();
            if($classe_group){
                $classes = $classe_group->classes;
            }
        }
        return $classes;
    }

    public function addNewClasse()
    {
        $this->emit('createNewClasseLiveEvent');
    }

    public function editClasseRespo1($classe_id, $target = 'r1')
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $target, $this->getSchoolYear()->id);
    }


    public function editClasseRespo2($classe_id, $target = 'r2')
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $target, $this->getSchoolYear()->id);
    }

    public function editClasseReferee($classe_id, $target = 'pp')
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $target, $this->getSchoolYear()->id);
    }

    public function resetReferee($classe_id, $target)
    {


    }


    public function updateName($classe_id)
    {
        // $this->emit('UpdateClasseLiveEvent', $classe_id);
    }



    public function reloadData()
    {
        $this->counter = rand(1, 7);
    }


    public function deleteClasse($classe_id)
    {
        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
        if($classe){
            $class = "App\Models\Classe";
            $this->emit('ConfirmRequestLiveEvent', $classe->id, $class, $school_year_model->id, 'D');
        }
    }

    public function lockClasseMarks($classe_id, $duration = 48)
    {
        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
        if($classe){
            $req = $classe->lockClasseUpdatedMarks(null, null, $duration);
            if($req){
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'TERMINEE', 'message' => "La mise à jour des notes de cette classe a été bloquée pour 48 heures!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une erreure est survenue, il est fort probable que les notes de cette classe aient déjà été bloquées!", 'type' => 'warning']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Classe introuvable!", 'type' => 'error']);
        }
    }

}
