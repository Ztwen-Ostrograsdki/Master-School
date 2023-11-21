<?php

namespace App\Http\Livewire;

use App\Models\Parentable;
use App\Models\Pupil;
use Livewire\Component;

class ParentFollowNewPupil extends Component
{
    protected $listeners = ['FollowMyPupilLiveEvent' => 'openModal'];


    public $parentable;

    public $target;

    public $auth_key;

    public $lien;

    public $matricule;

    public $title = "Demande de suivi d'un apprenant";

    public $identify;

    public $to_confirm = false;


    protected $rules = [
        'auth_key' => 'required|string',
        'matricule' => 'required|string',
        'lien' => 'required|string',
    ];

    public function render()
    {
        $liens = config('app.parentale_relations');

        return view('livewire.parent-follow-new-pupil', compact('liens'));
    }


    public function openModal($parentable_id)
    {
        $parentable = Parentable::find($parentable_id);

        if($parentable){

            $this->parentable = $parentable;

            $this->dispatchBrowserEvent('modal-parentFollowPupil');
        }
        
    }


    public function submit()
    {

        $this->validate();

        


    }


    public function confirm()
    {


    }


    public function to_cancel()
    {

    }
}
