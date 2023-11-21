<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasseSanctionables extends Model
{
    use HasFactory;

    protected $fillable = ['classe_id', 'creator_id', 'editor_id', 'subject_id', 'school_year_id', 'semestre', 'activated', 'min', 'max'];

    protected $table_name = ['classe_sanctionables'];


}
