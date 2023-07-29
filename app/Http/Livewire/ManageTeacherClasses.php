<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\TeacherCursus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageTeacherClasses extends Component
{
    protected $listeners = ['manageTeacherClasses' => 'openModal'];
    public $teacher;
    public $school_year_model;
    // public $classes_selecteds = [];
    public $target;
    public $title = 'Le prof...';

    use ModelQueryTrait;


    public function render()
    {
        $classes_selecteds = [];

        $classes = [];

        if($this->target){

            $classes = $this->school_year_model->classes()->where('level_id', $this->target->level_id)->where('classes.name', 'not like', "%polyvalente%")->orderBy('classes.name', 'asc')->get();

            $teacher_classes = $this->teacher->getTeachersCurrentClasses();

            foreach($teacher_classes as $classe_id =>  $cl){

                $classes_selecteds[] = $classe_id;
            }
        }

        return view('livewire.manage-teacher-classes', compact('classes', 'classes_selecteds'));
    }


    public function join($classe_id)
    {
        $classe = $this->school_year_model->findClasse($classe_id);

        if($classe){

            if($classe->level_id == $this->target->level_id && $classe->classe_group->hasThisSubject($this->target->speciality()->id)){
                
                if(!in_array($classe->id, $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->whereNull('end')->pluck('classe_id')->toArray())){
                    
                    $has_already_teacher_for_this_subject = $classe->hasAlreadyTeacherForThisSubject($this->target->speciality()->id, $this->school_year_model->id);

                    if(!$has_already_teacher_for_this_subject){

                        DB::transaction(function($e) use ($classe){
                            try {

                                $joined = $classe->teachers()->attach($this->target->id);

                                try {
                                    $cursus = TeacherCursus::create([
                                        'classe_id' => $classe->id,
                                        'subject_id' => $this->target->speciality()->id,
                                        'teacher_id' => $this->target->id,
                                        'school_year_id' => $this->school_year_model->id,
                                        'level_id' => $this->target->level_id,
                                        'start' => Carbon::now(),
                                    ]);

                                    if($cursus){

                                        $this->school_year_model->teacherCursus()->attach($cursus->id);

                                        // $classe->timePlans()->where('time_plans.school_year_id', $this->school_year_model->id)->where('time_plans.subject_id', $this->target->speciality()->id)->each(function($tp){

                                        //     // Ralier les emplois du temps si le prof est dispo pendanst les horaires de ces emplois du temps 
                                        //     // $tp->update(['teacher_id' => $this->target->id]);

                                        // });
                                    }
                                    else{

                                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "La mise à jour n'a pas été effective!", 'type' => 'error']);

                                    }
                                    
                                } catch (Exception $ee) {

                                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur Serveur niveau 1', 'message' => "Une erreure inconnue est survenue veuillez réessayer dans quelques secondes!", 'type' => 'warning']);
                                }
                                
                            } catch (Exception $e) {

                                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur Serveur niveau 2', 'message' => "Une erreure inconnue est survenue veuillez réessayer dans quelques secondes!", 'type' => 'warning']);
                            }

                        });

                        DB::afterCommit(function(){

                            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La liste des classes de $this->title a été mise à jour avec succès!", 'type' => 'success']);

                            $this->emit('userDataEdited');

                        });

                    }
                    else{

                        $cursus = $has_already_teacher_for_this_subject;

                        $taking = $cursus->teacher->getFormatedName();

                        $subject = $this->target->speciality()->name;

                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'PROF DEJA DISPONBLE POUR CETTE MATIERE', 'message' => "Cette classe a déja un enseignant de $subject. Il s'agit de Mr/Mme $taking !", 'type' => 'warning']);

                    }
                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Cette classe est déjà définie pour $this->title!", 'type' => 'question']);
                }
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter car cette classe n'est pas compatible avec cette ce prof ! Vérifiez que la matière du prof est bien enseignée dans cette classe et réessayez!", 'type' => 'error']);
            }

        }
           
    }


    public function disjoin($classe_id)
    {

        $classe = $this->school_year_model->findClasse($classe_id);

        if($classe){

            if($classe->level_id == $this->target->level_id && $classe->classe_group->hasThisSubject($this->target->speciality()->id)){

                if(in_array($classe->id, $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->whereNull('end')->pluck('classe_id')->toArray())){

                    DB::transaction(function($e) use ($classe){

                        try {

                            try {

                                $cursus = $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->where('classe_id', $classe->id)->whereNull('end')->first();

                                if($cursus){

                                    $this->school_year_model->teacherCursus()->detach($cursus->id);

                                    $cursus->update(['end' => Carbon::now()]);
                                }
                                else{

                                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "La mise à jour n'a pas été effective!", 'type' => 'error']);

                                }
                                
                            } catch (Exception $ee) {

                                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur Serveur niveau 1', 'message' => "Une erreure inconnue est survenue veuillez réessayer dans quelques secondes!", 'type' => 'warning']);
                            }
                            
                        } catch (Exception $e) {

                            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur Serveur niveau 2', 'message' => "Une erreure inconnue est survenue veuillez réessayer dans quelques secondes!", 'type' => 'warning']);
                        }

                    });

                    DB::afterCommit(function(){

                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La liste des classes de $this->title a été mise à jour avec succès!", 'type' => 'success']);

                        $this->emit('userDataEdited');

                    });
                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Cette classe est déjà définie pour $this->title!", 'type' => 'question']);
                }
            }
            else{
                
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter car cette classe n'est pas compatible avec cette ce prof ! Vérifiez que la matière du prof est bien enseignée dans cette classe et réessayez!", 'type' => 'error']);
            }
        }
        
    }

    public function reloadData()
    {
        $this->counter = 1;
    }



    public function openModal($teacher_id, $school_year = null)
    {
        $this->school_year_model = $this->getSchoolYear();

        $teacher = $this->school_year_model->findTeacher($teacher_id);

        $this->teacher = $teacher;

        $this->target = $this->teacher;

        $this->title = "Prof " . $teacher->name . ' ' . $teacher->surname;

        $this->dispatchBrowserEvent('modal-manageTeacherClasses');
    }


    public function hideForm()
    {
        $this->dispatchBrowserEvent('hide-form');
    }
}
