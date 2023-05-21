<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Level;
use App\Models\Teacher;
use Livewire\Component;

class TeacherTableList extends Component
{
    use ModelQueryTrait;

    protected $listeners = ['newTeacherHasBeenAdded' => 'reloadData', 'changeTeacherList' => 'reloadDataOnSection', 'userDataEdited' => 'reloadData'];
    public $counter = 0;
    public $classe_id = null;
    public $subject_id = null;
    public $level_id = null;
    public $selecteds = [];

    public function render()
    {
        if(!$this->level_id && session()->has('teacher_level_list_selected') && session('teacher_level_list_selected') !== null){
            $this->level_id = session('teacher_level_list_selected');

        }

        if(!$this->classe_id && session()->has('teacher_classe_list_selected') && session('teacher_classe_list_selected') !== null){
            $this->classe_id = session('teacher_classe_list_selected');

        }

        if(!$this->subject_id && session()->has('teacher_subject_list_selected') && session('teacher_subject_list_selected') !== null){
            $this->subject_id = session('teacher_subject_list_selected');

        }

        if(!$this->classe_id && !$this->level_id && !$this->subject_id){
            $teachers = Teacher::orderBy('name', 'asc')->orderBy('surname', 'asc')->get();
        }
        else{
            $teachers = $this->getData();
        }
        return view('livewire.teacher-table-list', compact('teachers'));
    }


    public function addNewTeacher()
    {
        $this->emit('addNewTeacher');
    }


    public function reloadData()
    {
        $this->counter = rand(1, 7);
    }


    public function reloadDataOnSection($level_id, $classe_id, $subject_id)
    {
        $this->level_id = $level_id;
        $this->subject_id = $subject_id;
        $this->classe_id = $classe_id;

    }

    public function getData()
    {
        $school_year_model = $this->getSchoolYear();

        $classe_id = $this->classe_id;
        $level_id = $this->level_id;
        $subject_id = $this->subject_id;

        $teachers = [];

        if($level_id){
            $level = Level::find($level_id);
            $data = $school_year_model->teachers()->where('level_id', $level_id)->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();

            if($classe_id){
                if($subject_id){
                    foreach($data as $teacher){
                        $cursus = $school_year_model->teacherCursus()->where('teacher_id', $teacher->id)->where('classe_id', $classe_id)->whereNull('end')->count();
                        if($teacher->speciality()->id == $subject_id && $cursus > 0){
                            $teachers[] = $teacher;
                        }

                    }
                }
                else{
                    foreach($data as $teacher){
                        $cursus = $school_year_model->teacherCursus()->where('teacher_id', $teacher->id)->where('classe_id', $classe_id)->whereNull('end')->count();
                        if($cursus > 0){
                            $teachers[] = $teacher;
                        }
                    }

                }
            }
            elseif(!$classe_id && $subject_id){
                foreach($data as $teacher){
                    if($teacher->speciality()->id == $subject_id){
                        $teachers[] = $teacher;
                    }
                }
            }
            elseif(!$classe_id && !$subject_id){
                $teachers = $data;
            }
        }
        elseif(!$level_id){
            if($classe_id){
                $teachers_ids = $school_year_model->teacherCursus()->where('classe_id', $classe_id)->whereNull('end')->pluck('teacher_id')->toArray();
                if($subject_id){
                    foreach($teachers_ids as $t){
                        if($t->subject_id == $subject_id){
                            $teachers = $school_year_model->teachers()->whereIn('teachers.id', $teachers_ids)->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();
                        }
                    }

                }
                else{
                    $teachers = $school_year_model->teachers()->whereIn('teachers.id', $teachers_ids)->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();
                }

            }
            elseif(!$classe_id && $subject_id){
                $data = $school_year_model->teachers()->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();

                foreach($data as $teacher){
                    if($teacher->speciality()->id == $subject_id){
                        $teachers[] = $teacher;
                    }
                }
            }
        }


        return $teachers;
    }


    public function toList($teacher_id)
    {

        $table = $this->selecteds;
        if(in_array($teacher_id, $table)){
            unset($table[$teacher_id]);
            $this->selecteds = $table;
        }
        else{
            $table[$teacher_id] = $teacher_id;
            $this->selecteds = $table;
        }

        $this->emit('updatedTeachersSelectedsList', $this->selecteds);

    }


    // public function updatedSelecteds($selecteds)
    // {
    //     $this->emit('updatedTeachersSelectedsList', $this->selecteds);
    // }


    public function manageTeacherClasses($teacher_id)
    {
        $this->emit('manageTeacherClasses', $teacher_id);
    }
}
