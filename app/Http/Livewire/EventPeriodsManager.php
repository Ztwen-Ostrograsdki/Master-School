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

class EventPeriodsManager extends Component
{
    protected $listeners = ['definedPeriodsLiveEvent' => 'openModal', 'editPeriodsLiveEvent' => 'openUpdate'];

    use ModelQueryTrait;

    use DateFormattor;

    public $semestres = [1, 2];

    public $semestre_type = 'Semestre';

    public $semestre_id = 1;

    public $hasErrorsHere = false;

    public $looked = 'CREATION';

    public $years;

    public $school_year;

    public $start;

    public $end;

    public $object;

    public $target;

    public $period;
    

    protected $rules = [
        'start' => 'required|bail',
        'end' => 'required|bail',
        'object' => 'string|required|bail',
        'target' => 'string|required|bail'

    ];


    public function render()
    {
        $period_weeks = null;

        $events = config('app.local_events');

        $school = School::first();

        if($school){

            if($school->trimestre){

                $this->semestre_type = 'Trimestre';

                $this->semestres = [1, 2, 3];
            }
            else{

                $this->semestres = [1, 2];
            }

            if($this->start && $this->end){

                $period_string = 'Du '. $this->__getDateAsString($this->start, false) . ' Au ' . $this->__getDateAsString($this->end, false);

                $week = Carbon::parse($this->start)->floatDiffInRealWeeks($this->end);

                $day = floor(($week - floor($week)) * 7);

                $period_weeks = floor($week) . ' Semaines ' . $day . ' Jours';
            }
            else{
                $period_string = "L'évènement pendant " . $this->semestre_type . ' ' . $this->semestre_id;
            }

        }
        return view('livewire.event-periods-manager', compact('school', 'period_string', 'period_weeks', 'events'));
    }

    public function openModal()
    {
        $this->school_year_model = $this->getSchoolYear();

        $semestre_calendars = $this->school_year_model->periods()->where('target', 'semestre-trimestre')->orderBy('object')->get();

        if(count($semestre_calendars)){

            $school_year = $this->school_year_model->school_year;

            $this->semestre_id = session('semestre_selected');

            if(!$school_year){

                $current_month_index = intval(date('m'));

                if($current_month_index >= 10){

                    $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
                }
                else{

                    $school_year = intval(date('Y') - 1) . ' - ' . intval(date('Y'));
                }
            }

            $years = explode(' - ', $school_year);

            $this->years = $years;

            $this->looked = 'CREATION';

            $this->dispatchBrowserEvent('modal-eventPeriodManager');
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE: SEMESTRE/TRIMESTRE NON DEFINI!', 'message' => "Veuillez d'abord définir le calendrier trimestriel ou semestriel avant d'ajouter un évènement!", 'type' => 'warning']);

        }
    }


    public function openUpdate($period_id)
    {  
        $period = Period::find($period_id);

        if($period){

            $school_year_model = $period->school_year;

            $this->period = $period;

            $this->start = $period->start;

            $this->end = $period->end;

            $this->object = $period->object;

            $this->target = $period->target;

            $years = explode(' - ', $school_year_model->school_year);

            $this->years = $years;
            
            $this->looked = 'Edition';

            $this->dispatchBrowserEvent('modal-eventPeriodManager');
        }
    }



    public function updatedStart($start)
    {
        $this->resetErrorBag(['start']);

        $this->start = $start;

        $this->validatePeriods($this->start, $this->end);

        if($this->semestre_id){

            $periodsExisted = $this->school_year_model->periods()->where('target', 'semestre-trimestre')->where('object', $this->semestre_type . ' ' . $this->semestre_id)->first();

            if($periodsExisted){

                $v_start = $this->thisDateIsBetween($periodsExisted->start, $periodsExisted->end, $this->start);

                if(!$v_start){

                    $this->addError('start', "La date renseillée est invalide. Elle doit être située entre le " . $this->semestre_type . ' ' . $this->semestre_id);
                }
            }

        }
    }


    public function updatedSemestreId($semestre_id)
    {
        $this->semestre_id = $semestre_id;

        $periodsExisted = $this->school_year_model->periods()->where('target', 'semestre-trimestre')->where('object', $this->semestre_type . ' ' . $this->semestre_id)->first();

        if($periodsExisted){

            $this->start = $periodsExisted->start;

            $this->end = $periodsExisted->end;
        }
    }

