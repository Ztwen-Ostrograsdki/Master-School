<?php

namespace App\Http\Livewire;

use App\Models\School;
use Livewire\Component;

class AdminHeaderComponent extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadSchoolYear',
        'schoolHasBeenCreated' => 'reloadData',
        'newClasseCreated' => 'reloadData',
    ];

    public $counter = 0;

    public function mount()
    {
        
    }



    public function render()
    {
        $school_year = null;
        $school = count(School::all());
        $school_years = count(School::all());
        if($school_years > 0 && $school > 0){
            $school_year = session('school_year_selected');
        }
        return view('livewire.admin-header-component', compact('school_year'));
    }



    public function reloadSchoolYear($school_year)
    {
        $this->school_year = $school_year;
    } 


    public function reloadData()
    {
        $this->counter = 1;
    }
}
