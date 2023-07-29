<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\Level;
use App\Models\Subject;
use Livewire\Component;

class TeacherListing extends Component
{
    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadData'];

    public $level_id_selected = null;
    public $classe_id_selected = null;
    public $subject_id_selected = null;
    public $counter = 0;
    public $baseRoute = 'teacher_listing';
    public $search = '';


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
        
        return view('livewire.teacher-listing', compact('classes', 'levels', 'subjects'));
    }

    public function setTeachersActiveSection($section)
    {
        // $this->classe_id = $section;
        session()->put('teachers_section_selected', $section);
    }


    public function updatedSearch($search)
    {
        $this->search = $search;

        if($search && strlen($search) > 2){

            $this->emit('TeacherTableListFetchOnSearch', $search);

        }
        else{

            $this->emit('TeacherTableListFetchOnSearch', null);
        }

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

    public function getClasses()
    {
        $level_id = $this->level_id_selected;

        if($level_id){
            $classes = Classe::where('level_id', $level_id)->get();
        }
        else{
            $classes = Classe::all();
        }

        return $classes;

    }

    public function getSubjects()
    {
        $level_id = $this->level_id_selected;

        if($level_id){
            $subjects = Subject::where('level_id', $level_id)->get();
        }
        else{
            $subjects = Subject::all();
        }

        return $subjects;

    }


    public function reloadData()
    {
        $this->counter = rand(0, 15);
    }
}
