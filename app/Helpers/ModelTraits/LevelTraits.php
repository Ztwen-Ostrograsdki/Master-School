<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\SchoolYear;




trait LevelTraits{


    use ModelQueryTrait;
    public function getLevelClasses($school_year = null)
    {
        if(!$school_year){
            $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
            if(session()->has('school_year_selected') && session('school_year_selected')){
                $school_year = session('school_year_selected');
                session()->put('school_year_selected', $school_year);
            }
            else{
                session()->put('school_year_selected', $school_year);
            }
        }
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();

        if($school_year_model){
            $classes = $school_year_model->classes()->where('level_id', $this->id)->get();
            if($classes->count() > 0){
                return $classes;
            }
        }
        return [];

    }


    public function getLevelPupils($school_year = null)
    {
        if(!$school_year){
            $school_year_model = $this->getSchoolYear();
        }

        $school_year_model = $this->getSchoolYear();

        if($school_year_model){
            $pupils = $school_year_model->pupils()->where('level_id', $this->id)->get();
            if($pupils->count() > 0){
                return $pupils;
            }
        }
        return [];

    }











}