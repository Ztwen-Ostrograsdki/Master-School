<?php

namespace App\Models;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class TeacherAbsences extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'classe_id',
        'motif',
        'justified',
        'horaire',
        'school_year',
        'school_year_id',
        'date',
        'subject_id',
        'semestre',
    ];

}
