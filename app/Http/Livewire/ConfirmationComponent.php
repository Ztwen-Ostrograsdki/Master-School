<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ConfirmationComponent extends Component
{
    protected $listeners = ['ConfirmRequestLiveEvent' => 'openModal'];
    public $title = "Gestion de requête de suppression délicate!";
    public $model;
    public $classMapping;
    public $school_year_model;
    private $local_actions = ['D', 'FD', 'C', 'U'];


    public function render()
    {
        return view('livewire.confirmation-component');
    }



    /**
     * D => delete
     * FD => force delete
     * C => create
     * U => update
        detach => [
            school_year,
            teachers
        ]

        delete => [
            responsible
            principalteacher
            timeplans
            average_modality
            teacher_cursus
            pupil_cursus
            images
            related_marks
            marks
            pupil_lates
            pupil_absences and for teacher
            presence

        ]
     */

    use ModelQueryTrait;

    public function makerForClasse($school_year_model = null)
    {
        if(!$school_year_model){
            $school_year_model = $this->school_year_model;
        }

        $classe = $this->model;

        if($school_year_model){
            $classe->classeDeleter($school_year_model->id);
        }

    }
   


    public function onceDelete()
    {
        $this->makerForClasse();
        $this->dispatchBrowserEvent('hide-form');
        $this->emit('classeUpdated');
    }

    public function fullDelete()
    {
        $school_years = $this->model->school_years;
        foreach($school_years as $school_year_model){
            $this->makerForClasse($school_year_model);
        }
        $this->dispatchBrowserEvent('hide-form');
        $this->emit('classeUpdated');
    }

    public function openModal($model_id, $classMapping, $school_year = null, $action = 'D')
    {
        if($model_id && $classMapping){
            $this->classMapping = $classMapping;
            $this->model = $classMapping::find($model_id);
        }
        $this->school_year_model = $this->getSchoolYear($school_year);

        $this->dispatchBrowserEvent('modal-confirmation');
    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');
    }


}
