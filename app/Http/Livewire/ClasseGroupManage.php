<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClasseGroupManage extends Component
{
    use ModelQueryTrait;


    protected $listeners = ['manageClasseGroupLiveEvent'];
    public $classe_group;
    public $classe_group_id;
    public $classes = [];
    public $classes_tabs = [];
    public $counter = 0;

    protected $rules = [
        'classes' => 'required'
    ];

    public function render()
    {
        $groupes_pedagogiques = [];
        if($this->classe_group){
            $groupes_pedagogiques = Classe::where('level_id', $this->classe_group->level_id)->whereNull('classe_group_id')->orderBy('name', 'asc')->get();
            foreach($groupes_pedagogiques as $c){
                $this->classes_tabs[$c->id] = $c->name;
            }
        }
        return view('livewire.classe-group-manage', compact('groupes_pedagogiques'));
    }

    public function manageClasseGroupLiveEvent($classe_group_id)
    {
        $classe_group = ClasseGroup::find($classe_group_id);
        if($classe_group){
            $this->classe_group = $classe_group;
            $this->classe_group_id = $classe_group_id;
            $this->dispatchBrowserEvent('modal-manageClasseGroup');

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "La promotion renseignée est introuvable!", 'type' => 'error']);
        }
    }



    public function changeClasse($classe_id)
    {
        $this->counter = 1;
    }

    public function removeClasse($classe_id)
    {
        $classes = [];

        if($this->classes){
            foreach ($this->classes as $classe) {
                if(intval($classe) !== intval($classe_id)){
                    $classes[] = $classe;
                }
            }
        }
        $this->classes = $classes;
    }
    public function submit()
    {
        $this->validate();

        DB::transaction(function($e){
            Classe::whereIn('id', $this->classes)->each(function($classe){
                if(!$classe->classe_group_id){
                    $classe->update(['classe_group_id' => $this->classe_group_id]);
                }
            });
        });

        DB::afterCommit(function(){
            $this->dispatchBrowserEvent('hide-form');
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "La liste des classes de cette a été mise à jour avec succès!", 'type' => 'success']);
            $this->emit('classeGroupUpdated');

        });
    }
}
