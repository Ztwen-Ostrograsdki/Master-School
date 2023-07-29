<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Averages extends Model
{
    use HasFactory;

    protected $fillable = ['classe_id', 'school_year_id', 'semestre', 'moy', 'rank', 'base', 'exp', 'pupil_id', 'mention', 'min', 'max'];


    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


}
