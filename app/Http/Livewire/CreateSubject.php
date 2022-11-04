<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateSubject extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = ['createNewSubjectLiveEvent' => 'createNewSubject'];
    public $name;
    public $school_year_model;
    public $school_year;
    public $level_id;
    public $joined = true;

    protected $rules = [
        'name' => 'required|unique:subjects|min:2',
    ];

    public function render()
    {
        $levels = Level::all();
        return view('livewire.create-subject', compact('levels'));
    }


    public function createNewSubject()
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
        $level_id = $this->level_id;

        $level = Level::find($level_id);
        if($level){
            DB::transaction(function($e) use ($level){
                $subject = Subject::create(['name' => $this->name, 'level_id' => $level->id]);
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
                        $school_year_model = $this->getSchoolYear();
                        $school_year_model->subjects()->attach($subject->id);
                    }
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Création de la matière terminée', 'message' => "la matière  $subject->name a été créé avec succès!", 'type' => 'success']);
                    $this->reset('name', 'joined');
                    $this->emit('newSubjectCreated');
                }
            });
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la matière a échoué car le cycle d'études renseigné est introuvable!", 'type' => 'error']);

        }
    }

}
