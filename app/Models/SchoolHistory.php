<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SchoolHistory extends Model
{
    use HasFactory;

    use ModelQueryTrait;

    use DateFormattor;

    private $visibilities = ['public', 'private', 'admins', 'master', 'parents', 'teachers', 'users'];

    protected $fillable = ['school_year_id', 'table', 'model_id', 'content', 'description', 'seen', 'visibility'];


    public function school_year()
    {
        return $this->belongsTo(SchoolYear::class);
    }


    public function of($model_id)
    {
        $school_year_model = $this->getSchoolYear();

        $data = $school_year_model->histories()->where('model_id', $model_id)->get();
    }
}
