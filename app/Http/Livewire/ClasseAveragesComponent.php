<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
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
        'UpdatedGlobalSearch' => 'updatedSearch',
        'JobProcessedWorks' => 'getJobsOk',
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


    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);

        $semestre_selected = session('semestre_selected');
        
        $pupils = [];

        $school = School::first();

        $semestres = [1, 2];

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

                    $averages = $classe->averages()->where('averages.school_year_id', $school_year_model->id)->whereNull('averages.semestre')->orderBy('moy', 'desc')->get();

                    if(count($averages)){

                        foreach($averages as $av){

                            $pupils[] = $av->pupil;

                        }

                    }
                    else{
                        $pupils = $classe->getPupils($school_year_model->id, $this->search, $this->sexe_selected);

                    }

                }
                elseif($this->targetToOrder){

                    $averages = $classe->averages()->where('averages.school_year_id', $school_year_model->id)->where('averages.semestre', $this->targetToOrder)->orderBy('moy', 'desc')->get();

                    if(count($averages)){

                        foreach($averages as $av){

                            $pupils[] = $av->pupil;

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
        }
        return view('livewire.classe-averages-component', compact('pupils', 'school_year_model', 'semestres', 'classe', 'semestre_selected'));
    }


    public function getJobsOk($event)
    {
        $this->reloadClasseData();

        dd($event);
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

    }


    public function refreshOrder()
    {
        $this->reset('targetToOrder', 'order');
    }



    public function reloadClasseData($school_year = null)
    {
        $this->counter = rand(1, 14);
    }
}
