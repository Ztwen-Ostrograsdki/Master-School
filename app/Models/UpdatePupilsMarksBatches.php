<?php

namespace App\Models;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UpdatePupilsMarksBatches extends Model
{
    use HasFactory;

    protected $fillable = [
        'classe_id', 'subject_id', 'school_year_id', 'semestre', 'user_id', 'finished', 'total_marks', 'method_type', 'batch_id', 'classes', 'subjects', 'types', 'all_classes', 'all_subjects', 'all_semestres', 'all_types', 'description',

    ];

    private $method_type = ['insertion', 'deletion'];

    private $table_name = 'update_pupils_marks_batches';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


    public function school_year()
    {

        return $this->belongsTo(SchoolYear::class);
    } 
}
