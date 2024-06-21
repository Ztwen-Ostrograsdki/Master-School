<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasseMarksStoppedForSchoolYear extends Model
{
    use HasFactory;

    private $table_name = 'classe_marks_stopped_for_school_years';


    protected $fillable = ['classe_id', 'school_year_id', 'semestre', 'subject_id', 'activated', 'for_update', 'for_create', 'for_delete', 'level_id'];


}
