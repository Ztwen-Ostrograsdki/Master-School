<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassesSecurity extends Model
{
    use HasFactory;

    /**
     * @int duration in hour;
     */

    protected $fillable = [
        'teacher_id', 
        'classe_id', 
        'pupil_id', 
        'level_id', 
        'school_year_id', 
        'subject_id', 
        'description', 
        'activated', 
        'duration', 
        'locked_classe', 
        'locked_marks', 
        'closed_classe', 
        'closed', 
        'locked', 
        'locked_marks_updating'
    ];


}
