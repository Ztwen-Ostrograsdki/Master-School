<?php

namespace App\Http\Livewire;

use App\Events\StartNewsPupilsInsertionEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassePupilSchoolYear;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\PupilCursus;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class AddNewPupil extends Component
{
    use ModelQueryTrait;
    protected $listeners = ['addNewPupilToClasseLiveEvent'];
    public $pupil;
    public $firstName;
    public $lastName;
    public $sexe = 'male';
    public $level_id;
    public $birth_day;
    public $contacts = '01010101';
    public $nationality = 'Béninoise';
    public $birth_city = 'Ville de naissance';
    public $residence = 'Ma résidence';
    public $last_school_from = 'Mon ancienne école';
    public $school_year_model;
    public $classe_id;
    public $classe;

    protected $rules = [
        'firstName' => 'required|string|min:2',
        'lastName' => 'required|string|min:2',
        'classe_id' => 'required|numeric',
        'contacts' => 'string|min:8',
        'sexe' => 'string',
        'birth_day' => 'date',
        'nationality' => 'string',
        'birth_city' => 'string',
        'level_id' => 'required|numeric',
        'residence' => 'string',
        'last_school_from' => 'required|string|min:2',
    ];


    public function mount()
    {
        $this->birth_day = (new \DateTime(Carbon::today()))->format('Y-m-d');
    }

    public function render()
    {
        $classes = [];

        $levels = Level::all();

        $this->school_year_model = $this->getSchoolYear();

        if($this->school_year_model){

            $classes = $this->school_year_model->classes;
        }
        return view('livewire.add-new-pupil', compact('levels', 'classes'));
    }


    public function addNewPupilToClasseLiveEvent($classe_id)
    {
        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();

        $this->school_year_model = $this->getSchoolYear();

        $this->classe = $classe;

        $this->classe_id = $classe->id;

        $this->level_id = $classe->level_id;

        $this->dispatchBrowserEvent('modal-addNewPupilToClasse');
    }


    public function submit()
    {
        $this->validate();

        if($this->classe_id){

            $classe = $this->school_year_model->findClasse($this->classe_id);

            if($classe->level_id == $this->level_id){

                $pupilNameHasAlreadyTaken = Pupil::where('lastName', $this->lastName)->where('firstName', $this->firstName)->first();

                if(!$pupilNameHasAlreadyTaken){
                    
                    $pupils = [];

                    $user = auth()->user();

                    $school_year_model = $this->school_year_model;

                    $data = [
                        'firstName' => trim(strtoupper($this->firstName)),
                        'lastName' => trim(ucwords($this->lastName)),
                        'classe_id' => $this->classe_id,
                        'contacts' => $this->contacts,
                        'sexe' => $this->sexe,
                        'birth_day' => $this->birth_day,
                        'nationality' => $this->nationality,
                        'birth_city' => $this->birth_city,
                        'level_id' => $this->classe->level_id,
                        'residence' => $this->residence,
                        'last_school_from' => $this->last_school_from

                    ];

                    $pupils[] = $data;

                    StartNewsPupilsInsertionEvent::dispatch($user, $school_year_model, $classe, $pupils);

                    $this->dispatchBrowserEvent('hide-form');

                    $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS LANCE', 'message' => "Le processus d'insertion de l'apprenant dans la base de données a été lancé et est en cours d'exécution!", 'type' => 'success']);

                    $this->resetErrorBag();

                    $this->reset('lastName', 'firstName', 'contacts', 'sexe', 'pupil', 'school_year_model', 'classe', 'classe_id', 'level_id');
                }
                else{
                    $this->addError('lastName', "Un apprenant est déjà inscrit sous ce nom et prénoms");

                    $this->addError('firstName', "Un apprenant est déjà inscrit sous ce nom et prénoms");
                }
            }
            else{
                $this->addError('level_id', 'Le cycle ne correspond pas à la classe sélectionnée');
            }
        }
    }

}
