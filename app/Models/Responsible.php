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
        'respo_1',
        'respo_2',
        'respo_3',
        'respo_4',
        'respo_5',
        'respo_6',
        'respo_7',
        'respo_8',
        'school_year_id',
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


}
