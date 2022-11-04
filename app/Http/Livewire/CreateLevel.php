<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Level;
use App\Models\SchoolYear;
use Livewire\Component;

class CreateLevel extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = ['createNewLevelLiveEvent' => 'createNewLevel'];
    public $name;
    public $school_year_model;
    public $school_year;
    public $joined = true;
    public $other_level = null;

    protected $rules = [
        'name' => 'required|unique:levels|min:5',
    ];

    public function render()
    {
        $school_years = SchoolYear::all();
        return view('livewire.create-level', compact('school_years'));
    }


    public function createNewLevel()
    {
        $this->dispatchBrowserEvent('modal-createNewLevel');
    }


    public function submit()
    {
        $this->validate();
        if($this->name && $this->name !== null){
            $level = Level::create(['name' => $this->name]);
        }
        elseif($this->other_level && $this->other_level !== null){
            $level = Level::create(['name' => strtolower($this->other_level)]);
        }
        else{
            $level = null;
        }
        if($level){

            $this->dispatchBrowserEvent('hide-form');
            $this->resetErrorBag();
            $this->dispatchBrowserEvent('Toast', ['title' => 'Création du cycle terminée', 'message' => "le cycle  $level->name a été créé avec succès!", 'type' => 'success']);
            if($this->joined){
                $school_years = SchoolYear::all();
                if (count($school_years) > 0) {
                    foreach ($school_years as $school_year) {
                        $school_year->levels()->attach($level->id);
                    }
                }
            }
            else{
                $school_year_model = $this->getSchoolYear();
                $school_year_model->levels()->attach($level->id);
            }
            $this->reset('name', 'joined', 'other_level');
            $this->emit('newLevelCreated');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Ereur serveur', 'message' => "creation du cycle a échoué!", 'type' => 'error']);

        }
    }
}
