<?php

namespace App\Http\Livewire;

use App\Events\PreparingToCreateNewTeacherEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AddNewTeacher extends Component
{

    use ModelQueryTrait;

    protected $listeners = ['addNewTeacher' => 'openModal', 'UpdateTeacherPersoData' => 'updateTeacherPersoData', 'UpdateTeacherSubject' => 'updateTeacherSubject'];

    public $title = 'Insertion de nouvel enseignant';
    
    public $name;
    
    public $user;
    
    public $account;
    
    public $surname;
    
    public $contacts;
    
    public $edit_subject = false;
    
    public $updating = false;
    
    public $creating = false;
    
    public $teacher;
    
    public $subject_id;
    
    public $level_id = 1;
    
    public $school_year;
    
    public $marital_status;
    
    public $old_subject;
    
    public $email;
    
    public $nationality = 'Béninoise';


    protected $rules = [
        'name' => 'required|string',
        'surname' => 'required|string', 
        'school_year' => 'required', 
        'subject_id' => 'required', 
        'email' => 'required|email', 
        'level_id' => 'required'
    ];

    public function render()
    {
        $teachers = Teacher::all();

        $users = User::all();

        $subjects = Subject::all();

        $school_years = SchoolYear::all();

        $levels = Level::all();

        return view('livewire.add-new-teacher', compact('teachers', 'users', 'subjects', 'levels', 'school_years'));
    }


    public function openModal()
    {
        $school_year_model = $this->getSchoolYear();

        $this->school_year = $school_year_model->id;

        $this->creating = true;

        $this->dispatchBrowserEvent('modal-addNewTeacher');
    }


    public function editingGenerator($teacher_id, $updating, $edit_subject)
    {
        $school_year_model = $this->getSchoolYear();

        $teacher = $school_year_model->teachers()->where('teachers.id', $teacher_id)->first();

        if($teacher){

            $this->teacher = $teacher;

            $this->email = $teacher->user->email;

            $this->name = $teacher->name;

            $this->surname = $teacher->surname;

            $this->contacts = $teacher->contacts;

            $this->marital_status = $teacher->marital_status;

            $this->level_id = $teacher->level_id;

            $this->nationality = $teacher->nationality;

            $this->user_id = $teacher->user_id;

            $this->subject_id = $teacher->speciality()->id;

            $this->account = $teacher->user->id;

            $this->school_year = $school_year_model->id;

            if($updating){

                $this->updating = true;

                $this->edit_subject = false;

                $this->title = "Edition des informations de " . $teacher->getFormatedName();
            }
            elseif($edit_subject){

                $this->updating = false;

                $this->edit_subject = true;

                $this->old_subject = $teacher->speciality();

                $this->title = "Edition de la matière de " . $teacher->getFormatedName();
            }
            else{

                $this->reset('edit_subject', 'updating', 'title', 'old_subject');
            }
            $this->dispatchBrowserEvent('modal-addNewTeacher');
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ENSEIGNANT INTROUVABLE', 'message' => "Cet enseignant est inconnue! Ou le compte a été supprimé ou bloqué momentanement!", 'type' => 'info']);
        }
    }


    public function updateTeacherPersoData($teacher_id)
    {
        $this->editingGenerator($teacher_id, true, false);
    }
    

    public function updateTeacherSubject($teacher_id)
    {
        $this->editingGenerator($teacher_id, false, true);
    }





    public function updatedEmail($email)
    {
        $this->resetErrorBag('email');

        if($email && strlen($email) > 8){

            $user = User::where('email', $email)->first();

            if(!$user){

                $this->addError('email', "Cette adresse mail est inconnue! Veuillez demander à cet enseignant de créer d'abord un compte utilisateur!");
            }
            else{

                $this->user = $user;

                $this->account = $user->id;
            }
        }
    }


    public function updatedAccount($user_id)
    {
        $this->resetErrorBag('email');

        $this->reset('email', 'user');

        $user = User::find($user_id);

        if(!$user){

            $this->addError('email', "Cette adresse mail est inconnue! Veuillez demander à cet enseignant de créer d'abord un compte utilisateur!");
        }
        else{

            $this->user = $user;

            $this->email = $user->email;

            $this->surname = $user->pseudo;

            $this->account = $user->id;
        }
    }


    public function insert()
    {
        $this->validateOnly('email');

        $v = $this->validate();
        
        $exists = false;

        if($this->creating){

            $exists = Teacher::where('name', $this->name)->where('surname', $this->surname)->first();
        }
        elseif ($this->updating) {

            $exists = Teacher::where('name', $this->name)->where('surname', $this->surname)->where('teachers.id', '<>', $this->teacher->id)->first();
        }
        if($exists){

            $this->addError('name', "Cet enseignant a déjà été enregistré!");

            $this->addError('surname', "Cet enseignant a déjà été enregistré!");
        }
        else{
            $this->resetErrorBag('name', 'surname');

            $school_year_model = $this->getSchoolYear();

            $subject = Subject::find($this->subject_id);

            PreparingToCreateNewTeacherEvent::dispatch($this->contacts, $this->level_id, $this->name, $this->nationality, $this->marital_status, $school_year_model, $subject, $this->surname, $this->user, $this->updating, $this->edit_subject, $this->old_subject);

            $this->dispatchBrowserEvent('hide-form');

            $this->resetErrorBag();

            $name = strtoupper($this->name);

            $surname = ucwords($this->surname);

            $this->dispatchBrowserEvent('Toast', ['title' => 'OPERATION TERMINEE', 'message' => "Les données de l'enseignant $name $surname ont été mises à jour avec succès! ", 'type' => 'success']);

            $this->reset('name', 'surname', 'subject_id', 'contacts', 'school_year', 'nationality', 'level_id', 'marital_status', 'user', 'email', 'edit_subject', 'teacher', 'updating', 'creating', 'old_subject');

            $this->emit('UpdatedSchoolYearData');

        }

    }


    public function brouillon()
    {
        DB::transaction(function($e){

            if($this->creating){
                try {
                    $teacher = Teacher::create([
                        'name' => strtoupper($this->name),
                        'surname' => ucwords($this->surname),
                        'nationality' => $this->nationality,
                        'contacts' => $this->contacts,
                        'marital_status' => $this->marital_status,
                        'level_id' => $this->level_id,
                        'user_id' => $this->user->id

                    ]);

                    if($teacher){

                        $school_year_model = SchoolYear::find($this->school_year);

                        $subject = Subject::find($this->subject_id);

                        $school_year_model->teachers()->attach($teacher->id);

                        $subject->teachers()->attach($teacher->id);

                        $this->user->update(['teacher_id' => $teacher->id]);

                    }
                } catch (Exception $e) {

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'enseignant a échoué!", 'type' => 'error']);
                }

            }
            elseif ($this->updating) {

                $this->teacher->update([
                    'name' => strtoupper($this->name),
                    'surname' => ucwords($this->surname),
                    'nationality' => $this->nationality,
                    'contacts' => $this->contacts,
                    'marital_status' => $this->marital_status

                ]);
            }
            elseif ($this->edit_subject) {

                $update = $this->teacher->update([
                    'level_id' => $this->level_id,
                ]);

                if($update){

                    $school_year_model = SchoolYear::find($this->school_year);

                    $subject = Subject::find($this->subject_id);

                    $this->old_subject->teachers()->detach($this->teacher->id);

                    $subject->teachers()->attach($this->teacher->id);
                    
                    if($this->old_subject->id !== $this->subject_id){

                        $classes = $this->teacher->getTeachersCurrentClasses(true);

                        foreach($classes as $classe_id => $data){

                            $classe = $data['classe'];

                            $cursus = $data['cursus'];

                            $canMarkedAsWorked = $data['asWorkedDuration'];

                            DB::transaction(function($e) use ($classe, $cursus, $canMarkedAsWorked){
                                try {
                                    try {
                                        if($cursus){

                                            $cursus->update(['end' => Carbon::now(), 'teacher_has_worked' => $canMarkedAsWorked]);
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
                        }

                    }
                }

            }
        });

        DB::afterCommit(function(){

            

            
        });
    }



}
