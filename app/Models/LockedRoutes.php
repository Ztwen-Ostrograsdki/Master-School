<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LockedRoutes extends Model
{
    use HasFactory;

    use DateFormattor;

    protected $fillable = ['path', 'url', 'routeName', 'delay', 'expired_date', 'user_id', 'activated', 'targeted_users'];

    public function routes()
    {
        return [];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function users()
    {
        $targeted_users = $this->targeted_users;

        $users = [];

        if($targeted_users){

            $ids = explode('-', $targeted_users);

            if(count($ids) > 0){

                $users = User::whereIn('users.id', $ids)->get();

            }

        }

        return $users;
    }
}
