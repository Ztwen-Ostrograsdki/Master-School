<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\ClasseGroup;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Filial extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'slug', 'option', 'closed', 'locked'];


    public function classes()
    {
        return $this->hasMany(Classe::class);
    }


    public function classe_groups()
    {
        return $this->hasMany(ClasseGroup::class);
    }
}
