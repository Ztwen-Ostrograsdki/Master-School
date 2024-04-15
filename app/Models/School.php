<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class School extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'trimestre',
        'semestre',
        'users_counter',
        'parents_counter',
        'pupils_counter',
        'classes_counter',
        'teachers_counter',
        'subjects_counter',
        'classe_groups_counter',
        'promotions_counter',
        'marks_counter',
        'epreuves_counter',
    ];
}
