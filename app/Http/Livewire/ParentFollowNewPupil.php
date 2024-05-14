<?php

namespace App\Http\Livewire;

use App\Events\ParentRequestToFollowPupilEvent;
use App\Models\ParentRequestToFollowPupil;
use App\Models\Parentable;
use App\Models\Pupil;
use App\Rules\PasswordChecked;
use Livewire\Component;

class ParentFollowNewPupil extends Component
{
    protected $listeners = ['FollowMyPupilLiveEvent' => 'openModal'];


    public $parentable;

    public $target;

    public $code;

    public $default_key;

    public $lien;

    public $matricule;

    public $title = "Demande de suivi d'un apprenant";

    public $identify;

    public $to_confirm = false;


    protected $rules = [
        'code' => 'required|string',
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

            $this->default_key = $parentable->key;

            $this->dispatchBrowserEvent('modal-parentFollowPupil');
        }
        
    }


    public function submit()
    {
        $this->resetErrorBag();

        $this->validate(['code' => new PasswordChecked($this->default_key, false)]);

        $this->validate();

        $matricule = $this->matricule;

        $pupil = Pupil::where('pupils.matricule', $matricule)->orWhere('pupils.ltpk_matricule', $matricule)->orWhere('pupils.educmaster', $matricule)->first();

        if($pupil){

            $yet = ParentRequestToFollowPupil::where('pupil_id', $pupil->id)->where('parentable_id', $this->parentable->id)->first();

            if(!$yet){

                $this->target = $pupil;

                $this->to_confirm = true;

            }
            else{

                if($yet->authorized){

                    $this->addError('matricule', "Vous suivez déjà {$pupil->getName()}");

                    $this->dispatchBrowserEvent('Toast', ['title' => 'APPRENANT DEJA SUIVI', 'message' => "Vous suivez déjà {$pupil->getName()}!", 'type' => 'info']);

                }
                elseif($yet->refused){

                    $this->addError('matricule', "Accès refusé pour {$pupil->getName()}");

                    $this->dispatchBrowserEvent('Toast', ['title' => "ACCES REFUSE", 'message' => "Vous ne pouvez pas suivre {$pupil->getName()}, veuillez vous rapprocher de l'administration!", 'type' => 'info']);

                }
                else{

                    $this->addError('matricule', "Vous avez déjà fait cette demande de suivi");

                    $this->dispatchBrowserEvent('Toast', ['title' => 'DEMANDE DEJA ENVOYEE', 'message' => "Vous avez déjà fait une demande de suivi pour cet apprenant!", 'type' => 'error']);

                }

            }

        }
        else{

            $this->addError('matricule', "Aucune correspondance n'a été trouvé");

            $this->dispatchBrowserEvent('Toast', ['title' => 'MATRICULE NON CORRESPONDU', 'message' => "Aucun apprenant n'a correspondu au matricule de vous avez renseigné, Veuillez vérifier et réessayer!", 'type' => 'error']);

        }

        


    }


    public function confirm()
    {

        if($this->target && $this->parentable && $this->lien){

            $pupil = $this->target;

            $this->dispatchBrowserEvent('hide-form');

            $user = auth()->user();

            ParentRequestToFollowPupilEvent::dispatch($this->parentable, $pupil, $this->lien, false, $user);

            $this->reset('matricule', 'matricule', 'code', 'to_confirm');

            $this->resetErrorBag();


        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'UNE ERREURE EST SURVENUE', 'message' => "Une erreure est survenue lors du traitement des données, Veuillez réessayer!", 'type' => 'error']);

        }

    }


    public function to_cancel()
    {

    }
}
