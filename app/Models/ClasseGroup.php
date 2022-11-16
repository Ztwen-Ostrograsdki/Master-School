<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Coeficient;
use App\Models\Level;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClasseGroup extends Model
{
    use HasFactory;
    use SoftDeletes;


    protected $fillable = [
        'name',
        'slug',
        'category',
        'option',
        'filial',
        'level_id',
    ];


    public function classes()
    {
        return $this->hasMany(Classe::class);
    }


    public function level()
    {
        return $this->belongsTo(Level::class);
    }


    public function coeficients()
    {
        return $this->hasMany(Coeficient::class);
    }


    public function subjects()
    {
        return $this->morphedByMany(Subject::class, 'promotable');
    }


    public function getCoef($subject_id)
    {
        return $this->coeficients()->where('subject_id', $subject_id)->first();
    }




}
