<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epreuves extends Model
{
    /**
      * @properties Duration unit: minutes 
    */ 
    
    use HasFactory;

    protected $table_name = 'epreuves';

    protected $fillable = [
        'name', 'classe_id', 'semestre', 'school_year_id', 'subject_id', 'description', 'target', 'duration', 'classe_group_id', 'teacher_id', 'author', 'session', 'exam_name', 'blocked', 'authorized', 'level_id'
    ];



    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }








}
