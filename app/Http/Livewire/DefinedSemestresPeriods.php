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

class DefinedSemestresPeriods extends Component
{
    protected $listeners = ['definedSemestresPeriodsLiveEvent' => 'openModal'];

    use ModelQueryTrait;
    use DateFormattor;

    public $semestres = [1, 2];
    public $semestre_type = 'Semestre';
    public $hasErrorsHere = false;
    public $years;
    public $school_year;
    public $period11;
    public $period12;
    public $period21;
    public $period22;
    public $period31;
    public $period32;

    protected $rules = [
        'period11' => 'required|bail',
        'period12' => 'required|bail',
        'period21' => 'required|bail',
        'period22' => 'required|bail'

    ];


    public function render()
    {
        $period1_weeks = null;
        $period2_weeks = null;
        $period3_weeks = null;

        $school = School::first();
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'Trimestre';
                $this->semestres = [1, 2, 3];
            }
            else{
                $this->period3 = false;
                $semestres = [1, 2];
            }

            if($this->period11 && $this->period12){
                $period1_string = 'Du '. $this->__getDateAsString($this->period11, false) . ' Au ' . $this->__getDateAsString($this->period12, false);
                $w1 = Carbon::parse($this->period12)->floatDiffInRealWeeks($this->period11);
                $d1 = floor(($w1 - floor($w1)) * 7);
                $period1_weeks = floor($w1) . ' Semaines ' . $d1 . ' Jours';
            }
            else{
                $period1_string = 'La période du ' . $this->semestre_type . ' 1';
            }

            if($this->period21 && $this->period22){
                $period2_string = 'Du '. $this->__getDateAsString($this->period21, false) . ' Au ' . $this->__getDateAsString($this->period22, false);
                $w2 = Carbon::parse($this->period22)->floatDiffInRealWeeks($this->period21);
                $d2 = floor(($w2 - floor($w2)) * 7);
                $period2_weeks = floor($w2) . ' Semaines ' . $d2 . ' Jours';
            }
            else{
                $period2_string = 'La période du ' . $this->semestre_type . ' 2';
            }

            if($this->period31 && $this->period32){
                $period2_string = 'Du '. $this->__getDateAsString($this->period31, false) . ' Au ' . $this->__getDateAsString($this->period32, false);
                $w3 = Carbon::parse($this->period32)->floatDiffInRealWeeks($this->period31);
                $d3 = floor(($w3 - floor($w3)) * 7);
                $period3_weeks = floor($w3) . ' Semaines ' . $d3 . ' Jours';
            }
            else{
                $period3_string = 'La période du ' . $this->semestre_type . ' 3';
            }


        }
        return view('livewire.defined-semestres-periods', compact('semestres', 'school', 'period1_string', 'period1_weeks', 'period2_string', 'period2_weeks', 'period3_string', 'period3_weeks'));
    }


    public function openModal()
    {
        dd(Period::all());

        
        $school_year = session('school_year_selected');
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

        $this->dispatchBrowserEvent('modal-definedSemestresPeriods');
    }

    public function updatedPeriod11($period11)
    {
        $this->resetErrorBag(['period11']);
        $this->period11 = $period11;

        $this->validatePeriods($this->period11, $this->period12);
    }

    public function updatedPeriod12($period12)
    {
        $this->resetErrorBag(['period12']);
        $this->period12 = $period12;
        $this->period21 = $period12;

        $this->validatePeriods($this->period11, $this->period12, 'period12');
    }

    public function updatedPeriod21($period21)
    {
        $this->resetErrorBag(['period21']);
        $this->period21 = $period21;

        $this->validatePeriods($this->period12, $this->period21, 'period21');
        $this->validatePeriods($this->period21, $this->period22, 'period21');
    }

    public function updatedPeriod22($period22)
    {
        $this->resetErrorBag(['period22']);
        $this->period22 = $period22;

        $this->validatePeriods($this->period21, $this->period22, 'period22');
    }

     public function updatedPeriod31($period31)
    {
        $this->resetErrorBag(['period31']);
        $this->period31 = $period31;
        $this->validatePeriods($this->period31, $this->period32, 'period31');
    }

    public function updatedPeriod32($period32)
    {
        $this->resetErrorBag(['period32']);
        $this->period32 = $period32;

        $this->validatePeriods($this->period32, $this->period31, 'period32');
        $this->validatePeriods($this->period31, $this->period32, 'period32');
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
        }

        $this->hasErrorsHere = $errors;


    }



    public function submit()
    {
        // dd(Period::all());

        $this->reset('hasErrorsHere');
        $errors = $this->getErrorBag();
        $school_year = session('school_year_selected');

        if($school_year){
            $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            if($school_year_model){
                $this->school_year_model = $school_year_model;
            }
        }

        if(!$this->validate()){

        }
        else{

            $this->validatePeriods($this->period11, $this->period12, 'period12');
            $this->validatePeriods($this->period12, $this->period21, 'period21');
            $this->validatePeriods($this->period21, $this->period22, 'period21');
            $this->validatePeriods($this->period21, $this->period22, 'period22');

            if($this->semestre_type == 'Trimestre'){
                $this->validate(['period31' => 'required', 'period32' => 'required']);

                $this->validatePeriods($this->period31, $this->period32, 'period31');
                $this->validatePeriods($this->period32, $this->period31, 'period32');
                $this->validatePeriods($this->period31, $this->period32, 'period32');

            }

            if($this->school_year_model && !$this->hasErrorsHere){
                DB::transaction(function($e) {
                    try {
                        $semestre1 = Period::create([
                            'start' => $this->period11,
                            'end' => $this->period12,
                            'object' => 'Semestre 1',
                            'school_year_id' => $this->school_year_model->id,
                        ]);
                        if($semestre1){
                            try {
                                $semestre2 = Period::create([
                                    'start' => $this->period21,
                                    'end' => $this->period22,
                                    'object' => 'Semestre 2',
                                    'school_year_id' => $this->school_year_model->id,
                                ]);

                                if($this->semestre_type == 'Trimestre'){
                                    if($semestre2){
                                        try {
                                            $semestre3 = Period::create([
                                                'start' => $this->period31,
                                                'end' => $this->period32,
                                                'object' => 'Semestre 3',
                                                'school_year_id' => $this->school_year_model->id,
                                            ]);
                                            
                                        } 
                                        catch (Exception $e3) {
                                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "Une erreure est survenue lors de la mise à jour du calendrier scolaire!", 'type' => 'error']);
                                        }
                                    }

                                }
                            } 

                            catch (Exception $e2) {
                                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "Une erreure est survenue lors de la mise à jour du calendrier scolaire!", 'type' => 'error']);
                            }

                        }
                    } 
                    catch (Exception $e1) {
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "Une erreure est survenue lors de la mise à jour du calendrier scolaire!", 'type' => 'error']);
                    }
                });

                DB::afterCommit(function(){
                    $this->dispatchBrowserEvent('hide-form');
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour réussie!', 'message' => "Le calendrier scolaire a été défini avec succès", 'type' => 'success']);
                });

            }
        }


    }
}
