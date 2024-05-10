<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AverageModality extends Model
{
    use HasFactory;

    protected $fillable = ['modality', 'classe_id', 'subject_id', 'school_year', 'semestre', 'activated', 'locked'];

    protected $casts = ['trimestre'];


    public function canUpdate()
    {
        return $this->locked == false;
    }


    
}
