<?php

namespace App\Http\Livewire;

use App\Events\FreshAveragesIntoDBEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use Livewire\Component;

class ClasseAveragesComponent extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'newLevelCreated' => 'reloadClasseData',
        'timePlanTablesWasUpdatedLiveEvent' => 'reloadClasseData',
        'NewClasseMarksInsert' => 'reloadClasseData',
        'UpdatedGlobalSearch' => 'updatedSearch',
        'JobProcessedWorks' => 'getJobsOk',
        'InitiateClasseDataUpdatingLiveEvent' => 'loadingDataStart',
        'ClasseDataLoadedSuccessfully' => 'dataWasLoaded',
    ];

    public $classe_id;

    public $counter = 0;

    public $semestre_type = 'Semestre';

    public $order = null;
    
    public $targetToOrder = null;

    public $orders = ['desc' => 'desc', 'asc' => 'asc', null];

    public $semestre_selected = 1;

    public $sexe_selected;

    public $search = null;

    public $is_loading = false;


    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);

        $semestre_selected = session('semestre_selected');
        
        $pupils = [];

        $school = School::first();

        $semestres = [1, 2];

        $additional = [];

        if($school){

            if($school->trimestre){

                $this->semestre_type = 'trimestre';

                $semestres = [1, 2, 3];
            }
            else{

                $semestres = [1, 2];
            }
        }


        if($classe){

            if($this->order){

                $data = [];

                if(!$this->targetToOrder){

                    $pupils_ids = [];

                    $averages = $classe->averages()->where('averages.school_year_id', $school_year_model->id)->whereNull('averages.semestre')->orderBy('moy', 'desc')->get();

                    if($this->sexe_selected && $this->search){

                        $pupils_ids = $classe->getPupils($school_year_model->id, $this->search, $this->sexe_selected, true);

                    }
                    elseif($this->sexe_selected){

                        $pupils_ids = $classe->getPupils($school_year_model->id, null, $this->sexe_selected, true);

                    }
                    elseif($this->search){

                        $pupils_ids = $classe->getPupils($school_year_model->id, $this->search, null, true);

                    }
                    else{

                        $pupils_ids = $classe->getPupils($school_year_model->id, null, null, true);

                    }

                    if(count($averages)){

                        foreach($averages as $av){

                            if($pupils_ids && in_array($av->pupil_id, $pupils_ids)){

                                $pupils[$av->pupil_id] = $av->pupil;

                            }

                        }

                    }
                    else{
                        $pupils = $classe->getPupils($school_year_model->id, $this->search, $this->sexe_selected);

                    }

                }
                elseif($this->targetToOrder){

                    $averages = $classe->averages()->where('averages.school_year_id', $school_year_model->id)->where('averages.semestre', $this->targetToOrder)->orderBy('moy', 'desc')->get();

                    $pupils_ids = [];
                    
                    if($this->sexe_selected && $this->search){

                        $pupils_ids = $classe->getPupils($school_year_model->id, $this->search, $this->sexe_selected, true);

                    }
                    elseif($this->sexe_selected){

                        $pupils_ids = $classe->getPupils($school_year_model->id, null, $this->sexe_selected, true);

                    }
                    elseif($this->search){

                        $pupils_ids = $classe->getPupils($school_year_model->id, $this->search, null, true);

                    }
                    else{

                        $pupils_ids = $classe->getPupils($school_year_model->id, null, null, true);

                    }

                    if(count($averages)){

                        foreach($averages as $av){

                            if($pupils_ids && in_array($av->pupil_id, $pupils_ids)){

                                $pupils[$av->pupil_id] = $av->pupil;

                            }
                        }

                    }
                    else{
                        $pupils = $classe->getPupils($school_year_model->id, $this->search, $this->sexe_selected);

                    }

                }

            }
            else{

                $pupils = $classe->getPupils($school_year_model->id, $this->search, $this->sexe_selected);
            }

            if($this->order){

                $additional = $classe->getPupils($school_year_model->id);

                foreach($additional as $p){

                    if(!array_key_exists($p->id, $pupils)){

                        $pupils[$p->id] = $p;

                    }

                }

            }
        }
        return view('livewire.classe-averages-component', compact('pupils', 'school_year_model', 'semestres', 'classe', 'semestre_selected'));
    }


    public function dataWasLoaded()
    {
        $this->is_loading = false;
    }

    public function loadingDataStart()
    {
        $this->is_loading = true;
    }


    public function forceDeletePupil($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $pupil->pupilDeleter(null, true);
        }
    }
    public function migrateTo($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $this->emit('MovePupilToNewClasse', $pupil->id);
        }
        
    }

    public function lockMarksUpdating($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        $pupil->lockPupilMarksUpdating();

    }


    public function unlockMarksUpdating($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        $pupil->unlockPupilMarksUpdating();

    }

    public function lockMarksInsertion($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        $pupil->lockPupilMarksInsertion();

    }

    public function unlockMarksInsertion($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        
        $pupil->unlockPupilMarksInsertion();

    }


    public function getJobsOk($event)
    {
        $this->reloadClasseData();

    }

    public function updatedSearch($search)
    {
        $this->search = $search;
    }

    public function updatedSexeSelected($sexe)
    {
        $this->sexe_selected = $sexe;
    }

    public function orderer($target = null)
    {
        $this->order = 'desc';

        $target ? $this->targetToOrder = $target : $this->targetToOrder = null;
        // dd($this);

    }


    public function refreshOrder()
    {
        $this->reset('targetToOrder', 'order');
    }


    public function optimizeClasseAveragesIntoDatabase($classe_id)
    {
        $classe = Classe::find($classe_id);

        $semestres = $this->getSemestres();

        $user = auth()->user();

        if($classe && $semestres){

            $school_year_model = $this->getSchoolYear();

            $semestre = session('semestre_selected');

            FreshAveragesIntoDBEvent::dispatch($user, $classe, $school_year_model, $semestre, true);
            
        }

    }



    public function reloadClasseData($school_year = null)
    {
        $this->counter = rand(1, 14);
    }
}
