<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\School;
use App\Models\SchoolYear;
use Livewire\Component;

class SchoolYearsManager extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolHasBeenCreated' => 'reloadData',
        'schoolYearChangedExternallyLiveEvent' => 'reloadSchoolYear',

    ];

    public $school_year_selected;

    public $counter = 0;

    public $has_school = false;


    public function render()
    {
        $school = count(School::all());

        if($school > 0){

            $school_years = SchoolYear::orderBy('school_year', 'asc')->get();

            $this->has_school = true;
        }
        else{

            $school_year = date('Y') . ' - ' . intval(date('Y') + 1);

            $this->school_years = [$school_year];
        }

        $school_year_model = $this->getSchoolYear();

        if($school_year_model){

            $this->school_year_selected = $school_year_model->school_year;
        }

        return view('livewire.school-years-manager', compact('school_years'));
    }


    public function updatedSchoolYearSelected($school_year)
    {
        session()->put('school_year_selected', $this->school_year_selected);

        $this->emit("schoolYearChangedLiveEvent", $this->school_year_selected);
    }

    public function reloadSchoolYear($school_year)
    {

        $this->school_year_selected = $school_year;

        session()->put('school_year_selected', $this->school_year_selected);

        $this->emit("schoolYearChangedLiveEvent", $this->school_year_selected);
    }


    public function reloadData()
    {
        $this->counter = 1;

    }
}
