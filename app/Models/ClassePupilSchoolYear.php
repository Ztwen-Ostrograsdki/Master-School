<?php

namespace App\Models;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClassePupilSchoolYear extends Model
{
    use HasFactory;
    use ModelQueryTrait;

    protected $fillable = [
        'classe_id',
        'pupil_id',
        'school_year_id',
    ];


    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }

}
