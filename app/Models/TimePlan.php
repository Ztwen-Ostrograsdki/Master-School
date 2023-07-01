<?php

namespace App\Models;

use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimePlan extends Model
{
    use HasFactory;

    protected $fillable = ['classe_id', 'subject_id', 'level_id', 'teacher_id', 'start', 'end', 'duration', 'closed', 'authorized', 'description', 'school_year_id', 'day', 'day_index', 'creator', 'editor', 'user_id'];


    public function school_years()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }




}
