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
    public $school_years;
    public $has_school = false;


    public function render()
    {
        $school = count(School::all());
        if($school > 0){
            $this->school_years = SchoolYear::all()->pluck('school_year');
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

        return view('livewire.school-years-manager');
    }


    public function changeSchoolYear()
    {
        session()->put('school_year_selected', $this->school_year_selected);
        $this->emit("schoolYearChangedLiveEvent", $this->school_year_selected);
    }

    public function reloadSchoolYear($school_year)
    {
        $this->school_year_selected = $school_year;
        $this->emit("schoolYearChangedLiveEvent", $this->school_year_selected);
    }


    public function reloadData()
    {
        $this->counter = 1;

    }
}
