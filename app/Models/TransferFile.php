<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\Transfer;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TransferFile extends Model
{
    use HasFactory;

    use DateFormattor;

    use ModelQueryTrait;

    protected $table_name = 'transfer_files';

    public $imagesFolder = 'epreuvesFolder';

    protected $fillable = [
        'name', 'classe_id', 'semestre', 'school_year_id', 'subject_id', 'description', 'target', 'duration', 'classe_group_id', 'teacher_id', 'user_id', 'session', 'exam_name', 'blocked', 'authorized', 'level_id', 'transfer_id', 'disk', 'path', 'size'
    ];


    protected $casts = ['disk' => 'string', 'path' => 'string', 'size' => 'integer'];

    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();
        if($created_at){
            return $this->dateAgoToString;
        }
        return $this->dateAgoToStringForUpdated;
    }




    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
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


    public function transfer()
    {
        return $this->belongsTo(Transfer::class);

    }
}
