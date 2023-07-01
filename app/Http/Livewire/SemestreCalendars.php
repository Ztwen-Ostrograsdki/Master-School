<?php

namespace App\Http\Livewire;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Period;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Livewire\Component;

class SemestreCalendars extends Component
{
    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadData'];
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
        $school = School::first();
        $current_period = [];
        $this->local_events = config('app.local_events');

        if($school){
            if($school->trimestre){
                $this->semestre_type = 'Trimestre';
                $this->semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }

            if($this->school_year){
                $this->school_year_model = SchoolYear::where('school_year', $this->school_year)->first();
            }
            else{
                $this->school_year_model = $this->getSchoolYear();
            }
        }
        $semestre_calendars = [];
        $school_calendars = [];
        $s_cals = $this->school_year_model->periods()->where('periods.target', 'semestre-trimestre')->get();
        foreach($s_cals as $s_cal){
            $start_string = $this->__getDateAsString($s_cal->start, false);
            $end_string = $this->__getDateAsString($s_cal->end, false);
            $weeks = Carbon::parse($s_cal->end)->floatDiffInRealWeeks($s_cal->start);
            $days = floor(($weeks - floor($weeks)) * 7);
            $duration = floor($weeks) . ' Semaines ' . $days . ' Jours';

            $in_period = $this->thisDateIsBetween($s_cal->start, $s_cal->end);

            $semestre_calendars[$s_cal->id] = [
                'model' => $s_cal,
                'start' => $start_string,
                'end' => $end_string,
                'duration' => $duration,
                'in_period' => $in_period,
            ];

            if($in_period){
                $passed = '';
                $rest = '';
                $today = Carbon::now();

                $weeks_passed = Carbon::parse($s_cal->today)->floatDiffInRealWeeks($s_cal->start);
                $days_passed = floor(($weeks_passed - floor($weeks_passed)) * 7);

                if(floor($weeks_passed)){
                    $passed.= floor($weeks_passed) . ' Semaines ';
                }
                if($days_passed){
                    $passed.= $days_passed . ' Jours';

                }

                $weeks_rest = Carbon::parse($s_cal->today)->floatDiffInRealWeeks($s_cal->end);
                $days_rest = floor(($weeks_rest - floor($weeks_rest)) * 7);

                if(floor($weeks_rest)){
                    $rest.= floor($weeks_rest) . ' Semaines ';
                }
                if($days_rest){
                    $rest.= $days_rest . ' Jours';

                }
                $current_period = [
                    'target' => $s_cal->object,
                    'passed' => $passed,
                    'rest' => $rest
                ];

            }
        }

        if(count($s_cals) > 0){
            $school_calendars = $this->getSchoolCalendars();
        }

        $periods = $this->school_year_model->periods;
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
        $this->counter = rand(1, 15);
    }

}
