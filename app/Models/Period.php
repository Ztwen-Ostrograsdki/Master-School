<?php

namespace App\Models;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Period extends Model
{
    use HasFactory;

    protected $fillable = ['start', 'end', 'object', 'description', 'school_year_id'];



    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }
}
