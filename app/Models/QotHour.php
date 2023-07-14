<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class QotHour extends Model
{
    use HasFactory;

    protected $fillable = ['classe_id', 'subject_id', 'school_year_id', 'quota'];


    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }








}
