<?php

namespace App\Helpers\ModelsHelpers;

use App\Models\SchoolYear;
use Illuminate\Support\Carbon;

trait ModelQueryTrait{


    public function getClasses($school_year = null)
    {

    }


    /**
     * @return SchoolYear Model::class;
     */
    public function getSchoolYear()
    {
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

        if(session()->has('semestre_type') && session('semestre_type')){
            $semestre_type = session('semestre_type');
            session()->put('semestre_type', $semestre_type);
        }
        else{
            session()->put('semestre_type', $semestre_type);
        }

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
        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = session('semestre_selected');
        }
        session()->put('semestre_selected', $semestre);
    }



}