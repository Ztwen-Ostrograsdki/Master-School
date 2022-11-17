<?php

namespace App\Models;

use App\Helpers\ModelTraits\ClasseTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AverageModality;
use App\Models\ClasseGroup;
use App\Models\ClassePupilSchoolYear;
use App\Models\Coeficient;
use App\Models\Image;
use App\Models\Level;
use App\Models\Pupil;
use App\Models\PupilCursus;
use App\Models\RelatedMark;
use App\Models\Responsible;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Classe extends Model
{
    use ModelQueryTrait;
    use HasFactory;
    use SoftDeletes;
    use ClasseTraits;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level_id',
        'classe_group_id',
        'closed',
        'locked',
        'teacher_id'
    ];




    public function classePupilSchoolYear()
    {
        return $this->hasMany(ClassePupilSchoolYear::class);
    }

    public function related_marks()
    {
        return $this->hasMany(RelatedMark::class);
    }

    public function averageModalities()
    {
        return $this->hasMany(AverageModality::class);
    }


    public function getPupils($school_year)
    {
        $pupils = [];
        if(is_numeric($school_year)){
            $school_year_model = SchoolYear::find($school_year);
        }
        else{
            $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        }
        $c_p_s_ys = $this->classePupilSchoolYear()->where('school_year_id', $school_year_model->id);
        if($c_p_s_ys->get() && count($c_p_s_ys->get()) > 0){
            $pupils_ids = $c_p_s_ys->pluck('pupil_id')->toArray();
            $pupils = Pupil::whereIn('id', $pupils_ids)
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc')
                             ->get();
        }
        return $pupils;
    }


    public function getClassePupilsOnGender(string $gender, $school_year)
    {
        $pupils = [];
        if($school_year){
            if ($gender) {
                $pupils_all = $this->getPupils($school_year);
                foreach($pupils_all as $pupil){
                    if($pupil->sexe == $gender){
                        $pupils[] = $pupil;
                    }
                }
            }

        }


        return $pupils;


    }


    public function classe_group()
    {
        return $this->belongsTo(ClasseGroup::class);
    }


    public function promotion()
    {
        return $this->belongsTo(ClasseGroup::class);
    }



    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }

    public function classePupils()
	{
		return $this->morphedByMany(Pupil::class, 'classable');
	}
    

    public function pp()
    {
        return $this->hasOne(Teacher::class);
    }

    public function cursus()
    {
        return $this->hasMany(PupilCursus::class);
    }

    public function pupils()
    {
        return $this->hasMany(Pupil::class);
    }
    public function responsibles()
    {
        return $this->hasMany(Responsible::class);
    }

    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }
    

    public function subjects()
	{
		return $this->morphedByMany(Subject::class, 'classable');
	}

    /**
     * To get all teachers of this classe
     * @return [type] [description]
     */
    public function teachers()
    {
    	return $this->morphedByMany(Teacher::class, 'classable');
    }

    /**
     * To get the principal
     * @return [type] [description]
     */
    public function teacher()
    {
        return $this->belongsTo(Teacher::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function coeficients()
    {
        return $this->hasMany(Coeficient::class);
    }

    public function alreadyJoinedToThisYear(int $school_year = null)
    {
        $classe_school_years = $this->school_years;
        if($school_year == null){
            $school_year = $this->getSchoolYear()->id;
        }
        if($classe_school_years && count($classe_school_years) > 0){
            $school_years_id_array = $classe_school_years->pluck('id')->toArray();

            return in_array($school_year, $school_years_id_array);
        }

        return false;

    }

    /**
     * Pour avoir le format numerique des classes
     * @return array [description]
     */
    public function getNumericName()
    {
        $name = $this->name;
        if ($this->level->name === "secondary") {
            $card = [];
            $card['id'] = $this->id;
            $card['name'] = $this->name;
            $card['idc'] = "";

            if(preg_match_all('/ /', $name)){
                $card['idc'] = explode(' ', $name)[1];
            }

            if (preg_match_all('/Sixi/', $name)) { 
                $card['sup'] = "ème";
                $card['root'] = "6";
            }
            elseif (preg_match_all('/Cinqui/', $name)) {
                $card['sup'] = "ème";
                $card['root'] = "5";
            }
            elseif (preg_match_all('/Quatriem/', $name)) {
                $card['sup'] = "ème";
                $card['root'] = "4";
            }
            elseif (preg_match_all('/Troisie/', $name)) {
                $card['sup'] = "ère";
                $card['root'] = "3";
            }
            elseif (preg_match_all('/Seconde/', $name)) {
                $card['sup'] = "nde";
                $card['root'] = "2";
            }
            elseif (preg_match_all('/Premi/', $name)) {
                $card['sup'] = "ère";
                $card['root'] = "1";
            }
            elseif (preg_match_all('/Terminale/', $name)) {
                $card['sup'] = "le";
                $card['root'] = "T";
                
            }
            else{
                return ['root' => $name, 'sup' => "", 'idc' => "", 'id' => $this->id, 'root' => $name];
            }

            return $card;
        }
        else{
            return ['root' => $name, 'sup' => "", 'idc' => "", 'id' => $this->id, 'root' => $name];
        }

    }


}
