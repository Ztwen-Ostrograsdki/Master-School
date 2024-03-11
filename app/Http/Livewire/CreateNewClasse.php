<?php

namespace App\Http\Livewire;

use App\Events\CompletedClasseCreationEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\Responsible;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateNewClasse extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = ['createNewClasseLiveEvent' => 'openModal', 'UpdateClasseLiveEvent' => "openModalForUpdate"];

    public $name;

    public $level_id; 

    public $classe_group_id; 

    public $school_year_model;

    public $school_year;

    public $classe;

    protected $rules = [
        'name' => 'required|unique:classes|min:2',
        'classe_group_id' => 'required|bail',
    ];


    public function mount()
    {
        $this->school_year_model = $this->getSchoolYear();
    }

    public function render()
    {
        $levels = Level::all();

        $promotions = ClasseGroup::all();

        $school_years = SchoolYear::all();

        if($this->school_year_model){

            $this->school_year = $this->school_year_model->id;
        }
        return view('livewire.create-new-classe', compact('levels', 'school_years', 'promotions'));
    }


    public function openModalForUpdate($classe_id)
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

        if ($classe) {

            $this->classe = $classe;

            $this->level_id = $classe->level_id;

            $this->name = $classe->name;

            $this->classe_group_id = $classe->classe_group_id;

            $this->dispatchBrowserEvent('modal-createNewClasse');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La classe est introuvable!", 'type' => 'error']);
        }
    }


    public function openModal()
    {
        $levels = Level::all();

        $classe_groups = ClasseGroup::all();

        if (count($levels) > 0) {

            if (count($classe_groups) > 0) {

                $this->level_id = Level::all()->shuffle()->first()->id;

                $this->dispatchBrowserEvent('modal-createNewClasse');
            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vous ne pouvez pas encore de créer de classe. Veuillez insérer d'abord des promotions de classes!", 'type' => 'error']);
            }
        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vous ne pouvez pas encore de créer de classe. Veuillez insérer d'abord des cycles d'études!", 'type' => 'error']);
        }
    }


    public function submit()
    {
        if($this->classe){
            //IS UPDATING
            $this->validate(['name' => 'required|string', 'classe_group_id' => 'required', 'level_id' => 'required']);

            $name_was_existed = $school_year_model->classes()
                                                  ->where('classes.name', $this->name)
                                                  ->where('classes.id', '<>', $this->classe->id)
                                                  ->first();

            if($name_was_existed){

                $this->addError('name', "Cette classe existe déjà!");
            }
        }
        else{

            $match = preg_match('/polyvalente/', $this->name);

            if($match){

                $this->validate(['name' => 'required|string', 'classe_group_id' => 'required', 'level_id' => 'required']);
            }
            else{

                $this->validate();
            }

        }
        
        $school_year_model = SchoolYear::find($this->school_year);

        if($school_year_model){

            $level_id = $this->level_id;

            DB::transaction(function($e) use ($school_year_model, $level_id){

                $level_existed = Level::find($level_id);

                if($level_existed){

                    if($this->classe_group_id == 'polyvalente'){

                        $classe_group_id = null;
                    }
                    else{

                        $classe_group_id = $this->classe_group_id;
                    }
                    if($this->classe){

                        $classe = $this->classe->update([
                            'name' => trim(ucfirst($this->name)),
                            'slug' => str_replace(' ', '-', trim(ucfirst($this->name))),
                            'level_id' => $level_id,
                            'classe_group_id' => $classe_group_id
                        ]);

                        $position = $this->classe->getClassePosition();

                        if(!$this->classe->position || ($this->classe->position && $this->classe->position !== $position)){

                            $this->classe->update(['position' => $position]);

                        }

                    }
                    else{

                        $classe = Classe::create(
                            [
                                'name' => trim(ucfirst($this->name)),
                                'slug' => str_replace(' ', '-', trim(ucfirst($this->name))),
                                'level_id' => $level_id,
                                'classe_group_id' => $classe_group_id
                            ]
                        );
                        if($classe){

                            $user = auth()->user();

                            $school_year_model->classes()->attach($classe->id);

                            CompletedClasseCreationEvent::dispatch($classe, $school_year_model, $user);

                            $this->dispatchBrowserEvent('hide-form');
                        }
                        else{
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la classe a échoué", 'type' => 'error']);
                        }
                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la classe a échoué car le cycle d'études renseigné est introuvable!", 'type' => 'error']);
                }
                
            });

            DB::afterCommit(function(){

                $this->dispatchBrowserEvent('Toast', ['title' => 'Création de classe terminée', 'message' => "la classe a été créé pour le cycle avec succès, le processus va s'achever en arrière plan!", 'type' => 'success']);

                $this->emit('newClasseCreated');

                $this->resetErrorBag();

                $this->reset('name', 'level_id', 'school_year');

            });
        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la classe a échoué car l'année scolaire renseignée est introuvable!", 'type' => 'warning']);
        }
    }

}
