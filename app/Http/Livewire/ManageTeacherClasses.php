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
    public $target;
    public $title = 'Le prof...';

    use ModelQueryTrait;


    public function render()
    {
        $classes_selecteds = [];
        $classes = [];
        if($this->target){
            $classes = $this->school_year_model->classes()->where('level_id', $this->target->level_id)->orderBy('classes.name', 'asc')->get();
            $classes_selecteds = $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->whereNull('end')->pluck('classe_id')->toArray();
        }
        return view('livewire.manage-teacher-classes', compact('classes', 'classes_selecteds'));
    }


   public function removeclasses($classes_id)
    {
        $classess_selecteds = [];

        if($this->classess_selecteds){
            foreach ($this->classess_selecteds as $classes) {
                if(intval($classes) !== intval($classes_id)){
                    $classes_selecteds[] = $classes;
                }
            }
        }
        $this->classes_selecteds = $classes_selecteds;
    }



    public function join($classe_id)
    {
        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
        if($classe){
            if($classe->level_id == $this->target->level_id && $classe->classe_group->hasThisSubject($this->target->speciality()->id)){
                if(!in_array($classe->id, $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->whereNull('end')->pluck('classe_id')->toArray())){
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


    public function disjoin($classe_id)
    {

        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
        if($classe){
            if($classe->level_id == $this->target->level_id && $classe->classe_group->hasThisSubject($this->target->speciality()->id)){
                if(in_array($classe->id, $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->whereNull('end')->pluck('classe_id')->toArray())){
                    DB::transaction(function($e) use ($classe){
                        try {

                            try {
                                $cursus = $this->school_year_model->teacherCursus()->where('teacher_id', $this->target->id)->where('classe_id', $classe_id)->whereNull('end')->first();

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

        $teacher = $this->school_year_model->teachers()->where('teachers.id', $teacher_id)->first();

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
