<?php

namespace App\Models;

use App\Models\AE;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\ClasseHistory;
use App\Models\ClassePupilSchoolYear;
use App\Models\ClassesSecurity;
use App\Models\Level;
use App\Models\Mark;
use App\Models\Period;
use App\Models\PrincipalTeacher;
use App\Models\Pupil;
use App\Models\PupilAbsences;
use App\Models\PupilCursus;
use App\Models\PupilLates;
use App\Models\RelatedMark;
use App\Models\Responsible;
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

	public function responsibles()
	{
		return $this->hasMany(Responsible::class);

	}

	public function principals()
	{
		return $this->hasMany(PrincipalTeacher::class);

	}


	public function periods()
	{
		return $this->hasMany(Period::class);

	}

	public function timePlans()
	{
		return $this->hasMany(TimePlan::class);
	}

	public function classes()
	{
		return $this->morphedByMany(Classe::class, 'schoolable');
	}

	public function classe_groups()
	{
		return $this->morphedByMany(ClasseGroup::class, 'schoolable');
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


    public function classeWithPupils()
    {
    	return $this->hasMany(ClassePupilSchoolYear::class);
    }


    public function getClassePupils($classe_id)
    {
    	$pupils = [];

    	$data = $this->classeWithPupils()->where('classe_id', $classe_id)->get();
    	if(count($data) > 0){
    		foreach($data as $datum){
    			$pupils = $this->pupils()->where('pupils.id', $datum->pupil_id)->orderBy('pupils.firstName', 'asc')->orderBy('pupils.lastName', 'asc')->get();
    		}
    	}

    	return $pupils;
    }







}
