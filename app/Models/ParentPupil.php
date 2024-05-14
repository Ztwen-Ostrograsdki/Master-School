<?php

namespace App\Models;

use App\Models\Pupil;
use App\Models\Parentable;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;


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




// 3201000252315

// 97 98 11 14 comptable
}
