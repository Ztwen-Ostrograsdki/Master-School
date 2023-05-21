<?php

namespace App\Http\Livewire;

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

    protected $listeners = ['addNewTeacher' => 'openModal'];
    public $name;
    public $user;
    public $account;
    public $surname;
    public $contacts;
    public $subject_id;
    public $level_id = 1;
    public $school_year;
    public $marital_status;
    public $email;
    public $nationality = 'Béninoise';


    protected $rules = ['name' => 'required|string', 'surname' => 'required|string', 'school_year' => 'required', 'subject_id' => 'required', 'email' => 'required|email', 'level_id' => 'required'];

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
        $this->dispatchBrowserEvent('modal-addNewTeacher');
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

        $exists = Teacher::where('name', $this->name)->where('surname', $this->surname)->first();

        if($exists){
            $this->addError('name', "Cet enseignant a déjà été enregistré!");
            $this->addError('surname', "Cet enseignant a déjà été enregistré!");
        }
        else{
            $this->resetErrorBag('name', 'surname');
            DB::transaction(function($e){
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

                    }
                } catch (Exception $e) {
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'enseignant a échoué!", 'type' => 'error']);
                }
            });

            DB::afterCommit(function(){
                $name = strtoupper($this->name);
                $surname = ucwords($this->surname);
                $this->dispatchBrowserEvent('hide-form');
                $this->resetErrorBag();
                $this->reset('name', 'surname', 'subject_id', 'contacts', 'school_year', 'nationality', 'level_id', 'marital_status', 'user', 'email');
                $this->dispatchBrowserEvent('Toast', ['title' => 'Inscription terminée', 'message' => "L'enseignant $name $surname a été ajouté à la base de données avec succès! ", 'type' => 'success']);
                $this->emit('newTeacherHasBeenAdded');

            });
        }

        
    }



}
