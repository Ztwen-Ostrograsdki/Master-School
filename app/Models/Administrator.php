<?php

namespace App\Models;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Administrator extends Model
{
    use HasFactory;

    private $admin_abilities = [
        'admin' => 'Administrateur standart',
        'master' => 'Administrateur master',
        'default' => 'Administrateur'
    ];

    protected $fillable = ['user_id', 'status', 'abilities', 'authorized', 'canManage'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
