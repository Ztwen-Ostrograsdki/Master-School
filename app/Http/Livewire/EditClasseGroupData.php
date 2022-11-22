<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\ClasseGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class EditClasseGroupData extends Component
{
    protected $listeners = ['editClasseGroupDataLiveEvent' => 'openModal'];

    public $classe_group_id;
    public $classe_group;
    public $name;
    public $category = '';
    public $option = '';
    public $filial = '';

    protected $rules = ['name' => 'required|string'];



    public function render()
    {
        return view('livewire.edit-classe-group-data');
    }


    public function submit()
    {
        $this->validate();
        $hasAlreadyNameTaken = ClasseGroup::where('name', $this->name)->whereId('<>', $this->classe_group->id)->count();

        if(!$hasAlreadyNameTaken){
            $updated = $this->classe_group->update(['name' => $this->name, 'category' => $this->category]);
            if($updated){
                $this->dispatchBrowserEvent('hide-form');
                $this->emit('classeGroupUpdated');
                $this->emit('classeUpdated');
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une erreure inconnue s'est produite lors de la mise à jour,Veuillez donc réessayer!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure FORMULAIRE', 'message' => "Le nom que vous avez choisir existe déjà!", 'type' => 'error']);
        }
    }


    public function openModal($classe_group_id)
    {
        if($classe_group_id){
            $classe_group = ClasseGroup::find($classe_group_id);
            if($classe_group){
                $this->classe_group = $classe_group;
                $this->name = $classe_group->name;
                $this->category = $classe_group->category;

                $this->dispatchBrowserEvent('modal-editClasseGroupData');
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "La promotion sélectionnée est introuvable. Veuillez sélectionner une promotion valide!", 'type' => 'error']);

            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Veuillez sélectionner une promotion!", 'type' => 'error']);

        }


    }


}
