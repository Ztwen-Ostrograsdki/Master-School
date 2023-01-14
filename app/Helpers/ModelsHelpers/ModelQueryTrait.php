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

        return SchoolYear::where('school_year', $school_year)->first();
    }



}