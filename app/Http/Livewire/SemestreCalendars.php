<?php

namespace App\Http\Livewire;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Period;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SemestreCalendars extends Component
{
    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadData', 'NewCalendarAddedLiveEvent' => 'reloadData'];
    public $calendars;
    public $school_year;
    public $school_calendar;
    public $school_calendar_title = "Veuillez sélectionner un calendrier";
    public $school_year_model;
    public $local_events = [];
    public $counter = 0;
    public $semestre_type = 'Semestre';
    public $semestres = [1, 2];


    use ModelQueryTrait;

    use DateFormattor;

    public function render()
    {
        $this->local_events = config('app.local_events');
        
        $this->school_year_model = $this->getSchoolYear($this->school_year);

        $school_year_model = $this->school_year_model;

        $calendar_profiler = $school_year_model->calendarProfiler();

        $current_period = $calendar_profiler['current_period'];

        $semestre_calendars = $calendar_profiler['semestre_calendars'];

        $s_cals = $school_year_model->periods()->where('periods.target', 'semestre-trimestre')->get();


        if(count($s_cals) > 0){

            $school_calendars = $this->getSchoolCalendars();
        }

        $periods = $school_year_model->periods;

        return view('livewire.semestre-calendars', compact('semestre_calendars', 'periods', 'current_period', 'school_calendars'));
    }


    public function addNewEventPeriod()
    {
        $this->emit('definedPeriodsLiveEvent');
    }

    public function definedSemestrePeriod()
    {
        $this->emit('definedSemestresPeriodsLiveEvent');
    }

    public function editSemestrePeriods()
    {
        $this->emit('editSemestrePeriodsLiveEvent');
    }

    public function getSchoolCalendars($school_calendar = null)
    {
        $calendars = [];

        if(session()->has('school_calendar_selected') && session('school_calendar_selected')){

            $this->school_calendar = session('school_calendar_selected');
        }
        elseif(!session()->has('school_calendar_selected')){

            $this->school_calendar = $school_calendar;
        }
        else{
            $this->school_calendar = null;
        }

        if($this->school_calendar){

            $calendars = $this->school_year_model->periods()->where('periods.target', $this->school_calendar)->get();

            $this->school_calendar_title = array_reverse($this->local_events)[$this->school_calendar];
        }

        return $calendars;
    }


    public function changeSchoolCalendar()
    {
        if($this->school_calendar){

            session()->put('school_calendar_selected', $this->school_calendar);

            $this->school_calendar_title = array_reverse($this->local_events)[$this->school_calendar];
        }
        else{

            $this->reset('school_calendar_title');

        }
    }


    public function resetAllCalendars()
    {
        $school_year_model = $this->getSchoolYear();

        $semestre_calendars = $school_year_model->periods;

        $size = count($semestre_calendars);

        DB::transaction(function($e) use ($semestre_calendars, $size){

            if($size > 0){
                
                $del = $semestre_calendars->delete();

                if($del){

                    $this->dispatchBrowserEvent('Toast', ['title' => "SUPPRESSION REUSSIE: $size évènements supprimés!", 'message' => "Tous les calendriers et évènements pré-programmés ont été rafraîchis avec succès!", 'type' => 'success']);

                    $this->reloadData();
                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'opération a échoué!", 'type' => 'error']);
                }
            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CALENDRIER VIDE!', 'message' => "Aucun évènement et ou calendrier n'a été pré-défini!", 'type' => 'info']);
            }


        });


    }



     public function resetCalendars($school_calendar = null)
    {
        $school_year_model = $this->getSchoolYear();

        $semestre_calendars = $school_year_model->periods()->where('target', $this->school_calendar)->get();

        $size = count($semestre_calendars);

        if($size > 0){

            $school_calendar = $this->school_calendar;
            
            DB::transaction(function($e) use ($semestre_calendars, $size){

                foreach($semestre_calendars as $calendar){

                    $calendar->delete();
                }
            });
            $this->dispatchBrowserEvent('Toast', ['title' => "SUPPRESSION REUSSIE : {$size} évènements supprimés!", 'message' => "Tous les calendriers et évènements de type {$school_calendar} pré-programmés ont été rafraîchis avec succès!", 'type' => 'success']);

            $this->reloadData();
        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CALENDRIER VIDE!', 'message' => "Aucun évènement et ou calendrier de type {$school_calendar} n'a été pré-défini!", 'type' => 'info']);
        }


    }


    public function deletePeriod($period_id)
    {
        $period = Period::find($period_id);

        if($period){
            $del = $period->delete();
            if($del){
                $this->dispatchBrowserEvent('Toast', ['title' => 'Succes', 'message' => "Le programme a été supprimé avec succès!", 'type' => 'success']);
                $this->counter = rand(1, 7);
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'opération a échoué!", 'type' => 'error']);
            }
        }
    }

    public function editPeriod($period_id)
    {
        $this->emit('editPeriodsLiveEvent', $period_id);
    }


    public function reloadData()
    {
        $this->counter = rand(1, 7);
    }

}
