<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockedUsersRequest extends Model
{
    use HasFactory;
    use DateFormattor;

    protected $fillable = ['user_id', 'requested', 'description', 'message'];


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();

        if($created_at){
            
            return $this->dateAgoToString;
        }
        return $this->dateAgoToStringForUpdated;
    }
}
