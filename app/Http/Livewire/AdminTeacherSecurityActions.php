<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\Level;
use App\Models\Subject;
use Livewire\Component;

class AdminTeacherSecurityActions extends Component
{
    protected $listeners = ['updatedTeachersSelectedsList' => 'getList'];
    public $level_id_selected = null;
    public $classe_id_selected = null;
    public $subject_id_selected = null;
    public $counter = 0;
    public $start = false;
    public $teachers_selecteds = [];

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
        $this->start = true;

    } 

    public function cancel()
    {
        $this->start = false;

    }

   

    public function getList($list)
    {
        $this->teachers_selecteds = $list;
    }
}
