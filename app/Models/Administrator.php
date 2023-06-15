<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'status', 'abilities', 'authorized', 'canManage'];

    public function users()
    {
        return $this->hasMany(User::class);
    }
}
