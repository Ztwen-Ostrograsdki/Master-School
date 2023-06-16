<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AdminTeacherSecurityActions extends Component
{
    use ModelQueryTrait;

    protected $listeners = ['updatedTeachersSelectedsList' => 'getList', 'newTeacherHasBeenAdded' => 'reloadData' ];

    public $level_id_selected = null;
    public $classe_id_selected = null;
    public $subject_id_selected = null;
    public $counter = 0;
    public $start = false;
    public $search = '';
    public $search_target = '';
    public $teachers_selecteds = [];
    public $teachers_table = [];



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
        $school_year_model = $this->getSchoolYear();
        $teachers = $school_year_model->teachers()->whereIn('teachers.id', $this->teachers_selecteds)->get();
        $this->teachers_table = $teachers;
        $this->start = true;

       
    } 

    public function submit($teacher_id = null, $classe_id = null, $secure_column = null)
    {
        $school_year_model = $this->getSchoolYear();
        $table = $this->teachers_selecteds;
        if(count($table) > 0){
            if($secure_column){
                if($classe_id){
                    $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
                    if($classe){
                        DB::transaction(function($e) use ($teacher_id, $classe, $secure_column){
                            try {
                                $secure = $classe->generateClassesSecurity($secure_column, $teacher_id, null, 48);
                            } catch (Exception $exception) {
                                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'enseignant a échoué!", 'type' => 'error']);
                            }
                        });
                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['Erreure' => 'Erreure requête', 'message' => "La classe est introuvable!", 'type' => 'error']);
                    }
                }
                else{
                    $teachers = $school_year_model->teachers()->whereIn('teachers.id', $tables)->get();

                    foreach($teachers as $teacher){
                        $classes = $teacher->getTeachersCurrentClasses();
                        if(count($classes) > 0){
                            foreach($classes as $classe){
                                DB::transaction(function($e) use ($teacher, $classe, $secure_column){
                                    try {
                                        $classe->generateClassesSecurity($secure_column, $teacher->id, null, 48);
                                    } catch (Exception $exception) {
                                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'enseignant a échoué!", 'type' => 'error']);
                                    }
                                });

                            }
                        }
                    }
                }


                DB::afterCommit(function() use ($table){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Les données ont été mises à jour avec succès! ", 'type' => 'success']);
                    $this->getList($table);
                });
            }
        }
    }






    public function hide()
    {
        $this->emit('selectedsWasChanged', $this->teachers_selecteds);
        $this->start = false;
    }


    public function cancel()
    {
        $this->reset('teachers_selecteds', 'teachers_table');
        $this->emit('selectedsWasChanged', $this->teachers_selecteds);
        $this->start = false;
    }

    public function manageTeacherClasses($teacher_id)
    {
        $this->emit('manageTeacherClasses', $teacher_id);
    }


    public function updatedSearch($search)
    {
        if($search && mb_strlen($search) > 4){
            $this->search_target = $search;
            $this->emit('teacherTableListFetchOnSearch', $search);
        }
        else{
            $this->reset('search_target');
            $this->emit('teacherTableListFetchOnSearch', $search);
        }
        
    }

    public function resetSearch()
    {
        $this->reset('search');
        $this->emit('teacherTableListFetchOnSearch', '');
    }

   

    public function getList($list)
    {
        $this->teachers_selecteds = $list;
    }
}
