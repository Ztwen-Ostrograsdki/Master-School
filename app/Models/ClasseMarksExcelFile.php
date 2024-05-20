<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ClasseMarksExcelFile extends Model
{
    use HasFactory, DateFormattor;

    protected $fillable = ['name', 'path', 'classe_id', 'subject_id', 'school_year_id', 'semestre', 'user_id', 'downloaded', 'downloaded_counter', 'secure'];

    private $table_name = "classe_marks_excel_files";

    

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }


    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }

    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }



}
