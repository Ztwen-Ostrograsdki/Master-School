<?php

namespace App\Http\Livewire;

use App\Events\ParentAccountBlockedEvent;
use App\Events\ParentAccountDeletedEvent;
use App\Events\ParentRequestAcceptedEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobSentParentKeyToParentableUser;
use App\Models\Parentable;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use Livewire\Component;

class ParentsListerComponent extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'UpdatedGlobalSearch' => 'updatedSearch',
        'pupilUpdated' => 'reloadData',
        'UpdatedSchoolYearData' => 'reloadData',
        'GlobalDataUpdated' => 'reloadData',
        'NewParentRequest' => 'newParent',
    ];

    public $counter = 0;

    public $search = null;



    use ModelQueryTrait;

    public function render()
    {
        $parents = Parentable::all();

        $professions = config('app.professions');

        return view('livewire.parents-lister-component', compact('parents', 'professions'));
    }


    public function updatedSearch($value)
    {
        $this->search = $value;
    }
    
    public function reloadData()
    {
        $this->counter = rand(1, 23);
    }

    public function delete($user_id)
    {
        $user = User::find($user_id);

        if($user){

            $parentable = $user->parentable;

            if($parentable){

                $parentable->delete();

                broadcast(new ParentAccountDeletedEvent($user));

                $this->reloadData();
            }

        }
    }

    public function lock($parentable_id)
    {
        $parentable = Parentable::find($parentable_id);

        if($parentable){

            $parentable->update(['authorized' => 0]);

            broadcast(new ParentAccountBlockedEvent($parentable->user));

            $this->reloadData();
        }
    }

    public function authorized($parentable_id)
    {
        $parentable = Parentable::find($parentable_id);

        $key = Str::random(5);

        $key = 12345;

        if($parentable){

            $parentable->update(['authorized' => 1, 'key' => Hash::make($key)]);

            broadcast(new ParentRequestAcceptedEvent($parentable->user));

            // dispatch(new JobSentParentKeyToParentableUser($parentable, $key))->delay(Carbon::now()->addSeconds(15));

            $this->reloadData();
        }
    }


    public function newParent($parent = null)
    {

        $this->dispatchBrowserEvent('Toast', ['title' => 'NOUVELLE DEMANDE DE COMPTE PARENT', 'message' => "Un utilisateur vient d'envoyer une demande de compte parent pour suivre ses enfants!", 'type' => 'info']);

        $this->reloadData();

    }
}
