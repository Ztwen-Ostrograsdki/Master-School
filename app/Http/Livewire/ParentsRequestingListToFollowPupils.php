<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ParentRequestToFollowPupil;
use App\Models\Parentable;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ParentsRequestingListToFollowPupils extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'pupilUpdated' => 'reloadData',
        'UpdatedSchoolYearData' => 'reloadData',
        'GlobalDataUpdated' => 'reloadData',
        'NewParentRequest' => 'newParent',
        'NewParentRequestToFollowPupilLiveEvent' => 'newParentRequest',
        'UpdateParentRequestsListLiveEvent' => 'reloadRequests',
        'ParentHaveBeenJoinedToPupilLiveEvent' => 'reloadRequests',
    ];

    public $counter = 0;

    public $search = null;

    public $display_by_target = null;

    public $display_by_parent = null;

    public $sections = [
        'all' => 'Toutes les demandes',
        'authorized' => 'Demandes Approuvées',
        'refused' => 'Demandes rejetées',
        'analysed' => 'Demandes analysées',

    ];



    use ModelQueryTrait;

    public function render()
    {
        $parents = [];

        $requestsToDisplay = [];

        $parentsRequestsNoTreats = ParentRequestToFollowPupil::where('refused', 0)->where('authorized', 0)->orderBy('updated_at', 'desc')->get();

        $parentsRequestsTreats = ParentRequestToFollowPupil::where('refused', 1)->orWhere('authorized', 1)->orderBy('updated_at', 'desc')->get();

        if(count($parentsRequestsTreats)){

            foreach($parentsRequestsTreats as $rq){

                if(!isset($parents[$rq->parentable_id])){

                    $parents[$rq->parentable_id] = $rq->parentable;

                }

            }

        }


        if(count($parentsRequestsNoTreats)){

            foreach($parentsRequestsNoTreats as $rqq){

                if(!isset($parents[$rqq->parentable_id])){

                    $parents[$rqq->parentable_id] = $rqq->parentable;

                }

            }

        }


        if(count($parentsRequestsTreats) > 0 || count($parentsRequestsNoTreats) > 0){

            $requestsToDisplay = $this->getRequestsToDisplay();

        }


        return view('livewire.parents-requesting-list-to-follow-pupils', compact('parents', 'parentsRequestsNoTreats', 'parentsRequestsTreats', 'requestsToDisplay'));
    }


    public function getRequestsToDisplay()
    {

        if(session()->has('parent_request_list_by_parent')){

            $this->display_by_parent = session('parent_request_list_by_parent');

        }
        

        if(session()->has('parent_request_list_by_target')){

            $this->display_by_target = session('parent_request_list_by_target');

        }

        $byp = $this->display_by_parent;

        $byt = $this->display_by_target;

        if($byp && $byt){


            if($byp !== 'all' && $byt !== 'all'){

                $requestsToDisplay = ParentRequestToFollowPupil::where($byt, 1)->where('parentable_id', $byp)->orderBy('updated_at', 'desc')->get();

            }
            elseif($byp !== 'all' && $byt == 'all'){

                $requestsToDisplay = ParentRequestToFollowPupil::where('parentable_id', $byp)->orderBy('updated_at', 'desc')->get();

            }
            elseif($byp == 'all' && $byt !== 'all'){

                $requestsToDisplay = ParentRequestToFollowPupil::where($byt, 1)->orderBy('updated_at', 'desc')->get();

            }
            elseif($byp == 'all' && $byt == 'all' ){

                $requestsToDisplay = ParentRequestToFollowPupil::orderBy('updated_at', 'desc')->get();

            }
        }
        else{

            if($byp == null && $byt !== null){

                if($byt == 'all'){

                    $requestsToDisplay = ParentRequestToFollowPupil::orderBy('updated_at', 'desc')->get();

                }
                else{

                    $requestsToDisplay = ParentRequestToFollowPupil::where($byt, 1)->orderBy('updated_at', 'desc')->get();

                }

            }
            elseif($byp !== null && $byt == null){

                if($byp == 'all'){

                    $requestsToDisplay = ParentRequestToFollowPupil::orderBy('updated_at', 'desc')->get();

                }
                else{

                    $requestsToDisplay = ParentRequestToFollowPupil::where('parentable_id', $byp)->orderBy('updated_at', 'desc')->get();

                }

            }
            else{

                $requestsToDisplay = ParentRequestToFollowPupil::where('refused', 0)->where('authorized', 0)->orderBy('updated_at', 'desc')->get();

            }
        }


        return $requestsToDisplay;


    }

    public function updatedDisplayByParent($parent)
    {
        session()->put('parent_request_list_by_parent', $parent);
    }


    public function updatedDisplayByTarget($target)
    {
        session()->put('parent_request_list_by_target', $target);
    }

    public function reloadRequests()
    {

        $this->counter = rand(1, 12);

    }

    public function newParentRequest()
    {

        $this->dispatchBrowserEvent('Toast', ['title' => 'NOUVELLE DEMANDE DE SUIVI', 'message' => "Un parent vient d'envoyer une demande de suivie d'un apprenant!", 'type' => 'success']);

        $this->counter = rand(1, 12);

    }

    public function delete($req_id)
    {
        ParentRequestToFollowPupil::find($req_id)->delete();
    }


    public function confirmed($req_id)
    {
        $req = ParentRequestToFollowPupil::find($req_id);

        return ($req && !$req->authorized) ? $req->update(['authorized' => true, 'analysed' => true, 'refused' => false]) : false;
    }

    public function analyzed($req_id)
    {
        $req = ParentRequestToFollowPupil::find($req_id);

        return ($req && !$req->analysed) ? $req->update(['analysed' => true, 'refused' => false]) : false;
    }

    public function refused($req_id)
    {
        
        $req = ParentRequestToFollowPupil::find($req_id);

        if($req){

            $joineds = $req->parentable->pupils()->where('parent_pupils.pupil_id', $req->pupil_id)->get();

            DB::transaction(function($e) use ($joineds, $req){

                foreach($joineds as $join){

                    $join->delete();

                }

                return (!$req->refused) ? $req->update(['refused' => true, 'analysed' => false, 'authorized' => false]) : false;

            });
        }
        

        
    }


}
