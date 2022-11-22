<?php

namespace App\Http\Livewire;

use App\Models\Classe;
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

        if($this->search){
            $search = '%' . $this->search . '%';
            $classes = Classe::where('name', 'like', $search)->orWhere('slug', 'like', $search)->get();
            if(count($classes)){
                // $this->hasData = true;
                $this->data = $classes;
            }
            else{
                $this->reset('hasData', 'data');

            }
        }



        return view('livewire.admin-header-component', compact('school_year'));
    }

    public function updatedSearch($value)
    {
        $this->search = $value;
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
