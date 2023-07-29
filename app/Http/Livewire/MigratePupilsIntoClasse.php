<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MigratePupilsIntoClasse extends Component
{

    protected $listeners = ['ImportPupilsIntoClasse' => 'openModal'];

    public $classe_id;

    public $level_id;

    public $classe_id_selected;

    public $classe_group_id_selected;

    public $classe;

    public $position = 1;

    public $title = "Migration des apprenants vers la classe ...";

    public $school_year_model;

    public $school_year_befor_model;

    public $import_marks = false;

    public $data = [];

    public $selecteds_pupils = [];

    public $confirmation = false;



    use ModelQueryTrait;


    public function render()
    {

        $pupils = [];

        $classe_groups = [];

        $classes = [];

        if($this->classe){

            $position = $this->position;

            $level_id = $this->level_id;

            $positions = [];

            $classe_id_selected = $this->classe_id_selected;

            $classe_group_id_selected = $this->classe_group_id_selected;

            $school_year_befor_model = $this->getSchoolYearBefor();

            $school_year_model = $this->school_year_model;


            if($classe_id_selected){

                $classe_selected = $school_year_befor_model->findClasse($classe_id_selected);

                if($classe_selected){

                    $pupils = $classe_selected->getClasseCurrentPupils($school_year_befor_model->id);

                }

            }
            elseif($classe_group_id_selected){

                $classe_group_selected = $school_year_befor_model->findClasseGroup($classe_group_id_selected);


                if($classe_group_selected){

                    $classes = $classe_group_selected->classes;

                    $pupils = $classe_group_selected->getClasseGroupCurrentPupils($school_year_befor_model->id);

                }

            }
            else{

                $positions = [$position, $position + 1];

                $classe_groups = $school_year_model->classe_groups;

                $classes = $school_year_befor_model->classes()->where('classes.level_id', $level_id)->whereIn('classes.position', $positions)->get();

                if(count($classes) > 0){

                    foreach($classes as $cl){

                        if(is_object($cl)){

                            $pupil_ids = $cl->classePupilSchoolYear()->where('school_year_id', $school_year_befor_model->id)->pluck('pupil_id')->toArray();

                            $cl_pupils = Pupil::whereIn('id', $pupil_ids)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

                            if($cl_pupils !== []){

                                foreach($cl_pupils as $p){

                                    $pupils[] = $p;

                                }

                            }

                        }

                    }

                }

            }

        }

        
        return view('livewire.migrate-pupils-into-classe', compact('classe_groups', 'classes', 'pupils'));
    }



    public function openModal($classe_id)
    {

        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($classe_id);

        if($classe){

            $this->classe = $classe;

            $this->classe_id = $classe_id;

            $this->level_id = $classe->level_id;

            $this->position = $classe->position;

            $this->school_year_model = $school_year_model;

            $this->school_year_befor_model = $this->getSchoolYearBefor();

            $this->title = "Migration des apprenants vers la classe de " . $classe->name;

            $this->dispatchBrowserEvent('modal-importPupilsIntoClasse');

        }


    }


    public function confirmed()
    {
        $pupils = $this->selecteds_pupils;

        if(count($pupils)){

            $this->classe->pupilsMigraterToClasseForNewSchoolYear($pupils);
        }

        DB::afterCommit(function(){

            $this->cancel();

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "La migration été faite avec succès!", 'type' => 'success']);

            $this->emit('GlobalDataUpdated');


        });

    }


    public function submit()
    {
        $data = $this->data;

        $pupils = [];

        if(count($data)){

            $pupils = Pupil::whereIn('id', $data)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

        }

        $this->selecteds_pupils = $pupils;

        $this->confirmation = true;
    }


    

    public function pushIntoData($pupil_id)
    {
        $data = $this->data;

        $data[$pupil_id] = $pupil_id;

        $this->data = $data;

        // dd($data, $this->data, $pupil_id);

    }

    public function retrieveFromData($pupil_id)
    {
        $data = $this->data;

        unset($data[$pupil_id]);

        $this->data = $data;

        if($this->confirmation){

            $data = $this->data;

            $pupils = [];

            if(count($data)){

                $pupils = Pupil::whereIn('id', $data)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();

            }

            $this->selecteds_pupils = $pupils;
        }

    }



    public function updatedClasseIdSelected($classe_id_selected)
    {

        
    }

    public function updatedClasseGroupIdSelected($classe_group_id_selected)
    {

        
    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');
        
        $this->reset('classe_id', 'classe', 'data', 'selecteds_pupils', 'confirmation', 'selecteds_pupils');

    }


    public function edit()
    {
        $this->confirmation = false;
    }
}
