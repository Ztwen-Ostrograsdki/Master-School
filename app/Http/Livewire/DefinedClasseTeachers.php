<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Subject;
use App\Models\TeacherCursus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class DefinedClasseTeachers extends Component
{

    use ModelQueryTrait;

    protected $listeners = ['ManageClasseTeachers' => 'openModal'];

    public $classe;

    public $subject_id;

    public $subject;

    public $teacher_id;

    public $teacher;

    public $school_year_model;

    public $classe_teachers = [];

    public $subjects = [];

    public $data = [];

    public $dataToShow = [];

    public $confirmation = false;


    public function render()
    {

        $teachers = [];

        if($this->classe){

            $teachers = $this->school_year_model->teachers()->where('teachers.level_id', $this->classe->level_id)->get();

        }

        return view('livewire.defined-classe-teachers', compact('teachers'));
    }

    public function confirmed()
    {

        $data = $this->data;

        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        DB::transaction(function($e) use($data, $school_year_model, $classe){

            foreach($data as $subject_id => $datum){

                $teacher_id = $datum['teacher'];

                $cursus  = $classe->teacherCursus()
                                 ->where('teacher_cursuses.school_year_id', $school_year_model->id)
                                 ->where('teacher_cursuses.subject_id', $subject_id)
                                 ->whereNull('end')
                                 ->first();
                if($cursus){
                    //UPDATING

                    if($cursus->teacher_id !== $teacher_id){

                        $school_year_model->teacherCursus()->detach($cursus->id);

                        $cursus->update(['end' => Carbon::now()]);

                        $joined = $classe->teachers()->attach($teacher_id);

                        $new_cursus = TeacherCursus::create([
                            'classe_id' => $classe->id,
                            'subject_id' => $subject_id,
                            'teacher_id' => $teacher_id,
                            'school_year_id' => $school_year_model->id,
                            'level_id' => $classe->level_id,
                            'start' => Carbon::now()
                        ]);

                    }

                }
                else{

                    $joined = $classe->teachers()->attach($teacher_id);

                    $new_cursus = TeacherCursus::create([
                            'classe_id' => $classe->id,
                            'subject_id' => $subject_id,
                            'teacher_id' => $teacher_id,
                            'school_year_id' => $school_year_model->id,
                            'level_id' => $classe->level_id,
                            'start' => Carbon::now()
                        ]);


                }

                


            }



        });


        DB::afterCommit(function(){

            $this->cancel();

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "La définition des enseigannts de la classe a été défini avec succès!", 'type' => 'success']);

            $this->emit('GlobalDataUpdated');




        });

    }


    public function submit()
    {
        $this->confirmation = true;
    }


    public function openModal($classe_id)
    {
        $this->school_year_model = $this->getSchoolYear();

        $classe = $this->school_year_model->findClasse($classe_id);

        $data = [];

        $dataToShow = [];

        if ($classe) {

            $this->classe = $classe;

            $this->subjects = $classe->classe_group->subjects;

            foreach($this->subjects as $subject){

                $teacher = $classe->getClasseCurrentTeachers(false, $subject->id);

                if($teacher){

                    $teacher_name = $teacher->getFormatedName();

                    $subject_name = $subject->name;

                    $data[$subject->id] = ['teacher' => $teacher->id];

                    $dataToShow[$subject->id] = ['subject' => $subject_name, 'teacher' => $teacher_name];

                }

                

            }

            $this->data = $data;

            $this->dataToShow = $dataToShow;
            
            $this->dispatchBrowserEvent('modal-definedClasseTeachers');
        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE CLASSE INTROUVABLE', 'message' => "La classe renseignée est introuvable!", 'type' => 'error']);
        }
    }


    public function pushIntoData()
    {
        $data = $this->data;

        $dataToShow = $this->dataToShow;

        $teacher_name = $this->teacher->getFormatedName();

        $subject_name = $this->subject->name;

        if(isset($data[$this->subject_id]) && array_key_exists($this->subject_id, $data)){

            unset($data[$this->subject_id]);

            unset($dataToShow[$this->subject_id]);

            $data[$this->subject_id] = ['teacher' => $this->teacher->id];

            $dataToShow[$this->subject_id] = ['subject' => $subject_name, 'teacher' => $teacher_name];

        }
        else{

            $data[$this->subject_id] = ['teacher' => $this->teacher->id];

            $dataToShow[$this->subject_id] = ['subject' => $subject_name, 'teacher' => $teacher_name];

        }

        $this->data = $data;

        $this->dataToShow = $dataToShow;

        $this->reset('subject', 'subject_id', 'teacher', 'teacher_id');

    }

    public function retrieveFromData($subject_id)
    {
        $data = $this->data;

        $dataToShow = $this->dataToShow;

        unset($data[$subject_id]);

        unset($dataToShow[$subject_id]);

        $this->data = $data;

        $this->dataToShow = $dataToShow;

    }


    public function updatedTeacherId($teacher_id)
    {
        if($teacher_id){

            $teacher = $this->school_year_model->findTeacher($teacher_id);

            if($teacher){

                $this->teacher = $teacher;

                $this->teacher_id = $teacher->id;

            }

        }
        else{

            $this->reset('teacher_id');

        }

    }

    public function updatedSubjectId($subject_id)
    {
        $this->reset('teacher_id');

        if($subject_id){

            $subject = Subject::find($subject_id);

            if($subject){

                $this->subject = $subject;

                $teacher = $this->classe->getClasseCurrentTeachers(false, $subject_id);

                if($teacher){

                    $this->teacher = $teacher;

                    $this->teacher_id = $teacher->id;

                }
                else{
                    $this->reset('teacher_id');
                }

            }

        }
        else{
            $this->reset('subject_id');
        }
    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');
        
        $this->reset('subject_id', 'subject', 'teacher_id', 'teacher', 'data', 'dataToShow', 'confirmation');

    }


    public function edit($subject_id = null)
    {
        if($subject_id){

            $this->subject_id = $subject_id;

        }

        $this->confirmation = false;
    }

}
