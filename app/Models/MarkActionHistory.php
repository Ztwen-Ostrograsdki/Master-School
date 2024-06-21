<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MarkActionHistory extends Model
{
    use HasFactory, DateFormattor;

    protected $fillable = ['classe_id', 'subject_id', 'school_year_id', 'semestre', 'user_id', 'value', 'description', 'action', 'mark_id', 'mark_index', 'type', 'session', 'exam_name', 'trimestre',
    ];

    private $actions = ['DELETE', 'CREATE', 'UPDATE'];


    public function getActions()
    {
        return $this->actions;
    }


    public function actioner()
    {
        return $this->belongsTo(User::class);
    }


    // public function prunable(): Builder
    // {
    //     return static::where('deleted_at', '<=', now()->subYear());

    // }

    public function user()
    {
        return $this->belongsTo(User::class);
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

    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();

        if($created_at){

            return $this->dateAgoToString;
        }
        
        return $this->dateAgoToStringForUpdated;
    }


    public function getMark()
    {
        $mark = Mark::withTrashed()->whereId($this->classe_id)->first();

        if($mark){

            return $mark;
        }

        return null;
    }


    public function mark()
    {
        return $this->getMark();
    }
}
