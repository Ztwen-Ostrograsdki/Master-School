<?php

namespace App\Models;

use App\Models\Parentable;
use App\Models\Pupil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentRequestToFollowPupil extends Model
{
    use HasFactory;


    protected $fillable = [
        'parentable_id',
        'pupil_id',
        'relation',
        'authorized',
    ];


    public function parentable()
    {
        return $this->belongsTo(Parentable::class);
    }

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }
}
