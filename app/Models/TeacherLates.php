<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherLates extends Model
{
    use HasFactory;
    protected $fillable = [
        'teacher_id',
        'classe_id',
        'duration',
        'coming_hour',
        'justified',
        'motif',
        'school_year',
        'school_year_id',
        'horaire',
        'date',
        'subject_id',
        'semestre',
    ];
}
