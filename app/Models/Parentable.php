<?php

namespace App\Models;

use App\Models\ParentRequestToFollowPupil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Parentable extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'status',
        'job',
        'residence',
        'authorized',
        'name',
        'contacts',
        'blocked',
    ];

     /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $hidden = [
        'key',
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

    public function parentRequests()
    {
        return $this->hasMany(ParentRequestToFollowPupil::class);
    }

    public function notConfirmedsParentRequests()
    {
        return $this->parentRequests()->where('authorized', 0)->get();
    }


    public function requestToFollowThisPupil($pupil_id, $relation, $authorized = false)
    {

        return ParentRequestToFollowPupil::create([
                'parentable_id' => $this->id,
                'pupil_id' => $pupil_id,
                'relation' => $relation,
                'authorized' => $authorized

            ]);

    }


    public function isMySon($pupil_id)
    {
        $join = $this->pupils()->where('parent_pupils.pupil_id', $pupil_id)->first();

        return $join ? ($join->locked ? false : true) : false;
    }


    public function parent_relation($pupil_id)
    {
        $join = $this->pupils()->where('parent_pupils.pupil_id', $pupil_id)->first();

        return $join ? $join->relation : 'Inconnue';
    }


    
}
