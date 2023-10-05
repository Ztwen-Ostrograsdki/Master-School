<?php

namespace App\Http\Livewire;

use App\Events\ImportRegistredTeachersToTheCurrentYearEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherCursus;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AllTeacherLister extends Component
{
     protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'UpdatedGlobalSearch' => 'updatedSearch',
        'UpdatedSchoolYearData' => 'reloadData',
    ];

    public $counter = 0;

    public $count = 0;

    public $search = null;

    public $selecteds = [];

    public $hasErrors = false;

    public $classe_group_id;

    public $level = 'secondary';

    public $classe_id_selected = null;

    public $subject_id_selected = null;

    public $level_id;

    public $classe_id = null;

    public $subject_id = null;

    public $baseRoute;

    public $teaching = true;


    use ModelQueryTrait;

    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $school_year_befor_model = $this->getSchoolYearBefor();

        $lastYear = $this->getLastYear();

        if(session()->has('teacher_list_on_teaching')){

            $this->teaching = session('teacher_list_on_teaching');
        }

        $this->level_id = Level::where('name', $this->level)->first()->id;

        $classes = Classe::where('level_id', $this->level_id)->get();

        $subjects = Subject::where('level_id', $this->level_id)->get();

        if($this->search && mb_strlen($this->search) > 2){

            $target = '%' . $this->search . '%';

            $teachers = Teacher::where('level_id', $this->level_id)->where('teachers.name', 'like', $target)->orWhere('teachers.surname', 'like', $target)->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();

        }
        else{

            if(!$this->classe_id && session()->has('teacher_classe_list_selected') && session('teacher_classe_list_selected') !== null){
                
                $this->classe_id = session('teacher_classe_list_selected');

            }

            if(!$this->subject_id && session()->has('teacher_subject_list_selected') && session('teacher_subject_list_selected') !== null){
                
                $this->subject_id = session('teacher_subject_list_selected');

            }

            if(!$this->classe_id && !$this->subject_id){
                
                $teachers = Teacher::where('level_id', $this->level_id)->whereNotNull('teachers.id')->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();
            }
            else{
                $teachers = $this->getData();
            }

            session()->put('teacher_classe_list_selected', $this->classe_id_selected);

            session()->put('teacher_subject_list_selected', $this->subject_id_selected);

        }
        return view('livewire.all-teacher-lister', compact('teachers', 'subjects', 'classes', 'school_year_model', 'lastYear', 'school_year_befor_model'));
    }


    public function resetSelectedData()
    {
        $this->reset('classe_id', 'classe_id_selected', 'classe_group_id_selected', 'classe_group_id');

        session()->forget('teacher_classe_list_selected');

        session()->forget('teacher_subject_list_selected');

    }



    public function changeSection($teaching = null)
    {
        $teaching = boolval($teaching);

        session()->put('teacher_list_on_teaching', $teaching);


        $this->teaching = $teaching;
    }


    public function updatedSearch($search)
    {
        $this->search = $search;
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

        $classe_id = $this->classe_id;

        $level_id = $this->level_id;

        $subject_id = $this->subject_id;

        $teachers = [];

        if($level_id){

            $data = Teacher::where('level_id', $level_id)->orderBy('name', 'asc')->orderBy('surname', 'asc')->get();

            if($classe_id){

                if($subject_id){

                    foreach($data as $teacher){

                        $cursus = TeacherCursus::where('teacher_id', $teacher->id)->where('classe_id', $classe_id)->whereNull('end')->count();

                        if($teacher->speciality()->id == $subject_id && $cursus > 0){

                            $teachers[] = $teacher;
                        }

                    }
                }
                else{
                    foreach($data as $teacher){

                        $cursus = TeacherCursus::where('teacher_id', $teacher->id)->where('classe_id', $classe_id)->whereNull('end')->count();

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

        return $teachers;
    }



    public function updatedSelecteds($selecteds)
    {
        $this->emit('updatedTeachersSelectedsList', $this->selecteds);
    }

    public function reGetUpdatesOfSelecteds($selecteds)
    {
        $this->selecteds = $selecteds;
    }


    public function manageTeacherClasses($teacher_id)
    {
        $this->emit('manageTeacherClasses', $teacher_id);
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


    public function joinAll()
    {
        $this->reset('count');

       $teachers = Teacher::where('level_id', $this->level_id)->get();

        $school_year_model = $this->getSchoolYear();

        foreach($teachers as $teacher){

            $yet = $school_year_model->teachers()->where('teachers.id', $teacher->id)->first();

            DB::transaction(function($e) use ($yet, $school_year_model, $teacher){

                if(!$yet){

                    $school_year_model->teachers()->attach($teacher->id);

                    $this->count = $this->count + 1;

                }

            });

        }

        DB::afterCommit(function() use ($school_year_model){

            $count = $this->count;

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "Les données de $count enseignants relatives à l'année-scolaire $school_year_model->school_year ont été générées avec succès!", 'type' => 'success']);

                $this->emit('classeUpdated');

        });
    }


    public function join($teacher_id)
    {
        $school_year_model = $this->getSchoolYear();

        $teacher = Teacher::find($teacher_id);


        if($teacher){

            $yet = $school_year_model->teachers()->where('teachers.id', $teacher_id)->first();

            DB::transaction(function($e) use ($yet, $school_year_model, $teacher){

                if(!$yet && $teacher){

                    $school_year_model->teachers()->attach($teacher->id);

                    $name = $teacher->getFormatedName();

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "L'enseignant  $name a été mise à jour avec succès! Les données de cet enseignant sont désormais disponibles en $school_year_model->school_year !", 'type' => 'success']);

                    $this->emit('UpdatedSchoolYearData');
                }
                else{

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ATTENTION', 'message' => "Cet enseignant semble déjà être liée à l'année-scolaire $school_year_model->school_year !", 'type' => 'warning']);

                    $this->emit('UpdatedSchoolYearData');
                }

            });


        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ENSEIGNANT INTROUVABLE', 'message' => "L'enseignant renseigné n'existe pas dans la base de données !", 'type' => 'warning']);

        }


    }


    public function disjoinAll()
    {
        $this->reset('count');

        $teachers = Teacher::where('level_id', $this->level_id)->get();

        $school_year_model = $this->getSchoolYear();

        foreach($teachers as $teacher){

            $yet = $school_year_model->teachers()->where('teachers.id', $teacher->id)->first();

            DB::transaction(function($e) use ($yet, $school_year_model, $teacher){

                if($yet){

                    $teacher->teacherDeleter(false);

                    $this->count = $this->count + 1;

                }

            });

        }

        DB::afterCommit(function() use ($school_year_model){

            $count = $this->count;

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "Les données de $count enseignants relatives à l'année-scolaire $school_year_model->school_year ont été supprimé avec succès!", 'type' => 'success']);

                $this->emit('classeUpdated');

        });


    }


    public function disjoin($teacher_id)
    {
        $school_year_model = $this->getSchoolYear();

        $teacher = Teacher::find($teacher_id);

        if($teacher){

            $teacher->teacherDeleter(false);

            DB::afterCommit(function() use ($school_year_model, $teacher){

                $name = $teacher->getFormatedName();

                $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "Les données de l'enseignant $name relatives à l'année-scolaire $school_year_model->school_year ont été supprimé avec succès!", 'type' => 'success']);

                    $this->emit('UpdatedSchoolYearData');
            });

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ENSEIGNANT INTROUVABLE', 'message' => "L'enseignant renseigné n'existe pas dans la base de données !", 'type' => 'warning']);

        }

    }


    public function importLastYearTeachersToThisYear()
    {
        $user = auth()->user();

        $school_year_model = $this->getSchoolYear();

        if($user && $user->isAdminAs('master')){

            ImportRegistredTeachersToTheCurrentYearEvent::dispatch($user, $school_year_model);

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['type' => 'warning', 'title' => 'ACTION NON AUTHORISEE',  'message' => "Vous n'êtes pas authorisé à exécuter une telle opération!"]);

        }
    }


}
