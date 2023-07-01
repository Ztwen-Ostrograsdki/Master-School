<?php

namespace App\Http\Livewire;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Period;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Livewire\Component;

class SchoolCalendar extends Component
{
    protected $listeners = ['relaodCalendars', 'schoolYearChangedLiveEvent' => 'relaodCalendars'];

    public $school_year_model;
    public $counter = 0;
    public $semestre_type = 'Semestre';
    public $semestres = [1, 2];


    use ModelQueryTrait;
    use DateFormattor;


    public function mount($school_year)
    {

        if($school_year){
            $school_year = str_replace('-', ' - ', $school_year);
            $this->school_year_model = SchoolYear::where('school_year', $school_year)->first();
        }
        else{
            return abort(404);

        }
    }


    public function render()
    {
        $school = School::first();
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'Trimestre';
                $this->semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        $periods = $this->school_year_model->periods;

        return view('livewire.school-calendar', compact('periods'));
    }


    public function setActiveSection($section)
    {
        $this->section = $section;
        session()->put('calendar_section', $section);
    }

    public function resetAllSemestreCalendars()
    {
        $semestre_calendars = $this->school_year_model->periods();
        if($semestre_calendars->get()->count() > 0){
            $del = $semestre_calendars->delete();
            if($del){
                $this->dispatchBrowserEvent('Toast', ['title' => 'Succes', 'message' => "Rafraichissement réussi!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'Opération a échoué!", 'type' => 'error']);
            }
        }
    }


    public function definedSemestrePeriod()
    {
        $this->emit('definedSemestresPeriodsLiveEvent');
    }

    public function editSemestrePeriods()
    {
        $this->emit('editSemestrePeriodsLiveEvent');
    }
    
    public function addNewEventPeriod()
    {
        $this->emit('definedPeriodsLiveEvent');
    }


    public function relaodCalendars()
    {
        $this->counter = rand(0, 5);
    }

}
