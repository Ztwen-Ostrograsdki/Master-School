<?php

namespace App\Models;

use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class ClasseHistory extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'classe_id',
    ];

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }
}
