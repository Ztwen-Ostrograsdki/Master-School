<?php

namespace App\Models;

use App\Models\Teacher;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class AE extends Model
{
    use HasFactory;

    protected $fillable = [
        'teacher_id',
        'subject_id',
        'blocked',
    ];

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }


    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }
    
}
