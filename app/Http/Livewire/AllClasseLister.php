<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Level;
use App\Models\Responsible;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AllClasseLister extends Component
{

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'UpdatedGlobalSearch' => 'updatedSearch',
        'UpdatedSchoolYearData' => 'reloadData',
        'ClasseDataWasUpdated' => 'reloadData',
    ];

    public $counter = 0;

    public $count = 0;

    public $search = null;

    public $hasErrors = false;

    public $classe_group_id;

    public $level = 'secondary';

    public $level_id;


    use ModelQueryTrait;






    public function render()
    {
        $classes = [];

        $school_year_model = $this->getSchoolYear();

        $lastYear = $this->getLastYear();

        $levels = $school_year_model->levels;

        $this->level_id = Level::where('name', $this->level)->first()->id;

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
                $classes = Classe::where('level_id', $this->level_id)->orderBy('name', 'asc')->get();
            }
        }

       

        return view('livewire.all-classe-lister', compact('classes', 'levels', 'classe_groups', 'school_year_model', 'lastYear'));
    }



    public function resetSelectedData()
    {
        $this->reset('classe_group_id', 'level_id');
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
        $search = $this->search;

        $target = '%' . $search . '%';

        $classes = Classe::where('level_id', $this->level_id)->where('classes.name', 'like', $target)->get();
        

        return $classes;

    }


    public function getClasses()
    {

        $classes = [];

        if($this->level_id){

            if($this->classe_group_id){

                $classe_group = ClasseGroup::find($this->classe_group_id);

                if($classe_group){

                    $classes = $classe_group->classes;
                }
            }
            else{
                $classes = Classe::where('classes.level_id', $this->level_id)->get();
            }
        }

        return $classes;
    }

    public function addNewClasse()
    {
        $this->emit('createNewClasseLiveEvent');
    }

    public function editClasseReferees($classe_id)
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $this->getSchoolYear()->id);
    }


    public function editClasseRespo1($classe_id, $target = 'respo1')
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $target, $this->getSchoolYear()->id);
    }


    public function editClasseRespo2($classe_id, $target = 'respo2')
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $target, $this->getSchoolYear()->id);
    }

    public function editClasseReferee($classe_id, $target = 'pp')
    {
        $this->emit('ManageClasseRefereesLiveEvent', $classe_id, $target, $this->getSchoolYear()->id);
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


    public function joinAll()
    {
        $this->reset('count');

        $classes = Classe::all();

        $school_year_model = $this->getSchoolYear();

        foreach($classes as $classe){

            $yet = $school_year_model->classes()->where('classes.id', $classe->id)->first();

            DB::transaction(function($e) use ($yet, $school_year_model, $classe){

                if(!$yet){

                    $school_year_model->classes()->attach($classe->id);

                    Responsible::create(['school_year_id' => $school_year_model->id, 'classe_id' => $classe->id]);

                    $this->count = $this->count + 1;

                }

            });

        }

        DB::afterCommit(function() use ($school_year_model){

            $count = $this->count;

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "Les données de $count classes relatives à l'année-scolaire $school_year_model->school_year ont été générées avec succès!", 'type' => 'success']);

                $this->emit('UpdatedSchoolYearData');

        });
    }


    public function join($classe_id)
    {
        $school_year_model = $this->getSchoolYear();

        $classe = Classe::find($classe_id);


        if($classe){

            $yet = $school_year_model->classes()->where('classes.id', $classe_id)->first();

            DB::transaction(function($e) use ($yet, $school_year_model, $classe){

                if(!$yet && $classe){

                    $school_year_model->classes()->attach($classe->id);

                    Responsible::create(['school_year_id' => $school_year_model->id, 'classe_id' => $classe->id]);

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Elle est désormais disponible en $school_year_model->school_year !", 'type' => 'success']);

                    $this->emit('UpdatedSchoolYearData');
                }
                else{

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ATTENTION', 'message' => "Cette  $classe->name semble déjà être liée à l'année-scolaire $school_year_model->school_year !", 'type' => 'warning']);

                    $this->emit('UpdatedSchoolYearData');
                }

            });


        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE INTROUVABLE', 'message' => "La classe renseignée n'existe pas dans la base de données !", 'type' => 'warning']);

        }


    }


    public function disjoinAll()
    {
        $this->reset('count');

        $classes = Classe::all();

        $school_year_model = $this->getSchoolYear();

        foreach($classes as $classe){

            $yet = $school_year_model->classes()->where('classes.id', $classe->id)->first();

            DB::transaction(function($e) use ($yet, $school_year_model, $classe){

                if($yet){

                    $classe->classeDeleter();

                    $this->count = $this->count + 1;

                }

            });

        }

        DB::afterCommit(function() use ($school_year_model){

            $count = $this->count;

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "Les données de $count classes relatives à l'année-scolaire $school_year_model->school_year ont été supprimé avec succès!", 'type' => 'success']);

                $this->emit('UpdatedSchoolYearData');

        });


    }


    public function disjoin($classe_id)
    {
        $school_year_model = $this->getSchoolYear();

        $classe = Classe::find($classe_id);

        if($classe){

            $classe->classeDeleter();

            DB::afterCommit(function() use ($school_year_model, $classe){

                $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "Les données de la classe de $classe->name relatives à l'année-scolaire $school_year_model->school_year ont été supprimé avec succès!", 'type' => 'success']);

                    $this->emit('UpdatedSchoolYearData');

            });

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE INTROUVABLE', 'message' => "La classe renseignée n'existe pas dans la base de données !", 'type' => 'warning']);

        }

    }
}
