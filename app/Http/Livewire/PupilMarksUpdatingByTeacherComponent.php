<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Livewire\Component;

class PupilMarksUpdatingByTeacherComponent extends Component
{
    use ModelQueryTrait;

    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $marks_requests = $school_year_model->marks()->where('marks.updating', true)->get();

        return view('livewire.pupil-marks-updating-by-teacher-component', compact('marks_requests', 'school_year_model'));
    }


    public function getListeners()
    {


        return [
            "echo-private:master, UpdatePupilsMarksUpdatingRequestsEvent" => "reloadData",
        ];

    }

    public function reloadData($data = null)
    {

    }
}
