<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;

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


    // public function prunable(): Builder
    // {
    //     return static::where('created_at', '<=', now()->subMonth());

    // }



}
