<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PupilLates extends Model
{
    use HasFactory;
    use DateFormattor;

    protected $fillable = [
        'pupil_id',
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

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    
}
