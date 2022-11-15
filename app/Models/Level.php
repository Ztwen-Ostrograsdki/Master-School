<?php

namespace App\Models;

use App\Helpers\ModelTraits\LevelTraits;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\PupilCursus;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherCursus;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Level extends Model
{
    use HasFactory;
    use SoftDeletes;
    use LevelTraits;

    protected $fillable = [
        'name',
    ];


    public function getName()
    {
        if($this->name == 'maternal'){
            return ' La Maternelle';
        }
        elseif($this->name == 'primary'){
            return 'Le Primaire';
        }
        elseif($this->name == 'secondary'){
            return 'Le Secondaire';
        }
        elseif($this->name == 'superior'){
            return 'Le Supérieur';
        }
        else{
            return 'Inconnue';
        }
    }

    public function level_classes($school_year = null)
    {
        $classes = $this->classes;

        if(count($classes) > 0){

            if($school_year){
                if(is_numeric($school_year)){
                    $school_year_model = SchoolYear::find($school_year);
                }
                else{
                    $school_year_model = SchoolYear::where('school_year', $school_year)->first();

                }
                if($school_year_model){
                    $classes_of_this_year = $school_year_model->classes()->where('level_id', $this->id)->get();
                    if(count($classes_of_this_year) > 0){
                        return $classes_of_this_year;
                    }
                }


            }
            return [];
        }

        return [];

    }



    public function classe_groups()
    {
        return $this->hasMany(ClasseGroup::class);
    }

    public function pupils()
    {
        return $this->hasMany(Pupil::class);
    }


    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function subjects()
    {
        return $this->hasMany(Subject::class);
    }

    public function teachers()
    {
        return $this->hasMany(Teacher::class);
    }

    public function pupilsCursus()
    {
        return $this->hasMany(PupilCursus::class);
    }


    public function teachersCursus()
    {
        return $this->hasMany(TeacherCursus::class);
    }
}
