<?php

namespace App\Models;

use App\Models\TransferFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Transfer extends Model
{
    use HasFactory;

    protected $fillable = ['user_id', 'batch_id'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function files()
    {
        return $this->hasMany(TransferFile::class);
    }
}
