<?php

namespace App\Models;

use App\Models\Image;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Coeficient;
use App\Models\SchoolYear;
use App\Models\PupilCursus;
use App\Models\Responsible;
use App\Models\AverageModality;
use Illuminate\Database\Eloquent\Model;
use App\Helpers\ModelTraits\ClasseTraits;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Classe extends Model
{
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


}
