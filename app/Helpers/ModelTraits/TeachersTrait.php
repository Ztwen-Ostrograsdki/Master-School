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
                if(!$classe->hasSecurities()){
                    $locked_marks_updating = $classe->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->where('teacher_id', $this->id)->get();

                    if(!$locked_marks_updating){
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






}
