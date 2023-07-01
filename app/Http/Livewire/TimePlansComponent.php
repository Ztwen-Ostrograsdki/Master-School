<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Level;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TimePlansComponent extends Component
{
    use ModelQueryTrait;

    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadSchoolYear', 'timePlanTablesWasUpdatedLiveEvent' => 'reloadData'];

    public $search = 6;
    public $counter = 0;
    public $data = [];
    public $classe_group_id_selected;
    public $classe_id_selected;
    public $level_id_selected;
    public $randomSubjectsTab = ['SVT', 'MATHS', 'HG', 'EPS', 'ANG', 'PHILO', 'PCT', 'ECO'];
    public $morning_times1 = [
        1 => ['s' => 7, 'e' => 8],
        2 => ['s' => 8, 'e' => 9],
        3 => ['s' => 9, 'e' => 10]
    ];

    public $morning_times2 = [
        1 => ['s' => 10, 'e' => 11],
        2 => ['s' => 11, 'e' => 12],
        3 => ['s' => 12, 'e' => 13],
    ];

    public $afternoon_times = [
        1 => ['s' => 14, 'e' => 15],
        2 => ['s' => 15, 'e' => 16],
        4 => ['s' => 16, 'e' => 17],
        5 => ['s' => 17, 'e' => 18],
        6 => ['s' => 18, 'e' => 19],
    ];
    

    public function render()
    {
        $classes = [];
        $classe_groups = [];
        $levels = [];

        $school_year_model = $this->getSchoolYear();


        if(session()->has('timePlan_level_list_selected') && session('timePlan_level_list_selected') !== null){
            $this->level_id_selected = session('timePlan_level_list_selected');

        }

        if(session()->has('timePlan_classe_list_selected') && session('timePlan_classe_list_selected') !== null){
            $this->classe_id_selected = session('timePlan_classe_list_selected');

        }

        if(session()->has('timePlan_classe_group_list_selected') && session('timePlan_classe_group_list_selected') !== null){
            $this->classe_group_id_selected = session('timePlan_classe_group_list_selected');

        }

        $levels = Level::all();
        $classe_groups = ClasseGroup::all();

        $classes = $classes = Classe::all();
        $classesToShow = $this->getClasses();

        $times_plans = $school_year_model->timePlans;

        return view('livewire.time-plans-component', compact('classe_groups', 'classes', 'levels', 'classesToShow', 'times_plans'));
    }


    public function deleteClasseTimePlans($classe_id = null)
    {
        $school_year_model = $this->getSchoolYear();
        if($classe_id){
            $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
            if($classe){
                $classe_name = $classe->name;
                DB::transaction(function($e) use($classe, $school_year_model, $teacher){
                    $times_plans = $classe->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->each(function($time_plan){
                        $time_plan->delete();
                    });
                });

                DB::afterCommit(function() use($classe_name){
                    $this->dispatchBrowserEvent('Toast', ['type' => 'success', 'title' => 'SUPPRESSION REUSSIE',  'message' => "Les emplois du temps de la classe de $classe_name ont été rafraîchies avec succès!"]);
                    $this->reloadData();
                });

            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE INTROUVABLE', 'message' => "Cette classe est inconnue ou a été supprimé ou bloqué momentanement!", 'type' => 'warning']);
            }
        }
        else{

            DB::transaction(function($e) use($school_year_model){
                    $classes = $this->getClasses();
                    foreach($classes as $classe){
                        $classe->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->each(function($time_plan){
                            $time_plan->delete();
                        });
                        
                    }
                });

            DB::afterCommit(function(){
                $this->dispatchBrowserEvent('Toast', ['type' => 'success', 'title' => 'SUPPRESSION REUSSIE',  'message' => "La table des emplois du temps des classes sélectionnées a été rafraîchies avec succès!"]);
                $this->reloadData();
            });


        }
            
    }


    public function addTimePlan()
    {
        $this->emit('insertTimePlan', null);
    } 

    public function updatedSearch($search)
    {
        if($search){
            if(is_numeric($search)){
                $classes = config('app.classes_by_number');
                if(isset($classes[$search])){
                    $this->search = $classes[$search];
                }
            }
            else{
                $this->search = $search;
            }

        }
        
    }

    public function resetSearch()
    {
        $this->search = null;
    }

    public function setTimePlansActiveSection($section)
    {
        session()->put('timePlan_section_selected', $section);
    }

     public function changeSection($section)
    {
        session()->put('timePlan_level_list_selected', $this->level_id_selected);
        session()->put('timePlan_classe_list_selected', $this->classe_id_selected);
        session()->put('timePlan_classe_group_list_selected', $this->classe_group_id_selected);

        $this->emit('changeTimePlanList', $this->level_id_selected, $this->classe_id_selected, $this->classe_group_id_selected);

        $this->counter = rand(1, 12);
    }

    public function getClasses()
    {
        $school_year_model = $this->getSchoolYear();
        $level_id = $this->level_id_selected;
        $classe_group_id = $this->classe_group_id_selected;
        $classe_id = $this->classe_id_selected;
        $search = $this->search;

        if(isset($search) && strlen($search) > 2){
            $target = '%' . trim($search) . '%';
            $classes = $school_year_model->classes()->where('classes.name', 'like', $target)->get();
        }
        else{
            if($classe_id){
                $classes = $school_year_model->classes()->where('classes.id', $classe_id)->get();
            }
            else{
                if($level_id){
                    if($classe_group_id){
                        $this->classe_id_selected = null;
                        session()->put('timePlan_classe_list_selected', $this->classe_id_selected);
                        $classe_group = ClasseGroup::where('classe_groups.id', $classe_group_id)->first();
                        $classes = $classe_group->classes;
                    }
                    else{
                        $this->classe_id_selected = null;
                        $this->classe_group_id_selected = null;
                        session()->put('timePlan_classe_list_selected', $this->classe_id_selected);
                        session()->put('timePlan_classe_group_list_selected', $this->classe_group_id_selected);

                        $classes = $school_year_model->classes()->where('classes.level_id', $level_id)->orderBy('classes.name', 'desc')->get();
                    }
                }
                else{
                    if($classe_group_id){
                        $this->classe_id_selected = null;
                        $this->level_id_selected = null;
                        session()->put('timePlan_classe_list_selected', $this->classe_id_selected);
                        session()->put('timePlan_level_list_selected', $this->level_id_selected);
                        $classe_group = ClasseGroup::where('classe_groups.id', $classe_group_id)->first();
                        $classes = $classe_group->classes;
                    }
                    else{
                        $this->classe_id_selected = null;
                        $this->level_id_selected = null;
                        $this->classe_group_id_selected = null;
                        session()->put('timePlan_classe_list_selected', $this->classe_id_selected);
                        session()->put('timePlan_level_list_selected', $this->level_id_selected);
                        session()->put('timePlan_classe_group_list_selected', $this->classe_group_id_selected);

                        $classes = Classe::orderBy('name', 'desc')->get();
                    }
                }
            }
        }

        return $classes;

    }

    public function reloadSchoolYear($school_year)
    {
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->counter = rand(0, 12);
    }

    public function getClassGroups()
    {
        $level_id = $this->level_id_selected;

        if($level_id){
            $class_groups = ClasseGroup::where('level_id', $level_id)->get();
        }
        else{
            $class_groups = ClasseGroup::all();
        }

        return $class_groups;

    }

    public function randomSubjects()
    {
        

    }
}
