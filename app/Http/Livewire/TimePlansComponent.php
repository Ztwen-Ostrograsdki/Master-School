<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Level;
use Livewire\Component;

class TimePlansComponent extends Component
{
    use ModelQueryTrait;

    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadSchoolYear'];

    public $search = 'Première';
    public $counter = 0;
    public $data = [];
    public $classe_group_id_selected;
    public $classe_id_selected;
    public $level_id_selected;
    public $randomSubjectsTab = ['SVT', 'MATHS', 'HG', 'EPS', 'ANG', 'PHILO', 'PCT', 'ECO'];
    public $morning_times1 = ['07H - 08H', '08H - 09H', '09H - 10H'];
    public $morning_times2 = ['10H - 11H', '11H - 12H', '12H - 13H'];
    public $afternon_times = ['14H - 15H', '15H - 16H', '16H - 17H', '17H - 18H', '18H - 19H'];
    

    public function render()
    {
        $classes = [];
        $classe_groups = [];
        $levels = [];


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

        return view('livewire.time-plans-component', compact('classe_groups', 'classes', 'levels', 'classesToShow'));
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
