<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AverageModality extends Model
{
    use HasFactory;

    protected $fillable = ['modality', 'classe_id', 'subject_id', 'school_year'];



    
}
