<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coeficient extends Model
{
    use HasFactory;

    protected $fillable = ['coef', 'classe_id', 'subject_id', 'school_year'];


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }





}
