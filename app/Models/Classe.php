<?php

namespace App\Models;

use App\Helpers\ModelTraits\ClasseTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AverageModality;
use App\Models\ClassePupilSchoolYear;
use App\Models\Coeficient;
use App\Models\Image;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\PupilCursus;
use App\Models\RelatedMark;
use App\Models\Responsible;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classe extends Model
{
    use ModelQueryTrait;
    use HasFactory;
    use SoftDeletes;
    use ClasseTraits;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level_id',
        'closed',
        'locked',
        'teacher_id'
    ];




    public function classePupilSchoolYear()
    {
        return $this->hasMany(ClassePupilSchoolYear::class);
    }

    public function related_marks()
    {
        return $this->hasMany(RelatedMark::class);
    }

    public function getPupils(int $school_year)
    {
        $pupils = [];
        $c_p_s_ys = $this->classePupilSchoolYear()->where('school_year_id', $school_year);
        if($c_p_s_ys->get() && count($c_p_s_ys->get()) > 0){
            $pupils_ids = $c_p_s_ys->pluck('pupil_id')->toArray();

            foreach ($pupils_ids as $pupil_id) {
                $pupil = Pupil::find($pupil_id);
                if($pupil){
                    $pupils[] = $pupil;
                }
            }

        }

        return $pupils;


    }



    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }

    public function classePupils()
	{
		return $this->morphedByMany(Pupil::class, 'classable');
	}
    

    public function pp()
    {
        return $this->hasOne(Teacher::class);
    }

    public function cursus()
    {
        return $this->hasMany(PupilCursus::class);
    }

    public function pupils()
    {
        return $this->hasMany(Pupil::class);
    }
    public function responsibles()
    {
        return $this->hasMany(Responsible::class);
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
    

    public function subjects()
	{
		return $this->morphedByMany(Subject::class, 'classable');
	}

    /**
     * To get all teachers of this classe
     * @return [type] [description]
     */
    public function teachers()
    {
    	return $this->morphedByMany(Teacher::class, 'classable');
    }

    /**
     * To get the principal
     * @return [type] [description]
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }


    public function coeficients()
    {
        return $this->hasMany(Coeficient::class);
    }

    public function averageModalities()
    {
        return $this->hasMany(AverageModality::class);
    }

    public function alreadyJoinedToThisYear(int $school_year = null)
    {
        $classe_school_years = $this->school_years;
        if($school_year == null){
            $school_year = $this->getSchoolYear()->id;
        }
        if($classe_school_years && count($classe_school_years) > 0){
            $school_years_id_array = $classe_school_years->pluck('id')->toArray();

            return in_array($school_year, $school_years_id_array);
        }

        return false;

    }


}
