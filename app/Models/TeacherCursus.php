<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class TeacherCursus extends Model
{
    use HasFactory;
    use SoftDeletes;
    use DateFormattor;
    use ModelQueryTrait;

    protected $fillable = [
        'classe_id',
        'teacher_id',
        'school_year_id',
        'subject_id',
        'level_id',
        'start',
        'end',
        'fullTime',
        'teacher_has_worked'
    ];


    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }


    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();
        if($created_at){
            return $this->dateAgoToString;
        }
        return $this->dateAgoToStringForUpdated;
    }


    public function canMarksAsWorkedForTeacher()
    {
        $default_weeks = config('app.min_weeks_to_consider_that_teacher_has_worked_in_classe');

        $timestamp_for_created_at = Carbon::parse($this->created_at)->timestamp;

        $duration = (int)$this->__getTimestampInWeeksBetweenDates($timestamp_for_created_at, null, false);
        
        return $duration >= $default_weeks;
    }
}
