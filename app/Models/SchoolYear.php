<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AE;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\ClasseHistory;
use App\Models\ClassePupilSchoolYear;
use App\Models\ClassesSecurity;
use App\Models\Level;
use App\Models\Mark;
use App\Models\MarkStopped;
use App\Models\Period;
use App\Models\PrincipalTeacher;
use App\Models\Pupil;
use App\Models\PupilAbsences;
use App\Models\PupilCursus;
use App\Models\PupilLates;
use App\Models\QotHour;
use App\Models\RelatedMark;
use App\Models\Responsible;
use App\Models\School;
use App\Models\SchoolHistory;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherAbsences;
use App\Models\TeacherCursus;
use App\Models\TeacherLates;
use App\Models\TimePlan;
use App\Models\TransferFile;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolYear extends Model
{
    use HasFactory;

    use ModelQueryTrait;

    use DateFormattor;

    protected $fillable = ['school_year'];

    public $calendars;
    public $local_events = [];
    public $semestre_type = 'Semestre';
    public $semestres = [1, 2];

    public function marksWasAlreadyStopped($semestre = 1, $date = null)
	{
		$semestre_calendar = $this->periods()->where('target', 'semestre-trimestre')->where('semestre', $semestre)->first();
		if($semestre_calendar){
			$start = $semestre_calendar->start;
			$end = $semestre_calendar->end;

			return !$this->thisDateIsBetween($start, $end, $date);

		}

		return true;
	}

    public function epreuves()
    {
        return $this->hasMany(TransferFile::class);
    }


    public function marks_stopped()
	{
		return $this->hasMany(MarkStopped::class);
	}

    public function levels()
	{
		return $this->morphedByMany(Level::class, 'schoolable');
	}

	public function qotHours()
	{
		return $this->hasMany(QotHour::class);
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
		return $this->hasMany(PupilCursus::class);
	}

    public function pupilLates()
	{
		return $this->hasMany(PupilLates::class);
	}
	
    public function pupilAbsences()
	{
		return $this->hasMany(PupilAbsences::class);
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



    public function calendarProfiler()
    {
    	$school = School::first();

        $current_period = [];

        $this->local_events = config('app.local_events');

        if($school){

            if($school->trimestre){

                $this->semestre_type = 'Trimestre';

                $this->semestres = [1, 2, 3];
            }
            else{

                $semestres = [1, 2];
            }

            
        }
        $semestre_calendars = [];

        $school_calendars = [];

        $s_cals = $this->periods()->where('periods.target', 'semestre-trimestre')->get();
        
        foreach($s_cals as $s_cal){

            $start_string = $this->__getDateAsString($s_cal->start, false);

            $end_string = $this->__getDateAsString($s_cal->end, false);

            $weeks = Carbon::parse($s_cal->end)->floatDiffInRealWeeks($s_cal->start);

            $days = floor(($weeks - floor($weeks)) * 7);

            $duration = floor($weeks) . ' Semaines ' . $days . ' Jours';

            $in_period = $this->thisDateIsBetween($s_cal->start, $s_cal->end);

            $semestre_calendars[$s_cal->id] = [
                'model' => $s_cal,
                'start' => $start_string,
                'end' => $end_string,
                'duration' => $duration,
                'in_period' => $in_period,
            ];

            if($in_period){

                $passed = '';

                $rest = '';

                $today = Carbon::now();

                $weeks_passed = Carbon::parse($s_cal->today)->floatDiffInRealWeeks($s_cal->start);

                $days_passed = floor(($weeks_passed - floor($weeks_passed)) * 7);

                if(floor($weeks_passed)){

                    $passed.= floor($weeks_passed) . ' Semaines ';
                }
                if($days_passed){

                    $passed.= $days_passed . ' Jours';

                }

                $weeks_rest = Carbon::parse($s_cal->today)->floatDiffInRealWeeks($s_cal->end);

                $days_rest = floor(($weeks_rest - floor($weeks_rest)) * 7);

                if(floor($weeks_rest)){

                    $rest.= floor($weeks_rest) . ' Semaines ';
                }
                if($days_rest){

                    $rest.= $days_rest . ' Jours';

                }
                $current_period = [
                    'target' => $s_cal->object,
                    'passed' => $passed,
                    'rest' => $rest
                ];

            }
        }
        return ['current_period' => $current_period, 'semestre_calendars' => $semestre_calendars];


    }


    public function findClasse(int $classe_id)
    {
    	return $this->classes()->where('classes.id', $classe_id)->first();
    }


    public function findClasses(array $classes_id)
    {
    	return $this->classes()->whereIn('classes.id', $classes_id)->get();
    }


    public function findPupil(int $pupil_id)
    {
    	return $this->pupils()->where('pupils.id', $pupil_id)->first();
    }

    public function findPupils($pupils_id)
    {
    	return $this->pupils()->whereIn('pupils.id', $pupils_id)->get();
    }


    public function findTeacher(int $teacher_id)
    {
    	return $this->teachers()->where('teachers.id', $teacher_id)->first();
    }

    public function findTeachers(array $teachers_id)
    {
    	return $this->teachers()->whereIn('teachers.id', $teachers_id)->get();
    }

    public function findClasseGroup(int $classe_group_id)
    {
    	return $this->classe_groups()->where('classe_groups.id', $classe_group_id)->first();
    }


    public function findClasseGroups(array $classe_groups_id)
    {
    	return $this->classe_groups()->whereIn('classe_groups.id', $classe_groups_id)->get();
    }



    public function histories()
    {

        return $this->hasMany(SchoolHistory::class);

    }

    public function isNotPupilOfThisSchoolYear($pupil_id)
    {
        $has = $this->findPupil($pupil_id);

        return $has ? false : true;
    }




}
