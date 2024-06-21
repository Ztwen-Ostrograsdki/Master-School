<?php

namespace App\Models;

use App\Models\ParentRequestToFollowPupil;
use App\Models\Parentable;
use App\Models\Pupil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;


/**
 * To join a parentable to a pupil who should be is her child
 */
class ParentPupil extends Model
{
    use HasFactory;
    
    protected $fillable = [
        'parentable_id',
        'pupil_id',
        'relation',
        'locked',
    ];


    public function parentable()
    {
        return $this->belongsTo(Parentable::class);
    }

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function following_request()
    {
        return ParentRequestToFollowPupil::where(['pupil_id' => $this->pupil_id, 'parentable_id' => $this->parentable_id])->first();
    }




// 3201000252315

// 97 98 11 14 comptable
}
