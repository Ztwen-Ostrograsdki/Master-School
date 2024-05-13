<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\Parentable;
use App\Models\Pupil;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParentRequestToFollowPupil extends Model
{
    use HasFactory;

    use DateFormattor;


    protected $fillable = [
        'parentable_id',
        'pupil_id',
        'relation',
        'authorized',
        'refused',
        'analysed',
    ];


    public function parentable()
    {
        return $this->belongsTo(Parentable::class);
    }

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
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
