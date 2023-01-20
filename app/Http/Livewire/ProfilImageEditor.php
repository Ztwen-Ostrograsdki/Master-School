<?php

namespace App\Http\Livewire;

use App\Helpers\ZtwenManagers\ZtwenImageManager;
use App\Models\Pupil;
use Livewire\Component;
use Livewire\WithFileUploads;

class ProfilImageEditor extends Component
{
    public function render()
    {
        return view('livewire.profil-image-editor');
    }


    use WithFileUploads;
    public $the_model;
    public $profil_image = null;
    protected $listeners = ['editImageEvent' => 'openEditorModal'];
    protected $rules = [
        'profil_image' => 'image|max:3000|mimes:png,jpg,jpeg'
    ];


    
    public function updateImage()
    {
        $this->validate();
        $make = (new ZtwenImageManager($this->the_model, $this->profil_image))->storer($this->the_model->imagesFolder);
        if ($make) {
            $this->dispatchBrowserEvent('hide-form');
            $this->emit('pupilUpdated', $this->the_model->id);
            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "La photo de profil a été mis à jour avec succès", 'type' => 'success']);
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Ereur serveur', 'message' => "La mise à jour du profil a échoué, veuillez réessayer!", 'type' => 'error']);
        }
        
    }


    public function openEditorModal($the_model_id)
    {
        $the_model = Pupil::find($the_model_id);
        $this->the_model = $the_model;
        $this->dispatchBrowserEvent('modal-updateProfilImage');
    }

}
