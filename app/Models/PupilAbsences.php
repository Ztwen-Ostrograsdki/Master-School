<?php

namespace App\Models;

use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PupilAbsences extends Model
{
    use HasFactory;
    protected $fillable = [
        'pupil_id',
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


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

}
