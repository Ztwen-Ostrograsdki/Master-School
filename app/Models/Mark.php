<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Mark extends Model
{
    use HasFactory;
    use SoftDeletes;

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
        'editing_value', 
        'edited', 
        'level_id', 
        'creator', 
        'editor', 
        'authorized',
        'forget',
        'blocked',
        'forced_mark',
        'mark_index',
    ];

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
        if($this->school_years && count($this->school_years) > 0){
            return $this->school_years()->first();
        }
        return null;
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

}
