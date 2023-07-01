<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\School;
use App\Models\Teacher;
use App\Models\User;
use Livewire\Component;

class TeacherProfilAsUser extends Component
{
    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadData'];
    use ModelQueryTrait;

    public $user;
    public $slug;
    public $classe_id;
    public $counter = 0;
    public $semestre_type = 'Semestre';
    public $section_to_display = 'marks';
    public $school_year;
    public $semestre_selected = 1;
    public $search = null;
    public $titles = [
                        'liste' => 'La liste de la classe',
                        'marks' => 'Les notes de la classe',
                        'related_marks' => 'Gestion bonus/sanctions de la classe',
                        'classe_general_stats' => 'Statistique de la classe',
                        'absences' => 'Gestion absences/présences de la classe'
                    ];



    public function render()
    {
        $classe = null;
        $classe_subject_coef = null;

        $school = School::first();
        $semestres = [1, 2];
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        $school_year_model = $this->getSchoolYear();
        $school_year = session('school_year_selected');
        
        $classe = $school_year_model->classes()->where('slug', urldecode($this->slug))->first();

        if(session()->has('teacher_user_active_section') && session('teacher_user_active_section')){
            $section_to_display = session('teacher_user_active_section');
            session()->put('teacher_user_active_section', $section_to_display);
            $this->section_to_display = $section_to_display;
        }

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }
        else{
            $this->semestre_selected = 1;
            session()->put('semestre_selected', $this->semestre_selected);
        }


        if(session()->has('semestre_type') && session('semestre_type')){
            $semestre_type = session('semestre_type');
            session()->put('semestre_type', $semestre_type);
            $this->semestre_type = $semestre_type;
        }
        else{
            session()->put('semestre_type', $this->semestre_type);
        }

        if($classe && $this->user && $this->user->teacher){
            $subject_id = $this->user->teacher->speciality()->id;
            session()->put('classe_subject_selected', $subject_id);
            $classe_subject_coef = $classe->get_coefs($subject_id, $school_year_model->id, true);
        }

        return view('livewire.teacher-profil-as-user', compact('classe', 'semestres', 'classe_subject_coef'));
    }


    public function mount($id, $slug)
    {
        $auth = auth()->user();
        $teacher = Teacher::find($id);
        $this->slug = $slug;

        $this->user = $teacher->user;
    }


    public function changeSemestre()
    {
        $this->count = 1;
        session()->put('semestre_selected', $this->semestre_selected);
        $this->emit('semestreWasChanged', $this->semestre_selected);
    }

    public function changeSection()
    {
        session()->put('teacher_user_active_section', $this->section_to_display);
    }


    public function reloadData()
    {
        $this->counter = rand(0, 21);
    }



}
