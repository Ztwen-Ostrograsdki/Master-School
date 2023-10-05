<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AE;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateSubject extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = ['createNewSubjectLiveEvent' => 'openModal', 'UpdateSubjectDataLiveEvent' => 'openModalForUpdate'];
    public $title = "Creation d'une nouvelle matière";
    public $name;
    public $school_year_model;
    public $school_year;
    public $level_id;
    public $teacher_id;
    public $teacher;
    public $subject_id;
    public $subject;
    public $updating_success = false;
    public $message = "";
    public $updating = false;
    public $joined = true;

    protected $rules = [
        'name' => 'required|unique:subjects|min:2',
        'level_id' => 'required|int',
    ];

    public function render()
    {
        $levels = Level::all();
        $teachers = [];
        if($this->updating){
            $teachers = $this->subject->teachers;
        }
        return view('livewire.create-subject', compact('levels', 'teachers'));
    }

    public function openModalForUpdate($subject_id)
    {
        $school_year_model = $this->getSchoolYear();
        $subject  = $school_year_model->subjects()->where('subjects.id', $subject_id)->first();

        if ($subject) {
            $this->title = "Edition de la matière " . $subject->name;
            $this->updating = true;
            $this->subject = $subject;
            $this->subject_id = $subject_id;
            $this->level_id = $subject->level_id;
            $this->teacher_id = $subject->ae ? $subject->ae->teacher_id : null;
            $this->name = $subject->name;
            $this->dispatchBrowserEvent('modal-createNewSubject');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'MATIERE INTROUVABLE', 'message' => "La matière renseignée est introuvable ou a été supprimé momentanément!", 'type' => 'error']);
        }
    }


    public function openModal()
    {
        $levels = Level::all();

        if (count($levels) > 0) {
            $this->level_id = Level::all()->shuffle()->first()->id;
            $this->dispatchBrowserEvent('modal-createNewSubject');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vous ne pouvez pas encore de créer de matière. Veuillez insérer d'abord des cycles d'études!", 'type' => 'error']);
        }
    }


    public function submit()
    {
        $this->validate();

        $this->school_year_model = $this->getSchoolYear();
        $school_year_model = $this->school_year_model;

        $level = $school_year_model->levels()->where('levels.id', $this->level_id)->first();

        if($level){
            if(!$this->updating){
                $this->subjectCreator();
            }
            else{
                $this->subjectUpdator();
            }
        }
        else{
            $this->addError('level_id', 'Le cycle est inexistant');
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE CYCLE', 'message' => "La Création de la matière a échoué car le cycle renseigné est introuvable!", 'type' => 'warning']);

        }
        
    }

    public function subjectUpdator()
    {
        $school_year_model = $this->school_year_model;
        $old_name = $school_year_model->subjects()->where('subjects.name', $this->name)->where('subjects.id', '<>', $this->subject_id)->get();
        DB::transaction(function($e) use ($school_year_model, $old_name){
            if(count($old_name) == 0){
                if($this->teacher_id){
                    $this->validate(['teacher_id' => 'required|int']);
                    $teacher = $school_year_model->teachers()->where('teachers.id', $this->teacher_id)->first();
                    if($teacher){
                        if($this->subject->name !== $this->name){
                            $update_name = $this->subject->update(['name' => $this->name]);
                            if($update_name){
                                if(!$teacher->ae){
                                    $old_ae = $school_year_model->aes()->where('subject_id', $this->subject_id)->where('teacher_id', $this->teacher_id)->first();
                                    if($old_ae){
                                        $detach = $school_year_model->aes()->detach($old_ae->id);
                                        $update_ae = $old_ae->update(['teacher_id' => $this->teacher_id]);
                                        
                                        if($detach && $update_ae){
                                            $school_year_model->aes()->attach($old_ae->id);

                                            $message = "La matière " . $this->name . " a été mise à jour avec succès avec " . $teacher->getFormatedName() . " comme AE!";

                                            $this->resetErrorBag();
                                            $this->dispatchBrowserEvent('hide-form');
                                            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à de la matière terminée', 'message' => $message, 'type' => 'success']);
                                            $this->resetor();
                                        }
                                        else{
                                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "La mise à jour de l'AE a échoué!", 'type' => 'error']);
                                        }
                                    }
                                    else{
                                        $created_ae = AE::create(['teacher_id' => $this->teacher_id, 'subject_id' => $this->subject_id, 'school_year_id' => $school_year_model->id]);
                                        
                                        if($created_ae){
                                            $school_year_model->aes()->attach($created_ae->id);

                                            $message = "La matière " . $this->name . " a été mise à jour avec succès avec " . $teacher->getFormatedName() . " comme AE!";

                                            $this->resetErrorBag();
                                            $this->dispatchBrowserEvent('hide-form');
                                            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à de la matière terminée', 'message' => $message, 'type' => 'success']);
                                            $this->resetor();
                                        }
                                        else{
                                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "La mise à jour de l'AE a échoué!", 'type' => 'error']);
                                        }
                                    }
                                    
                                }
                                else{
                                    $this->addError('teacher_id', 'Enseignant déjà AE');
                                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ENSEIGNANT DEJA AE', 'message' => "La mise à jour de la matière a échoué car l'AE renseigné est déjà AE d'une matière paraît-il!", 'type' => 'error']);
                                }

                            }
                            else{
                                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La mise à jour du nom de la matière a échoué!", 'type' => 'error']);
                            }
                        }
                        else{
                            if(!$teacher->ae){
                                $old_ae = $school_year_model->aes()->where('subject_id', $this->subject_id)->where('teacher_id', $this->teacher_id)->first();
                                if($old_ae){
                                    $detach = $school_year_model->aes()->detach($old_ae->id);
                                    $update_ae = $old_ae->update(['teacher_id' => $this->teacher_id]);
                                    
                                    if($detach && $update_ae){
                                        $school_year_model->aes()->attach($old_ae->id);

                                        $message = "La matière " . $this->name . " a été mise à jour avec succès avec " . $teacher->getFormatedName() . " comme AE!";
                                        $this->resetErrorBag();
                                        $this->dispatchBrowserEvent('hide-form');
                                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à de la matière terminée', 'message' => $message, 'type' => 'success']);
                                        $this->resetor();
                                    }
                                    else{
                                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "La mise à jour de l'AE a échoué!", 'type' => 'error']);
                                    }
                                }
                                else{
                                    $created_ae = AE::create(['teacher_id' => $this->teacher_id, 'subject_id' => $this->subject_id, 'school_year_id' => $school_year_model->id]);
                                    
                                    if($created_ae){
                                        $school_year_model->aes()->attach($created_ae->id);

                                        $message = "La matière " . $this->name . " a été mise à jour avec succès avec " . $teacher->getFormatedName() . " comme AE!";

                                        $this->resetErrorBag();
                                        $this->dispatchBrowserEvent('hide-form');
                                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à de la matière terminée', 'message' => $message, 'type' => 'success']);
                                        $this->resetor();
                                    }
                                    else{
                                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "La mise à jour de l'AE a échoué!", 'type' => 'error']);
                                    }
                                }
                                
                            }
                            else{
                                $this->addError('teacher_id', 'Enseignant déjà AE');
                                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ENSEIGNANT DEJA AE', 'message' => "La mise à jour de la matière a échoué car l'AE renseigné est déjà AE d'une matière paraît-il!", 'type' => 'error']);
                            }
                        }

                    }
                    else{
                        $this->addError('teacher_id', 'Enseignant introuvable');
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ENSEIGNANT INTROUVABLE', 'message' => "La mise à jour de la matière a échoué car l'AE renseigné est introuvable!", 'type' => 'error']);
                    }

                }
                else{
                    if($this->subject->name !== $this->name){
                        $update_name = $this->subject->update(['name' => $this->name]);
                        if($update_name){
                            $message = "La matière " . $this->name . " a été mise à jour avec succès!";
                            $this->resetErrorBag();
                            $this->dispatchBrowserEvent('hide-form');
                            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à de la matière terminée', 'message' => $message, 'type' => 'success']);
                            $this->resetor();
                        }
                        else{
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La mise à jour du nom de la matière a échoué!", 'type' => 'error']);
                        }
                    }
                }

            }
            else{
                $this->addError('name', "Nom déja existant!");
            }

        });



    }



    public function subjectCreator()
    {
        $school_year_model = $this->school_year_model;

        $old_name = $school_year_model->subjects()->where('subjects.name', $this->name)->get();

        DB::transaction(function($e) use ($school_year_model, $old_name){

            if(count($old_name) == 0){

                $subject = Subject::create(['name' => $this->name, 'level_id' => $this->level_id]);

                if($subject){

                    $this->dispatchBrowserEvent('hide-form');

                    $this->resetErrorBag();

                    if($this->joined){

                        $school_years = SchoolYear::all();

                        if (count($school_years) > 0) {

                            foreach ($school_years as $school_year) {

                                $school_year->subjects()->attach($subject->id);
                            }
                        }
                    }
                    else{

                        $school_year_model->subjects()->attach($subject->id);
                    }
                    $message = "La matière " . $this->name . " a été mise à jour avec succès!";

                    $this->resetErrorBag();

                    $this->dispatchBrowserEvent('hide-form');

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à de la matière terminée', 'message' => $message, 'type' => 'success']);
                    
                    $this->resetor();
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la matière a échoué car une erreure inconnue est survenue lors de la création!", 'type' => 'error']);
                }
            }
            else{
                $this->addError('name', "Nom déja existant!");
            }
        });
    }


    public function resetor()
    {
        $this->reset('name', 'joined', 'updating', 'teacher', 'subject', 'subject_id', 'level_id', 'teacher_id', 'updating_success');
        $this->emit('newSubjectCreated');
        $this->emit('subjectDataUpdated');
    }

}
