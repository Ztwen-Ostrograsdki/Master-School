<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkStopped extends Model
{
    use HasFactory;

    private $table_name = "mark_stoppeds";

    protected $fillable = ['stopped', 'school_year_id', 'semestre', 'level_id'];
    
}
