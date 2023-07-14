<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PupilAbsences extends Model
{
    use HasFactory;
    use DateFormattor;
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


    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }


    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

}
