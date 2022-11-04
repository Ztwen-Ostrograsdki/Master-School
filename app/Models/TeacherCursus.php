<?php

namespace App\Models;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherCursus extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'classe_id',
        'teacher_id',
        'school_year_id',
        'subject_id',
        'level_id',
        'start',
        'end',
        'fullTime'
    ];


    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }
}
