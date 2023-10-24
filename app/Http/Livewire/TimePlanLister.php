<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class TimePlanLister extends Component
{

    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadSchoolYear', 
        'ChangeTimePlanListLiveEvent' => 'reloadDataToFetch',
        'RefreshTimePlanLiveEvent' => 'reloadTimePlan',
        'RefreshTimePlanIntoClasseProfilLiveEvent' => 'reloadDataToFetchFromEvent',
    ];

    public $search = 6;
    public $counter = 0;
    public $data = [];
    public $subject_id;
    public $classe_id;
    public $classesToShow = [];
    public $intoClasseProfil = false;

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
        return view('livewire.time-plan-lister');
    }



    public function reloadSchoolYear($school_year)
    {
        $this->reloadData();
    }

    public function reloadData()
    {
        $this->counter = rand(0, 12);
    }


    public function reloadDataToFetch($subject_id_selected, $level_id_selected, $classe_id_selected, $classe_group_id_selected, $search)
    {
        $this->classesToShow = $this->getClasses($subject_id_selected, $level_id_selected, $classe_id_selected, $classe_group_id_selected, $search);
    } 

    public function reloadDataToFetchFromEvent($classe_id)
    {
        $this->classesToShow = $this->getClasses(null, null, $classe_id, null, null);
    }

    public function reloadTimePlan()
    {
        $this->classesToShow = $this->getClasses(null, null, null, null, null);
    }





    public function getClasses($subject_id, $level_id, $classe_id, $classe_group_id, $search)
    {
        $school_year_model = $this->getSchoolYear();

        $this->subject_id = $subject_id;

        $this->classe_id = $classe_id;

        $this->search = $search;

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

                        $this->classe_id = null;

                        session()->put('timePlan_classe_list_selected', $this->classe_id);

                        $classe_group = ClasseGroup::where('classe_groups.id', $classe_group_id)->first();

                        $classes = $classe_group->classes;
                    }
                    else{
                        $this->classe_id = null;

                        $classe_group_id = null;

                        session()->put('timePlan_classe_list_selected', $this->classe_id);

                        session()->put('timePlan_classe_group_list_selected', $classe_group_id);

                        $classes = $school_year_model->classes()->where('classes.level_id', $level_id)->orderBy('classes.name', 'desc')->get();
                    }
                }
                else{
                    if($classe_group_id){

                        $this->classe_id = null;

                        $level_id = null;

                        session()->put('timePlan_classe_list_selected', $this->classe_id);

                        session()->put('timePlan_level_list_selected', $level_id);

                        $classe_group = ClasseGroup::where('classe_groups.id', $classe_group_id)->first();

                        $classes = $classe_group->classes;
                    }
                    else{
                        $this->classe_id = null;

                        $level_id = null;

                        $classe_group_id = null;

                        session()->put('timePlan_classe_list_selected', $this->classe_id);

                        session()->put('timePlan_level_list_selected', $level_id);

                        session()->put('timePlan_classe_group_list_selected', $classe_group_id);

                        $classes = Classe::orderBy('name', 'desc')->get();
                    }
                }
            }
        }

        return $classes;

    }


    public function manageTime($classe_id, $start, $end, $day, $school_year = null)
    {
        $this->emit('EditTimePlan', $classe_id, $start, $end, $day, $this->intoClasseProfil, $school_year);
        
        if($this->intoClasseProfil){

            $this->classesToShow = $this->getClasses(null, null, $classe_id, null, null);

        }
        else{

            $this->classesToShow = $this->getClasses(null, null, null, null, null);

        }
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

                    $this->emit('RefreshTimePlanLiveEvent');

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

                $this->emit('RefreshTimePlanLiveEvent');

                $this->dispatchBrowserEvent('Toast', ['type' => 'success', 'title' => 'SUPPRESSION REUSSIE',  'message' => "La table des emplois du temps des classes sélectionnées a été rafraîchies avec succès!"]);

                $this->reloadData();
            });


        }
            
    }








}
