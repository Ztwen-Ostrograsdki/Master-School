<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Livewire\Component;

class PupilsListingByClasse extends Component
{

    use ModelQueryTrait;
    
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
    ];
    
    
    public $classe_id;

    public $slug;

    public $counter = 0;

    public $search = null;


    public function mount($slug = null)
    {
        if($slug){

            $this->slug = $slug;
        }
        else{
            return abort(404);
        }
    }

    public function render()
    {
         $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('slug', urldecode($this->slug))->first();

        if($classe){

            $this->classe_id = $classe->id;

        }

        return view('livewire.pupils-listing-by-classe', compact('school_year_model', 'classe'));
    }

    public function reloadClasseData($school_year = null)
    {
        $this->counter = 1;
    }
}
