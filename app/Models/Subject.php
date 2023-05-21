<?php

namespace App\Models;

use App\Models\AverageModality;
use App\Models\Classe;
use App\Models\Coeficient;
use App\Models\Image;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $fillable = [
        'name',
        'level_id',
    ];



    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }

    public function teachers()
    {
        return $this->morphedByMany(Teacher::class, 'subjectable');
    }

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }


    public function classes()
    {
        return $this->morphToMany(Classe::class, 'classable');
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function coeficients()
    {
        return $this->hasMany(Coeficient::class);
    }

    public function averageModalities()
    {
        return $this->hasMany(AverageModality::class);
    }

    public function getAverageModalityOf($classe_id, string $school_year, $semestre = null)
    {
       return $this->averageModalities()->where('classe_id', $classe_id)->where('school_year', $school_year)->where('semestre', $semestre)->first();
    }


}
