<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Level;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class CreateNewClasseGroup extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = ['createNewClasseGroupLiveEvent' => 'CreateNewClasseGroup'];
    public $name;
    public $category;
    public $level_id; 
    public $classe_group;
    public $joining = false;

    protected $rules = [
        'name' => 'required|unique:classe_groups|min:2',
    ];

    public function render()
    {
        $levels = Level::all();
        return view('livewire.create-new-classe-group', compact('levels'));
    }


    public function CreateNewClasseGroup()
    {
        $levels = Level::all();

        if (count($levels) > 0) {
            $this->level_id = Level::all()->shuffle()->first()->id;
            $this->dispatchBrowserEvent('modal-createNewClasseGroup');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vous ne pouvez pas encore de créer de classe. Veuillez insérer d'abord des cycles d'études!", 'type' => 'error']);
        }
    }


    public function submit()
    {
        $this->validate();
        $level_id = $this->level_id;
        DB::transaction(function($e) use ($level_id){
            $level_existed = Level::find($level_id);
            if($level_existed){
                    $classe_group = ClasseGroup::create(
                    [
                        'name' => ucfirst($this->name),
                        'category' => ucwords($this->category),
                        'slug' => str_replace(' ', '-', $this->name),
                        'level_id' => $level_id,
                    ]
                );
                if($classe_group){
                    $school_year_model = $this->getSchoolYear();
                    $school_year_model->classe_groups()->attach($classe_group->id);

                    if($this->joining){
                        $name = $this->name;
                        $target = '%' . $name . '%';
                        $classes = Classe::where('level_id', $this->level_id)->whereNull('classe_group_id')->where('name', 'like', $target)->each(function($classe) use ($classe_group){
                            $joined = $classe->update(['classe_group_id' => $classe_group->id]);
                            if(!$joined){
                                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de jonction des classes à la promotion', 'message' => "La Création de la promotion s'est bien déroulée mais la jonction aux anciennes classes existantes a échoué", 'type' => 'error']);
                            }
                        });
                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la promotion a échoué", 'type' => 'error']);
                }
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "La Création de la classe a échoué car le cycle d'études renseigné est introuvable!", 'type' => 'error']);
            }
            
        });

        DB::afterCommit(function(){
            $this->dispatchBrowserEvent('hide-form');
            $this->dispatchBrowserEvent('Toast', ['title' => 'Création de la promotion terminée', 'message' => "la promotion a été créé pour le cycle avec succès!", 'type' => 'success']);
            $this->emit('newClasseGroupCreated');
            $this->resetErrorBag();
            $this->reset('name', 'level_id', 'classe_group');

        });
    }
}
