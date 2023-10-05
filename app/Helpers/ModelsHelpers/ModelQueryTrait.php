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
        else{

            $school_year = null;

            $current_month_index = intval(date('m'));

            if($current_month_index >= 9){

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

            $model = SchoolYear::where('school_year', $school_year)->first();

            if($school_year && $model){

                $this->__setSemestreIndex($school_year);

                $school_year_model = $model;

            }
            else{

                $school_year_model = $this->getLastYear();

                $this->__setSemestreIndex($school_year_model->school_year);

            }

            session()->put('school_year_selected', $school_year_model->school_year);

            return $school_year_model;

        }

    }



    public function __setSemestreIndex($school_year = null)
    {
        $semestre_type = 'Semestre';

        $school_year_model = SchoolYear::where('school_year', $school_year)->first();

        $semestre = session('semestre_selected');

        $school = School::first();

        $no_current_calendar = true;

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

                    $semestre = null;

                    $calend = null;

                    foreach($semestre_calendars as $calendar){

                        $is_current = $this->thisDateIsBetween($calendar->start, $calendar->end);

                        if($is_current){

                            $semestre = str_replace($semestre_type . ' ', '', $calendar->object);

                            $calend = $calendar;

                            $no_current_calendar = false;
                        }
                    }

                    if($no_current_calendar == true){

                        

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

    public function theSemestreWasPassed($start, $end, $date = null)
    {
        if(!$date){

            $date = Carbon::now();
        }

        if($start && $end && $date){

            $timestamp_of_date = Carbon::parse($date)->timestamp;

            $timestamp_start = Carbon::parse($start)->timestamp;

            $timestamp_end = Carbon::parse($end)->timestamp;
            
            if($timestamp_of_date >= $timestamp_start &&  $timestamp_of_date >= $timestamp_end){

                return true;
            }

            return false;
        }

        return false;
    }


    public function getLastYear()
    {
        return SchoolYear::orderBy('school_year', 'desc')->first();
    }

    public function getSchoolYearBefor($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $sy = $school_year_model->school_year;

        $y = explode(' - ', $sy)[0];

        $y_bf = $y - 1 . ' - ' . $y;

        return SchoolYear::where('school_year', $y_bf)->first();

    }


    public function getSemestres()
    {
        $school = School::first();

        $semestres = [1, 2];

        if($school){

            if($school->trimestre){

                $semestres = [1, 2, 3];
            }
            else{

                $semestres = [1, 2];
            }
        }

        return $semestres;

    }

    public function assetIfTheSemestresWasAllPassed($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $semestre_calendars = $school_year_model->periods()->where('periods.target', 'semestre-trimestre')->get();

        foreach($semestre_calendars as $calendar){

            // $is_between = $this->thisDateIsBetween($calendar->start, $calendar->end);

            $is_passed = $this->theSemestreWasPassed($calendar->start, $calendar->end);

            if($is_passed){

                $passed = true;
            }
            else{

                $passed = false;

            }
        }


    }

}