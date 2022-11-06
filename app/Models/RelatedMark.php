<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RelatedMark extends Model
{
    protected $fillable = [
        'value', 
        'pupil_id', 
        'subject_id', 
        'classe_id', 
        'motif', 
        'trimestre', 
        'semestre', 
        'type', 
        'date', 
        'horaire', 
        'month', 
        'editing_value', 
        'edited', 
        'level_id', 
        'creator', 
        'editor', 
        'authorized',
        'blocked',
        'forced_mark',
    ];

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }

    public function school_year()
    {
        if($this->school_years && count($this->school_years) > 0){
            return $this->school_years()->first();
        }
        return null;
    }

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function level()
    {
        return $this->belongsTo(Level::class);
    }

}
