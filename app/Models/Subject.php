<?php

namespace App\Models;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AE;
use App\Models\AverageModality;
use App\Models\Classe;
use App\Models\Coeficient;
use App\Models\Image;
use App\Models\Level;
use App\Models\QotHour;
use App\Models\SchoolYear;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Subject extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelQueryTrait;

    protected $fillable = [
        'name',
        'level_id',
    ];


    public $tables = [
    ];


    public function qotHours()
    {
        return $this->hasMany(QotHour::class);
    }

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

    public function ae()
    {
        return $this->hasOne(AE::class);
    }

    public function hasAE()
    {
        $school_year_model = $this->getSchoolYear();
        return $school_year_model->aes()->where('subject_id', $this->id)->count() > 0;
    }


    public function getCurrentAE()
    {
        $school_year_model = $this->getSchoolYear();
        if($this->hasAE()){
            $ae = $school_year_model->aes()->where('subject_id', $this->id)->first();
            return $ae->teacher ? $ae->teacher : null;
        }

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

    public function getSimpleName()
    {
        return mb_substr($this->name, 0, 3);
    }


}
