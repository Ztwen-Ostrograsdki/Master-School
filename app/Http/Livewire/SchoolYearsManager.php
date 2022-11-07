<?php

namespace App\Http\Livewire;

use App\Models\School;
use App\Models\SchoolYear;
use Livewire\Component;

class SchoolYearsManager extends Component
{
    protected $listeners = [
        'schoolHasBeenCreated' => 'reloadData',
        'schoolYearChangedExternallyLiveEvent' => 'reloadSchoolYear',

    ];
    public $school_year_selected;

    public $counter = 0;
    public $school_years;
    public $has_school = false;

    public function mount()
    {
        $this->school_years = SchoolYear::all()->pluck('school_year');
    }

    public function render()
    {
        $school = count(School::all());
        if($school > 0){
            $this->has_school = true;
            $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
            if(session()->has('school_year_selected') && session('school_year_selected')){
                $school_year = session('school_year_selected');
                session()->put('school_year_selected', $school_year);
                $this->school_year_selected = $school_year;
            }
            else{
                session()->put('school_year_selected', $school_year);
                $this->school_year_selected = $school_year;
            }

        }
        else{


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
