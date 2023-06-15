<?php

namespace App\Models;

use App\Models\AE;
use App\Models\Classe;
use App\Models\ClasseHistory;
use App\Models\ClassesSecurity;
use App\Models\Level;
use App\Models\Mark;
use App\Models\Period;
use App\Models\Pupil;
use App\Models\PupilAbsences;
use App\Models\PupilCursus;
use App\Models\PupilLates;
use App\Models\RelatedMark;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAbsences;
use App\Models\TeacherCursus;
use App\Models\TeacherLates;
use App\Models\TimePlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    protected $fillable = ['school_year'];



    public function levels()
	{
		return $this->morphedByMany(Level::class, 'schoolable');
	}


	public function periods()
	{
		return $this->hasMany(Period::class);

	}

	public function timePlans()
	{
		return $this->morphedByMany(TimePlan::class, 'schoolable');
	}

	public function classes()
	{
		return $this->morphedByMany(Classe::class, 'schoolable');
	}


    public function marks()
	{
		return $this->morphedByMany(Mark::class, 'schoolable');
	}

	public function related_marks()
	{
		return $this->morphedByMany(RelatedMark::class, 'schoolable');
	}

    public function classeHistories()
	{
		return $this->morphedByMany(ClasseHistory::class, 'schoolable');
	}


    public function teachers()
	{
		return $this->morphedByMany(Teacher::class, 'schoolable');
	}
    public function aes()
	{
		return $this->morphedByMany(AE::class, 'schoolable');
	}
    public function teacherCursus()
	{
		return $this->morphedByMany(TeacherCursus::class, 'schoolable');
	}
    public function teacherLates()
	{
		return $this->morphedByMany(TeacherLates::class, 'schoolable');
	}
    public function teacherAbsences()
	{
		return $this->morphedByMany(TeacherAbsences::class, 'schoolable');
	}



    public function subjects()
	{
		return $this->morphedByMany(Subject::class, 'schoolable');
	}


	public function pupils()
	{
		return $this->morphedByMany(Pupil::class, 'schoolable');
	}
    public function pupilCursus()
	{
		return $this->morphedByMany(PupilCursus::class, 'schoolable');
	}
    public function pupilLates()
	{
		return $this->morphedByMany(PupilLates::class, 'schoolable');
	}
    public function pupilAbsences()
	{
		return $this->morphedByMany(PupilAbsences::class, 'schoolable');
	}


	public function securities()
    {
        return $this->hasMany(ClassesSecurity::class);
    }







}
