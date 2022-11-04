<?php

namespace App\Helpers\ModelsHelpers;

use App\Models\SchoolYear;

trait ModelQueryTrait{


    public function getClasses($school_year = null)
    {

    }


    public function getSchoolYear()
    {
        $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
        if(session()->has('school_year_selected') && session('school_year_selected')){
            $school_year = session('school_year_selected');
            session()->put('school_year_selected', $school_year);
        }
        else{
            session()->put('school_year_selected', $school_year);
        }
        return SchoolYear::where('school_year', $school_year)->first();
    }



}