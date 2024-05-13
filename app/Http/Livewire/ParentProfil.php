<?php

namespace App\Http\Livewire;

use App\Events\NewAddParentRequestEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Parentable;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class ParentProfil extends Component
{
    use ModelQueryTrait;

    public $counter = 0;

    public $parentable;

    public $pupils;

    public $user;

    public $user_id;

    public $account;

    public $residence;

    public $contacts;

    public $job;

    public $name;

    protected $listeners = [
        'UpdateParentableEvent' => 'updateParentable', 
        'NotifyMeWhenMyRequestAccepted' => 'reloadData',
        'UpdateParentAccountAfterDeleted' => 'parentAccoutDeleted',
        'UpdateParentAccountAfterBlocked' => 'reloadData',
    ];

    protected $rules = [

        'job' => 'required|string',
        'residence' => 'required|string',
        'name' => 'required|string',
        'contacts' => 'required|string',

    ];

    public function mount($id)
    {
        if($id){

            $this->user_id = $id;

            $this->user = User::find($id);

        }

    }


    public function render()
    {

        $parent_requests = [];

        $auth = auth()->user();

        if($this->user->parentable){
                
            $this->parentable = $this->user->parentable;

            $my_requests = $this->parentable->parentRequests;

            $parent_requests = $my_requests;
        }

        $teacher = $this->user->teacher;

        if($teacher){

            $this->name = $teacher->getFormatedName();

            $this->contacts = $teacher->contacts;

            $this->residence = $teacher->residence;

        }

        return view('livewire.parent-profil', compact('auth', 'parent_requests'));
    }


    public function register()
    {
        $this->resetErrorBag();

        $this->validate();

        $existed = Parentable::where('name', $this->name)->where('contacts', 'like', '%' . $this->contacts .'%')->first();

        if(!$existed){

                DB::transaction(function($e){

                    $make = $this->user->parentable_creator($this->contacts, $this->job, $this->name, $this->residence);

                    if($make){

                        broadcast(new NewAddParentRequestEvent());

                        $this->emit("UpdateParentableEvent", $make);

                        $this->dispatchBrowserEvent('Toast', ['title' => 'DEMANDE ENVOYEE AVEC SUCCES', 'message' => "Votre demande a bien été envoyée! Elle sera traitée d'ici peu!", 'type' => 'success']);

                        $this->resetErrorBag();

                        $this->reset('name', 'job', 'contacts', 'residence');

                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'UNE ERREURE EST SURVENUE', 'message' => "Votre demande n'a pu être soumise. Veuillez vérifier votre formulaire!", 'type' => 'error']);

                    }
                });

        }
        else{
            $this->addError('name', 'Déjà existant');

            $this->addError('contacts', 'Déjà existant');

            $this->dispatchBrowserEvent('Toast', ['title' => 'UNE ERREURE EST SURVENUE', 'message' => "Votre demande n'a pu être soumise car ses données existent déjà. Veuillez vérifier votre formulaire!", 'type' => 'error']);

        }
    }

    public function updateParentable($parentable)
    {
        $this->parentable = $parentable;
    }

    public function followMyPupil()
    {
        $this->emit('FollowMyPupilLiveEvent', $this->user->parentable->id);
    }

    public function reloadData($user = null)
    {
        if($user){

            $this->emit("UpdateParentableEvent", $user);
        }

        $this->counter = rand(1, 12);
    }

    public function parentAccoutDeleted()
    {
        return redirect(route('parent_profil', ['id' => $this->user->id]));
    }
}
