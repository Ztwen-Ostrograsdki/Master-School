<?php

namespace App\Models;

use App\Models\User;
use App\Models\Level;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\SchoolYear;
use App\Models\TeacherLates;
use App\Models\TeacherCursus;
use App\Models\TeacherAbsences;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Teacher extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
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

    public function subjects()
	{
		return $this->morphToMany(Subject::class, 'subjectable');
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