    public function updatedEnd($end)
    {
        $this->resetErrorBag(['end']);

        $this->end = $end;

        $this->validatePeriods($this->start, $this->end, 'end');

        if($this->semestre_id){

            $periodsExisted = $this->school_year_model->periods()->where('target', 'semestre-trimestre')->where('object', $this->semestre_type . ' ' . $this->semestre_id)->first();
            
            if($periodsExisted){

                $v_end = $this->thisDateIsBetween($periodsExisted->start, $periodsExisted->end, $this->end);

                if(!$v_end){

                    $this->addError('end', "La date renseillée est invalide. Elle doit être située entre le " . $this->semestre_type . ' ' . $this->semestre_id);
                }

            }

        }
    }



    public function validatePeriods($start, $end, $target = null)
    {
        $errors = null;

        if($start && $end){

            if(in_array(Carbon::parse($start)->year, $this->years) && in_array(Carbon::parse($end)->year, $this->years)){

                $timestamp_start = Carbon::parse($start)->timestamp;

                $timestamp_end = Carbon::parse($end)->timestamp;

                $v = $timestamp_end - $timestamp_start;

                if($v <= 0){

                    $errors = true;

                    $this->addError($target, "La période définie est incorrecte!");
                }
                else{
                    
                }
            }
            else{

                $errors = true;

                $this->addError($target, "L'année est incorrecte!");

            }

            if($this->semestre_id){

                $the_target = "semestre-trimestre";

                $the_object = $this->semestre_type . ' ' . $this->semestre_id;

                $periodsExisted = $this->school_year_model->getPeriod($the_target, $the_object);

                if($periodsExisted){

                    $v_start = $this->thisDateIsBetween($periodsExisted->start, $periodsExisted->end, $start);
                    $v_end = $this->thisDateIsBetween($periodsExisted->start, $periodsExisted->end, $end);

                    if(!$v_start){

                        $this->addError('start', "La date renseillée est invalide. Elle doit être située entre le " . $this->semestre_type . ' ' . $this->semestre_id);

                        $errors = true;
                    }

                    if(!$v_end){

                        $this->addError('end', "La date renseillée est invalide. Elle doit être située entre le " . $this->semestre_type . ' ' . $this->semestre_id);

                        $errors = true;
                    }

                }

            }
        }

        $this->hasErrorsHere = $errors;


    }



    public function submit()
    {
        $this->reset('hasErrorsHere');

        $errors = $this->getErrorBag();

        if(!$this->validate()){

        }
        else{

            $this->validatePeriods($this->start, $this->end, 'start');

            if(!$this->hasErrorsHere){

                if($this->period){
                    //UPDATING
                    DB::transaction(function($e) {
                        try {
                            $this->period->update
                            ([
                                'start' => $this->start,
                                'end' => $this->end,
                                'target' => $this->target,
                                'object' => $this->object,
                                'semestre' => $this->semestre_id,
                                'school_year_id' => $this->school_year_model->id,
                            ]);
                        } 
                        catch (Exception $e1) {

                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "Une erreure est survenue lors de la mise à jour de cet évènement!", 'type' => 'error']);
                        }
                    });

                    DB::afterCommit(function(){

                        $this->dispatchBrowserEvent('hide-form');

                        $this->emit('NewCalendarAddedLiveEvent');
                        $this->emit('relaodCalendars');

                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour réussie!', 'message' => "Votre évènement $this->target : $this->object avec succès", 'type' => 'success']);
                    });



                }
                else{
                    //CREATING
                    DB::transaction(function($e) {
                        try {
                            $event = Period::create([
                                'start' => $this->start,
                                'end' => $this->end,
                                'target' => $this->target,
                                'object' => $this->object,
                                'semestre' => $this->semestre_id,
                                'school_year_id' => $this->school_year_model->id,
                            ]);
                        } 
                        catch (Exception $e1) {

                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "Une erreure est survenue lors de la mise à jour de cet évènement!", 'type' => 'error']);
                        }
                    });

                    DB::afterCommit(function(){

                        $this->dispatchBrowserEvent('hide-form');

                        $this->emit('NewCalendarAddedLiveEvent');
                        $this->emit('relaodCalendars');

                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour réussie!', 'message' => "Votre évènement $this->target : $this->object avec succès", 'type' => 'success']);
                    });
                }

                
            }
        }


    }
}
