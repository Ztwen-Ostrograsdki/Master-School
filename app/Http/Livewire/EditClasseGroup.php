<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\ClasseGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditClasseGroup extends Component
{
    protected $listeners = ['editClasseGroupLiveEvent' => 'openModal'];

    public $classe_id;
    public $classe_group_id;
    public $classe;



    public function render()
    {
        $promotions = [];
        if($this->classe_id){
            $promotions = ClasseGroup::where('level_id', $this->classe->level_id)->get();
        }
        return view('livewire.edit-classe-group', compact('promotions'));
    }


    public function submit()
    {
        $updated = $this->classe->update(['classe_group_id' => $this->classe_group_id]);

        if($updated){
            $name = $this->classe->name;
            $this->dispatchBrowserEvent('hide-form');
            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La classe: $name a été mise à jour avec succès!", 'type' => 'success']);
            $this->emit('classeGroupUpdated');
            $this->emit('classeUpdated');
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une erreure inconnue s'est produite lors de la mise à jour,Veuillez donc réessayer!", 'type' => 'error']);
        }
    }


    public function openModal($classe_id)
    {
        if($classe_id){
            $classe = Classe::find($classe_id);
            if($classe){
                $this->classe = $classe;
                $this->classe_id = $classe_id;
                $this->dispatchBrowserEvent('modal-editClasseGroup');
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "La classe sélectionnée est introuvable. Veuillez sélectionner une classe valide!", 'type' => 'error']);

            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Veuillez sélectionner une classe!", 'type' => 'error']);

        }


    }


}
