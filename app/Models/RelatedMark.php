<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;

class RelatedMark extends Model
{
    use DateFormattor, SoftDeletes, Prunable;
    
    protected $fillable = [
        'value', 
        'pupil_id', 
        'subject_id', 
        'school_year_id', 
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

    public function prunable(): Builder
    {
        return static::where('deleted_at', '<=', now()->subMonth());

    }

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
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

    public function getValue()
    {
        if ($this->type == 'bonus') {
            return  '+ ' . $this->value;
        }
        elseif ($this->type == 'minus') {
            return  '- ' . $this->value;
        }

    }

}
