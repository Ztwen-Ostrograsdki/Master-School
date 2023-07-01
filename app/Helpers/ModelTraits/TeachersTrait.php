<?php
namespace App\Helpers\ModelTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassesSecurity;


trait TeachersTrait{

	use ModelQueryTrait;


	public function teacherCanUpdateMarksInThisClasse($classe_id)
    {
        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

        if($classe){
            $teacher_classes = $this->getTeachersCurrentClasses();
            if(array_key_exists($classe->id, $teacher_classes)){
                if($classe->hasSecurities()){
                    $locked_marks_updating_for_teacher = $classe->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->where('teacher_id', $this->id)->get();
                    $locked_marks_updating_for_all = $classe->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->get();

                    if(!$locked_marks_updating_for_all && !$locked_marks_updating_for_teacher){
                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return true;
                }
            }
            else{
                return false;
            }
        }
        else{
            return false;

        }
    }


    public function getCurrentTimePlans($classe_id = null, $school_year_id = null)
    {
        $school_year_model = $this->getSchoolYear();

        if(!$school_year_id){
            $school_year_id = $school_year_model->id;

        }
        if($classe_id){
            return $this->timePlans()->where('time_plans.classe_id', $classe_id)->where('time_plans.school_year_id', $school_year_id)->where('time_plans.subject_id', $this->speciality()->id)->orderBy('day_index', 'asc')->get();
        }
        else{
            return $this->timePlans()->where('time_plans.school_year_id', $school_year_id)->where('time_plans.subject_id', $this->speciality()->id)->orderBy('day_index', 'asc')->get();
        }
    }


    public function teacherWasFreeInThisTime($start, $end, $day, $school_year_id = null)
    {
        if(!$school_year_id){
            $school_year_id = $this->getSchoolYear()->id;
        }

        $times = $this->timePlans()->where('time_plans.school_year_id', $school_year_id)->where('time_plans.day', $day)->get();

        if(count($times) > 0){
            foreach($times as $time){
                $time_start = $time->start;
                $time_end = $time->end;

                if($end >= $time_start && $end <= $time_end){
                    return false ;
                    break;
                }
                elseif($start >= $time_start && $end <= $time_end){
                    return false;
                    break;
                }
                elseif($start <= $time_end && $end >= $time_end){
                    return false;
                    break;
                }
                elseif($start <= $time_start && $end >= $time_end){
                    return false;
                    break;
                }

            }

            return true;

        }
        return true;
    }


    public function getFormatedName()
    {
        return strtoupper($this->name) . ' ' . ucwords($this->surname);
    }





}
