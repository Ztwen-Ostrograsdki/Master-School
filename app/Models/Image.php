<?php

namespace App\Models;

use App\Models\User;
use App\Models\Pupil;
use App\Models\Classe;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Image extends Model
{
    use HasFactory;
    use SoftDeletes;
    const DEFAULT_PROFIL_PHOTO_PATH = "/myassets/images/product_02.jpg";

    protected $fillable = [
        'name',
    ];

    public function user()
    {
        return $this->morphedByMany(User::class, 'imageable');
    }

    public function Pupil()
    {
        return $this->morphedByMany(Pupil::class, 'imageable');
    }


    public function classe()
    {
        return $this->morphedByMany(Classe::class, 'imageable');
    }


    public function subject()
    {
        return $this->morphedByMany(Subject::class, 'imageable');
    }

}
