<?php

namespace App\Models;

use App\Events\UserTryingToUpdatePupilMarkEvent;
use App\Helpers\DateFormattor;
use App\Helpers\ModelTraits\MarkTraits;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Prunable;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Mark extends Model
{
    use HasFactory, SoftDeletes, MarkTraits, DateFormattor;

    const DELAYED = 72; // For three days among

    public function getDelay()
    {
        return self::DELAYED;
    }

    protected $fillable = [
        'value', 
        'pupil_id', 
        'subject_id', 
        'school_year_id', 
        'classe_id', 
        'user_id',
        // 'trimestre', 
        'semestre', 
        'type', 
        'exam_name', 
        'session', 
        'month', 
        'edited', 
        'level_id', 
        'creator', 
        'editor', 
        'authorized',
        'updating',
        'forget',
        'blocked',
        'forced_mark',
        'mark_index',
    ];


    protected $casts = ['editing_value'];


    // public function prunable(): Builder
    // {
    //     return static::where('deleted_at', '<=', now()->subMonth());
    // }


    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }

    public function pupil()
    {
        return $this->belongsTo(Pupil::class);
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function level()
    {
        return $this->belongsTo(Level::class);
    }


    public function getCreator()
    {
        return $this->creator ? User::find($this->creator) : null;
    }

    public function getEditor()
    {
        return $this->editor ? User::find($this->editor) : null;
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
