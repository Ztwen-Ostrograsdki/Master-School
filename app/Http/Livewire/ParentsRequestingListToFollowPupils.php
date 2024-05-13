<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ParentRequestToFollowPupil;
use App\Models\Parentable;
use Livewire\Component;

class ParentsRequestingListToFollowPupils extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'UpdatedGlobalSearch' => 'updatedSearch',
        'pupilUpdated' => 'reloadData',
        'UpdatedSchoolYearData' => 'reloadData',
        'GlobalDataUpdated' => 'reloadData',
        'NewParentRequest' => 'newParent',
        'NewParentRequestToFollowPupilLiveEvent' => 'reloadRequests',
    ];

    public $counter = 0;

    public $search = null;



    use ModelQueryTrait;

    public function render()
    {
        $parents = Parentable::all();

        $parentsRequests = ParentRequestToFollowPupil::all();

        $professions = config('app.professions');

        return view('livewire.parents-requesting-list-to-follow-pupils', compact('parents', 'professions', 'parentsRequests'));
    }

    public function reloadRequests()
    {
        $this->counter = rand(1, 12);
    }

    public function delete($req_id)
    {
        ParentRequestToFollowPupil::find($req_id)->delete();
    }


    public function confirmed($req_id)
    {
        $req = ParentRequestToFollowPupil::find($req_id);
    }


}
