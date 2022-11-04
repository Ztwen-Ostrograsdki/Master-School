<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateNewClasse extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = ['createNewClasseLiveEvent' => 'createNewClasse'];
    public $name;
    public $level_id; 
    public $school_year_model;
    public $school_year;
    public $classe;

    protected $rules = [
        'name' => 'required|unique:classes|min:2',
    ];


    public function mount()
    {
        $this->school_year_model = $this->getSchoolYear();
    }

    public function render()
    {
        $levels = Level::all();
        $school_years = SchoolYear::all();
        $this->school_year = $this->school_year_model->id;
        return view('livewire.create-new-classe', compact('levels', 'school_years'));
    }


    public function createNewClasse()
    {
        $levels = Level::all();

        if (count($levels) > 0) {
            $this->level_id = Level::all()->shuffle()->first()->id;
            $this->dispatchBrowserEvent('modal-createNewClasse');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vous ne pouvez pas encore de créer de classe. Veuillez insérer d'abord des cycles d'études!", 'type' => 'error']);
        }
    }


    public function submit()
    {
        $this->validate();
        $school_year = SchoolYear::find($this->school_year);
        if($school_year){
            $level_id = $this->level_id;
            $db = DB::transaction(function($e) use ($school_year, $level_id){
                $level_existed = Level::find($level_id);
                if($level_existed){
                        $classe = Classe::create(
                        [
                            'name' => $this->name,
                            'slug' => str_replace(' ', '-', $this->name),
                            'level_id' => $level_id,
                        ]
                    );
                    if($classe){
                        $school_year->classes()->attach($classe->id);
                        $this->dispatchBrowserEvent('hide-form');
                        $level = str_replace('Le', '', $classe->level->getName());
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Création de classe terminée', 'message' => "la classe  $classe->name a été créé pour le cycle du $level avec succès!", 'type' => 'success']);
                        $this->emit('newClasseCreated', $classe->id);
                        $this->resetErrorBag();
                        $this->reset('name', 'level_id', 'school_year');
                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la classe a échoué", 'type' => 'error']);
                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la classe a échoué car le cycle d'études renseigné est introuvable!", 'type' => 'error']);
                }
                
            });

            if($db){
              
            }
            
            
        }
    }

}
