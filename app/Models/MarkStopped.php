<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkStopped extends Model
{
    use HasFactory;

    protected $fillable = ['stopped', 'school_year_id', 'semestre'];
    
}
