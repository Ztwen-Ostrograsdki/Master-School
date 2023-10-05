<?php

namespace App\Http\Livewire;

use App\Events\StartNewsPupilsInsertionEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassePupilSchoolYear;
use App\Models\Level;
use App\Models\Pupil;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class MultiplePupilInsertion extends Component
{
    use ModelQueryTrait;
    protected $listeners = ['insertMultiplePupils' => 'openModal'];

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

    public $pupils = [];

    public $pupilsTableToShow = [];

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
        return view('livewire.multiple-pupil-insertion', compact('levels', 'classes'));
    }


    public function openModal($classe_id)
    {
        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();

        $this->school_year_model = $this->getSchoolYear();

        $this->classe = $classe;

        $this->classe_id = $classe->id;

        $this->level_id = $classe->level_id;

        $this->dispatchBrowserEvent('modal-insertMultiplePupils');
    }


    public function retrieveFrom($pupil_index = null)
    {
        $pupils = $this->pupils;

        if(count($pupils) > 0){

            if($pupil_index){

                unset($pupils[$pupil_index]);

            }
            else{

                $last = count($pupils);

                array_pop($pupils);

            }

            $this->pupils = $pupils;
        }


    }


    public function pushInto()
    {
        $pupils = $this->pupils;

        $counter = count($pupils);

        $this->resetErrorBag();

        $pupil_counter = 0;

        $data = [];

        $pupilNameHasAlreadyTaken = Pupil::where('lastName', $this->lastName)->where('firstName', $this->firstName)->first();

        if(!$pupilNameHasAlreadyTaken){

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

            $pupil_counter = $counter + 1;

            $pupils[$pupil_counter] = $data;

            $this->pupils = $pupils;

            $this->reset('lastName', 'firstName', 'contacts', 'sexe');

        }


    }


    public function submit()
    {
        $pupils = $this->pupils;

        if($pupils && count($pupils) > 0){

            $user = auth()->user();

            $school_year_model = $this->school_year_model;

            $classe = $this->classe;

            StartNewsPupilsInsertionEvent::dispatch($user, $school_year_model, $classe, $pupils);

            $this->dispatchBrowserEvent('hide-form');

            $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS LANCE', 'message' => "Le processus d'insertion des apprenants dans la base de données a été lancé et est en cours d'exécution!", 'type' => 'success']);

            $this->resetErrorBag();

            $this->reset('lastName', 'firstName', 'contacts', 'sexe', 'pupils', 'school_year_model', 'classe', 'classe_id', 'level_id');

        }

        
        

    }



}
