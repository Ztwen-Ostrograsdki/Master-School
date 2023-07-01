<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PrincipalTeacher extends Model
{
    use HasFactory;

    protected $fillable = ['teacher_id', 'school_year_id', 'classe_id'];


    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }


    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


}
