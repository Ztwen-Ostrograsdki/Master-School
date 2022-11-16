<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Coeficient extends Model
{
    use HasFactory;
    protected $fillable = ['coef', 'classe_group_id', 'subject_id', 'school_year_id'];


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classe_group()
    {
        return $this->belongsTo(ClasseGroup::class);
    }

    public function classes()
    {
        return $this->classe_group->classes;
    }

}
