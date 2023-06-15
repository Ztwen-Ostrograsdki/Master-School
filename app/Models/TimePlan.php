<?php

namespace App\Models;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TimePlan extends Model
{
    use HasFactory;

    protected $fillable = ['classe_id', 'subject_id', 'level_id', 'teacher_id', 'start', 'end', 'duration', 'closed', 'authorized', 'description', 'school_year_id', 'day'];


    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }




}
