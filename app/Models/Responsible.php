<?php

namespace App\Models;

use App\Models\Pupil;
use App\Models\Classe;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Responsible extends Model
{
    use HasFactory;

    protected $fillable = [
        'pupil_id',
        'classe_id',
        'rank',
    ];

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }


    
    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }



}
