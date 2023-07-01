<?php

namespace App\Helpers\ModelsHelpers;

use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;

trait ModelQueryTrait{


    public function getClasses($school_year = null)
    {

    }


    /**
     * @return SchoolYear Model::class;
     */
    public function getSchoolYear($school_year = null)
    {
        if($school_year){
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::where('id', $school_year)->first();
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }

            return $school_year_model;
        }

        $school_year = null;
        $current_month_index = intval(date('m'));
        if($current_month_index >= 10){
            $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
        }
        else{
            $school_year = intval(date('Y') - 1) . ' - ' . intval(date('Y'));
        }
        if(session()->has('school_year_selected') && session('school_year_selected')){
            $school_year = session('school_year_selected');
            session()->put('school_year_selected', $school_year);
        }
        else{
            session()->put('school_year_selected', $school_year);
        }

        $this->__setSemestreIndex();

        return SchoolYear::where('school_year', $school_year)->first();
    }



    public function __setSemestreIndex()
    {
        $semestre_type = 'Semestre';
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $semestre = session('semestre_selected');

        $school = School::first();
        if($school && $school_year_model){
            $semestre_calendars = $school_year_model->periods()->where('periods.target', 'semestre-trimestre')->get();
            if(session()->has('semestre_type') && session('semestre_type')){
                $semestre_type = session('semestre_type');
                session()->put('semestre_type', $semestre_type);
            }
            else{
                if($school->trimestre){
                    $semestre_type = 'Trimestre';
                }
                session()->put('semestre_type', $semestre_type);
            }

            if(session()->has('semestre_selected') && session('semestre_selected')){
                $semestre = session('semestre_selected');
            }
            else{
                if($semestre_calendars){

                    foreach($semestre_calendars as $calendar){
                        $is_current = $this->thisDateIsBetween($calendar->start, $calendar->end);
                        if($is_current){
                            $semestre = str_replace($semestre_type . ' ', '', $calendar->object);
                        }
                    }
                }
                else{
                    $current_month_index = intval(date('m'));

                    if ($semestre_type == 'Semestre') {
                        if(in_array($current_month_index, [10, 11, 12, 1, 2]) ){
                            $semestre = 1;
                        }
                        else{
                            $semestre = 2;
                        }
                    }
                    else{
                        if(in_array($current_month_index, [10, 11, 12, 1]) ){
                            $semestre = 1;
                        }
                        elseif (in_array($current_month_index, [2, 3, 4])) {
                            $semestre = 2;
                        }
                        else{
                            $semestre = 3;
                        }
                        
                    }
                }
            }
        }
        session()->put('semestre_selected', $semestre);
    }


    public function thisDateIsBetween($start, $end, $date = null)
    {
        if(!$date){
            $date = Carbon::now();
        }

        if($start && $end && $date){
            $timestamp_of_date = Carbon::parse($date)->timestamp;
            $timestamp_start = Carbon::parse($start)->timestamp;
            $timestamp_end = Carbon::parse($end)->timestamp;
            
            if($timestamp_of_date >= $timestamp_start &&  $timestamp_of_date <= $timestamp_end){
                return true;
            }
            return false;
        }

        return false;
    }

}