<?php

namespace App\Http\Livewire;

use App\Models\Classe;
use App\Models\School;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Route;
use Livewire\Component;

class AdminHeaderComponent extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadSchoolYear',
        'schoolHasBeenCreated' => 'reloadData',
        'newClasseCreated' => 'reloadData',
    ];

    public $counter = 0;
    public $search;
    public $hasData = false;
    public $data;

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

    public function updatedSearch($search)
    {
        $this->emit("UpdatedGlobalSearch", $search);
    }


    public function cancelSearch()
    {
        $this->reset('search');
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
