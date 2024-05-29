<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Filial;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Epreuve extends Model
{
    use HasFactory, DateFormattor, ModelQueryTrait;

    protected $table_name = 'epreuves';

    public $imagesFolder = 'epreuvesFolder';

    protected $fillable = [
        'name', 'path', 'extension', 'classe_id', 'semestre', 'school_year_id', 'subject_id', 'description', 'target', 'duration', 'classe_group_id', 'filial_id', 'teacher_id', 'author', 'session', 'exam_name', 'authorized', 'level_id', 'locked', 'downloaded', 'downloaded_counter', 'trimestre', 'done', 'date'
    ];

    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();
        if($created_at){
            return $this->dateAgoToString;
        }
        return $this->dateAgoToStringForUpdated;
    }


    public function classe_group()
    {
        return $this->belongsTo(ClasseGroup::class);
    }


    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function filial()
    {
        return $this->belongsTo(Filial::class);

    }

    public function getFullPath()
    {
        $full_path = $this->path . '/' . $this->name . '' . $this->extension;

        return $full_path;
    }
}
