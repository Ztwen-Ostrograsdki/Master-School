<?php

namespace App\Models;

use App\Helpers\ActionsTraits\ModelActionTrait;
use App\Helpers\AdminTraits\AdminTrait;
use App\Helpers\DateFormattor;
use App\Helpers\UserTraits\MustVerifyEmailTrait;
use App\Helpers\UserTraits\UserPasswordManagerTrait;
use App\Helpers\UserTraits\UserTrait;
use App\Helpers\ZtwenManagers\GaleryManager;
use App\Models\Admin;
use App\Models\Administrator;
use App\Models\Image;
use App\Models\LockedUsersRequest;
use App\Models\MyNotifications;
use App\Models\Parentable;
use App\Models\ResetEmailConfirmation;
use App\Models\Role;
use App\Models\Teacher;
use App\Models\User as ModelsUser;
use App\Models\UserAdminKey;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable

{
    use HasApiTokens;
    use HasFactory;
    use HasProfilePhoto;
    use Notifiable;
    use TwoFactorAuthenticatable;
    use SoftDeletes;
    use DateFormattor;
    use ModelActionTrait;
    use MustVerifyEmailTrait;
    use AdminTrait;
    use UserPasswordManagerTrait;
    use UserTrait; 
    use GaleryManager;

    /**
     * The attributes that are mass assignable.
     *
     * @var string[]
     */
    protected $fillable = [
        'pseudo',
        'email',
        'sexe',
        'new_email',
        'password',
        'role_id',
        'school_year',
        'email_verified_token',
        'new_email_verified_token',
        'reset_password_token',
        'blocked',
        'locked',
        'token',
        'unlock_token',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    /**
     * The accessors to append to the model's array form.
     *
     * @var array
     */
    protected $appends = [
        'profile_photo_url',
    ];


    public $imagesFolder = 'usersPhotos';

    
    public function role()
    {
        return $this->hasOne(Role::class);
    } 


    public function lockedRequests()
    {
        return $this->hasOne(LockedUsersRequest::class);
    }


    public function getRole()
    {
        
        if($this->role_id){
            $role = Role::whereId($this->role_id)->first();
            return $role ? $role : null;
        }
        else{
            return null;

        }
    }

    public function admin()
    {
        return $this->hasOne(Admin::class);
    }


    public function administrator()
    {
        return $this->hasOne(Administrator::class);
    }



    public function parent()
    {
        return $this->hasOne(Parentable::class);
    }


    public function teacher()
    {
        return $this->hasOne(Teacher::class);
    }

    
    public function userAdminKey()
    {
        return $this->hasOne(UserAdminKey::class);
    }
    
    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }


    public function isAdmin()
    {
        return $this->administrator !== null;
    }

    public function isAdminAs($status)
    {
        return $this->isAdmin() && ($this->administrator && $this->administrator->status == $status);
    }

    public function isAuthorizedAdmin()
    {
        return $this->isAdmin() && ($this->administrator && $this->administrator->authorized && $this->administrator->canManage);
    }






    /**
     * To refresh the unread message of current model about an user
     *
     * @param [int] $user_id
     * @return void
     */
    public function refreshUnreadMessagesOf($user_id)
    {
        $messages = $this->getUnreadMessagesOf($user_id);
        foreach ($messages as $m){
            $m->update(['seen' => true]);
        }
    }


    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();
        if($created_at){
            return $this->dateAgoToString;
        }
        return $this->dateAgoToStringForUpdated;
    }

    
    public function myNotifications()
    {
        return $this->hasMany(MyNotifications::class);
    }

    public function emailConfirmation()
    {
        return $this->hasOne(ResetEmailConfirmation::class);
    }


    public function hasConfirmedEmail()
    {
        return !$this->email_verified_at == null;
    }

}
