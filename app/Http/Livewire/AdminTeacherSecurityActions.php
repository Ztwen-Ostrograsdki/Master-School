<?php

namespace App\Http\Livewire;

use App\Events\InitiateMarksStoppingEvent;
use App\Helpers\AdminTraits\AdminTrait;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\MarkStopped;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminTeacherSecurityActions extends Component
{
    use ModelQueryTrait;

    use AdminTrait;

    protected $listeners = [
        'updatedTeachersSelectedsList' => 'getList',
        'newTeacherHasBeenAdded' => 'reloadData' ,
        'UpdatedGlobalSearch' => 'updatedSearch',
        'userDataEdited' => 'reloadData',
        'ClasseSecuritiesWasDelete' => 'notifyKeysDeleted',
    ];

    public $school_year_model;

    public $level_id_selected = null;

    public $classe_id_selected = null;

    public $subject_id_selected = null;

    public $counter = 0;

    public $occurence = 0;

    public $start = false;

    public $search = '';

    public $search_target = '';

    public $teachers_selecteds = [];

    public $teachers_table = [];

    public $classe_id = null;

    public $teacher_id = null;

    public $common_acted = null;

    public $acted = null;

    public $actions = [
        'locked-true' => 'Verrouiler la classe',
        'locked-false' => 'Déverrouiler la classe',
        'closed-true' => 'Fermer la classe',
        'closed-false' => 'Ouvrir la classe',
        'locked_marks-true' => 'Verrouiler insertion des notes',
        'locked_marks-false' => 'Déverrouiler insertion des notes',
        'locked_marks_updating-true' => 'Verrouiler édition des notes',
        'locked_marks_updating-false' => 'Déverrouiler édition des notes',

    ];

    public $teachers_actions_inputs = [];

    public $current_actions = [];


    public function render()
    {
        $levels = Level::all();

        $classes = Classe::all();

        $subjects = Subject::all();

        if(session()->has('teacher_level_list_selected') && session('teacher_level_list_selected') !== null){
            $this->level_id_selected = session('teacher_level_list_selected');

        }

        if(session()->has('teacher_classe_list_selected') && session('teacher_classe_list_selected') !== null){
            $this->classe_id_selected = session('teacher_classe_list_selected');

        }

        if(session()->has('teacher_subject_list_selected') && session('teacher_subject_list_selected') !== null){
            $this->subject_id_selected = session('teacher_subject_list_selected');

        }
        
        return view('livewire.admin-teacher-security-actions', compact('classes', 'levels', 'subjects'));
    }


    public function setTeachersActiveSection($section)
    {
        session()->put('teachers_section_selected', $section);
    }

     public function addNewTeacher()
    {
        $this->emit('addNewTeacher');
    }

    public function changeSection($section)
    {
        session()->put('teacher_level_list_selected', $this->level_id_selected);

        session()->put('teacher_classe_list_selected', $this->classe_id_selected);

        session()->put('teacher_subject_list_selected', $this->subject_id_selected);

        $this->emit('changeTeacherList', $this->level_id_selected, $this->classe_id_selected, $this->subject_id_selected);

        $this->counter = rand(1, 12);
    }

    public function startProcess()
    {
        $this->school_year_model = $this->getSchoolYear();

        $school_year_model = $this->school_year_model;

        $teachers = $school_year_model->teachers()->whereIn('teachers.id', $this->teachers_selecteds)->get();

        $this->teachers_table = $teachers;

        $this->start = true;

       
    }


    public function updatedTeacherId($id)
    {
    }


    public function hide()
    {
        $this->emit('selectedsWasChanged', $this->teachers_selecteds);

        $this->start = false;
    }


    public function cancel()
    {
        $this->reset('teachers_selecteds', 'teachers_table', 'teachers_actions_inputs', 'current_actions', 'acted', 'occurence');

        $this->emit('selectedsWasChanged', $this->teachers_selecteds);

        $this->start = false;
    }

    public function manageTeacherClasses($teacher_id)
    {
        $this->emit('manageTeacherClasses', $teacher_id);
    }


    public function insertActions($teachers_id, $classe_id)
    {
        $action = $this->acted;

        $key = $teachers_id . '-' . $classe_id;

        if($this->acted){

            foreach($this->teachers_actions_inputs as $index => $data){

                if(array_key_exists($key, $data)){

                    unset($data[$key]);

                    unset($this->teachers_actions_inputs[$index]);

                    unset($this->current_actions[$key]);

                }

            }

            $this->teachers_actions_inputs[] = [$key => $action];

            $this->setClasseAction($key);

            // $this->reset('acted');
        }
        else{
            foreach($this->teachers_actions_inputs as $index => $data){

                if(array_key_exists($key, $data)){

                    unset($data[$key]);

                    unset($this->teachers_actions_inputs[$index]);

                    unset($this->current_actions[$key]);

                }
            }
        }


    }

    public function setClasseAction($key)
    {
        $action = '';

        if($this->teachers_actions_inputs !== []){

            $arr = $this->teachers_actions_inputs;

            foreach($arr as $ar){

                if(array_key_exists($key, $ar)){

                    $act = $ar[$key];

                    $action = $this->actions[$act];

                }
            }
        }

        $this->current_actions[$key] = $action;



    }

    public function submitTeacherRequest($teacher_id)
    {
        $this->occurence = 0;

        $school_year_model = $this->school_year_model;

        if($this->teachers_actions_inputs !== []){

            $teacher = $school_year_model->findTeacher($teacher_id);

            if($teacher){

                $data = $this->getTeachersRequestFromInputs($teacher_id);

                if($data){

                    DB::transaction(function($e) use ($school_year_model, $data, $teacher){

                        foreach($data as $datum){

                            $subject_id = $teacher->speciality()->id;

                            $teacher_id = $teacher->id;

                            $classe_id = $datum['classe_id'];

                            $secure_column = $datum['secure_column'];

                            $action = $datum['action'];

                            $classe = $school_year_model->findClasse($classe_id);

                            if($classe){

                                if($action == true){

                                    $done = $classe->generateClassesSecurity($secure_column, $teacher_id, $subject_id, 48, $action);
                                }
                                else{

                                    $done = $classe->destroyClasseSecurity($secure_column, $teacher_id);

                                }

                                if($done){

                                    $this->occurence = $this->occurence + 1;

                                }

                            }
                        }

                    });


                    DB::afterCommit(function(){

                        $occurence = $this->occurence;

                        $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => " $occurence requêtes ont été effectuées!", 'type' => 'success']);

                        $this->emit('userDataEdited');

                        $this->reset('teachers_actions_inputs', 'current_actions', 'acted', 'occurence');


                    });

                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " LA requête est ambigüe, veuillez spécifier une action à éxécuter!", 'type' => 'error']);

                }

            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'ENSEIGNANT INTROUVABLE', 'message' => " L'enseignant indexé n'a pas été trouvé dans la base de données!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " LA requête est ambigüe, veuillez spécifier une action à éxécuter!", 'type' => 'error']);

        }
        
    }



    public function submitTeachersRequests()
    {
        $school_year_model = $this->school_year_model;

        $teachers = $this->teachers_table;

        $this->occurence = 0;


        if($teachers){

            if($this->common_acted){

                $actions = explode('-', $this->common_acted);

                if(count($actions) > 1){

                    $secure_column = $actions[0];

                    $todo = $actions[1] == 'true' ? true : false;

                    $this->makeSecureForTeachersSelecteds($teachers, $secure_column, $todo);

                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE ', 'message' => " La requête semble être ambigüe!", 'type' => 'error']);
                }

            }
            elseif($this->teachers_actions_inputs != []){

                foreach($teachers as $teacher){

                    $data = $this->getTeachersRequestFromInputs($teacher->id);

                    if($data){

                        DB::transaction(function($e) use ($school_year_model, $data, $teacher){

                            foreach($data as $datum){

                                $subject_id = $teacher->speciality()->id;

                                $teacher_id = $teacher->id;

                                $classe_id = $datum['classe_id'];

                                $secure_column = $datum['secure_column'];

                                $action = $datum['action'];

                                $classe = $school_year_model->findClasse($classe_id);

                                if($classe){

                                    if($action == true){

                                        $done = $classe->generateClassesSecurity($secure_column, $teacher_id, $subject_id, 48, $action);
                                    }
                                    else{

                                        $done = $classe->destroyClasseSecurity($secure_column, $teacher_id);

                                    }

                                    if($done){

                                        $this->occurence = $this->occurence + 1;

                                    }

                                }
                            }

                        });

                    }
                    else{

                        $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " LA requête est ambigüe, veuillez spécifier l'action à éxécuter!", 'type' => 'error']);

                    }


                }

                DB::afterCommit(function(){

                    $occurence = $this->occurence;

                    $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => " $occurence requêtes ont été effectuées!", 'type' => 'success']);

                    $this->emit('userDataEdited');

                    $this->reset('teachers_actions_inputs', 'current_actions', 'acted', 'occurence');


                });

            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE ', 'message' => " La requête semble être ambigüe; Veuillez sélectionner une action à éxécuter!", 'type' => 'error']);
            }

        }



    }



    public function getTeachersRequestFromInputs($teacher_id)
    {
        $inputs = $this->teachers_actions_inputs;

        $data = [];

        foreach($inputs as $input){

            if(isset($input) && $input){

                foreach($input as $key => $action){

                    $t_cl = explode('-', $key);

                    if(count($t_cl) > 1){

                        $t_id = (int)$t_cl[0];

                        $cl_id = (int)$t_cl[1];

                        if($t_id == $teacher_id){

                            $actions = explode('-', $action);

                            $secure_column = $actions[0];

                            $todo = $actions[1] == 'true' ? true : false;

                            $data[] = [
                                'classe_id' => $cl_id,
                                'secure_column' => $secure_column,
                                'action' => $todo,

                            ];

                        }
                    }
                }

            }

        }

        return $data;
    }


    public function makeSecureForTeachersSelecteds($selectedTeachers, $secure_column, $action = true)
    {

        if(count($selectedTeachers) > 0){

            if($secure_column){

                DB::transaction(function($e) use($selectedTeachers, $secure_column, $action){

                    foreach($selectedTeachers as $teacher){

                        $classes = $teacher->getTeachersCurrentClasses();

                        if($classes){

                            foreach($classes as $classe){

                                if($action == true){

                                    $done = $classe->generateClassesSecurity($secure_column, $teacher->id, $teacher->speciality()->id, 48, $action);
                                }
                                else{

                                    $done = $classe->destroyClasseSecurity($secure_column, $teacher->id);

                                }

                                if($done){

                                    $this->occurence = $this->occurence + 1;

                                }

                            }

                        }

                    }

                });

                DB::afterCommit(function(){

                    $occurence = $this->occurence;

                    $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => " $occurence requêtes ont été effectuées!", 'type' => 'success']);

                    $this->emit('userDataEdited');

                    $this->reset('teachers_actions_inputs', 'current_actions', 'acted', 'occurence');


                });

            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " La requête est ambigüe, veuillez spécifier l'action à éxécuter!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => " La requête est ambigüe, veuillez spécifier l'action à éxécuter!", 'type' => 'error']);

        }

    }


    public function updatedSearch($search)
    {
        if($search && mb_strlen($search) > 1){

            $this->search_target = $search;

            $this->emit('TeacherTableListFetchOnSearch', $search);
        }
        else{

            $this->reset('search_target');

            $this->emit('TeacherTableListFetchOnSearch', $search);
        }
        
    }

    public function resetSearch()
    {
        $this->reset('search');

        $this->emit('TeacherTableListFetchOnSearch', '');
    }


    public function reloadData()
    {
        $this->counter = rand(1, 12);
    }

   

    public function getList($list)
    {
        $this->teachers_selecteds = $list;
    }


    public function promoteToteaching($teacher_id)
    {
        
    }


    public function destroyClasseSecuritiesKeys($target = null)
    {
        $school_year_model = $this->getSchoolYear();



        $keys = [];

        if(!$target){

            $keys = $school_year_model->securities()->pluck('classes_securities.id')->toArray();

        }
        else{

            if($target == 'classes'){

                $keys = $school_year_model->securities()
                                          ->whereNotNull('classes_securities.classe_id')
                                          ->whereNull('classes_securities.teacher_id')
                                          ->pluck('classes_securities.id')
                                          ->toArray();

            }
            elseif($target == 'teachers'){

                $keys = $school_year_model->securities()
                                          ->whereNotNull('classes_securities.teacher_id')
                                          ->pluck('classes_securities.id')
                                          ->toArray();

            }
            elseif($target == 'marks'){

                $keys = $school_year_model->securities()
                                          ->where('classes_securities.locked_marks', true)
                                          ->orWhere('classes_securities.locked_marks_updating', true)
                                          ->pluck('classes_securities.id')
                                          ->toArray();

            }


        }


        if($keys){

            $this->__destroyClasseSecuritiesKeyExpired($keys);

        }

    }



    public function notifyKeysDeleted($occurence)
    {
        if($occurence){

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR REUSSIE', 'message' => "$occurence clés ont été supprimé avec succès!", 'type' => 'success']);

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'RECHERCHE TERMINEE', 'message' => "Aucune clé expirée n'a été trouvé!", 'type' => 'info']);

        }

    }
}
