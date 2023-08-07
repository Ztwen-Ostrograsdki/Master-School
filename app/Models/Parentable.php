<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'key',
        'job',
        'residence',
        'authorized',
        'name',
        'contacts',
        'blocked',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roles()
    {
        $this->user->userRoles;
    }


    public function pupils()
    {
        return $this->hasMany(ParentPupil::class);
    }


    
}
