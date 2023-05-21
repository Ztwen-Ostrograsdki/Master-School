<?php

namespace App\Models;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\TeacherAbsences;
use App\Models\TeacherCursus;
use App\Models\TeacherLates;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Teacher extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelQueryTrait;

    protected $fillable = [
        'name',
        'surname',
        'contacts',
        'residence',
        'level_id',
        'user_id',
        'birth_day',
        'nationality',
        'authorized',
        'marital_status',
    ];


    public $imagesFolder = 'teachersPhotos';

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }


    public function cursus()
    {
        return $this->hasMany(TeacherCursus::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classes()
	{
		return $this->morphToMany(Classe::class, 'classable');
	}



    public function getTeachersCurrentClasses()
    {
        $school_year_model = $this->getSchoolYear();
        $current_classes = [];
        
        if($this->hasClasses()){
            $classes_id = $school_year_model->teacherCursus()->where('teacher_id', $this->id)->whereNull('end')->pluck('classe_id')->toArray();

            foreach($classes_id as $classe_id){
                $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
                $current_classes[] = $classe;
            }
        }
        return $current_classes;

    }


    public function hasClasses()
    {
        $school_year_model = $this->getSchoolYear();
        $cursuses = $school_year_model->teacherCursus()->where('teacher_id', $this->id)->whereNull('end')->count();

        return $cursuses > 0;
    }

    public function subjects()
	{
		return $this->morphToMany(Subject::class, 'subjectable');
	}


    public function speciality()
    {
        $has = $this->subjects;
        if($has){
            $subject = $this->subjects()->first();
            if($subject){
                return $subject;
            }
        }

        return null;

    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function lates()
    {
        return $this->hasMany(TeacherLates::class);
    }
    
    public function absences()
    {
        return $this->hasMany(TeacherAbsences::class);
    }


}
