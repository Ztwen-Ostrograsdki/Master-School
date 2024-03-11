<?php

namespace App\Models;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Coeficient;
use App\Models\Filial;
use App\Models\Level;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ClasseGroup extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelQueryTrait;


    protected $fillable = [
        'name',
        'slug',
        'category',
        'option',
        'filial',
        'level_id',
        'filial_id',
    ];


    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }


    public function classes()
    {
        return $this->hasMany(Classe::class);
    }

    public function filiale()
    {
        return $this->belongsTo(Filial::class);
    }


    public function level()
    {
        return $this->belongsTo(Level::class);
    }


    public function coeficients()
    {
        return $this->hasMany(Coeficient::class);
    }


    public function subjects()
    {
        return $this->morphedByMany(Subject::class, 'promotable');
    }


    public function getCoef($subject_id, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        return $this->coeficients()->where('subject_id', $subject_id)->where('coeficients.school_year_id', $school_year_model->id)->first();
    }

    public function getSlug()
    {
        return str_replace(' ', '-', $this->name);
    }



    public function hasThisSubject($subject_id)
    {
        $subjects = $this->subjects()->pluck('subjects.id')->toArray();

        return in_array($subject_id, $subjects);
    }


    public function getPupils($school_year = null, $search = null, $sexe = null, $onlyIds = false)
    {
        return $this->getClasseGroupCurrentPupils($school_year, $search, $sexe, $onlyIds);
    }


    public function getClasseGroupCurrentPupils($school_year = null, $search = null, $sexe = null, $onlyIds = false)
    {
        $pupils = [];

        $classes = $this->classes;

        if(count($classes) > 0){

            foreach($classes as $classe){
            
                $classe_pupils = $classe->getPupils($school_year, $search, $sexe, $onlyIds);

                if($classe_pupils && count($classe_pupils) > 0){

                    foreach($classe_pupils as $pupil){

                        $pupils[] = $pupil;
                    }
                }

            }

        }

        return $pupils ? $pupils : [];

    }

    public function getClassePupilsOnGender(string $gender, $school_year = null)
    {
        $pupils = [];

        $classes = $this->classes;

        if(count($classes) > 0){

            foreach($classes as $classe){
            
                $classe_pupils = $classe->getClassePupilsOnGender($gender, $school_year);

                if(count($classe_pupils) > 0){

                    foreach($classe_pupils as $pupil){

                        $pupils[] = $pupil;
                    }
                }

            }

        }

        return $pupils;


    }


    public function marks($school_year, $semestre = null)
    {
        $marks = [];

        $school_year_model = $this->getSchoolYear($school_year);

        $classes = $this->classes;

        if($classes){

            foreach($classes as $classe){

                if($semestre){

                    $school = School::first();

                    $semestres = [1, 2];

                    if($school){

                        if($school->trimestre){

                            $semestre_type = 'trimestre';

                            $semestres = [1, 2, 3];
                        }
                        else{

                            $semestre_type = 'semestre';

                            $semestres = [1, 2];
                        }
                    }

                    $classe_marks = $classe->marks()->where('marks.school_year_id', $school_year_model->id)->where($semestre_type, $semestre)->get();

                }
                else{

                    $classe_marks = $classe->marks()->where('marks.school_year_id', $school_year_model->id)->get();
                }

               if(count($classe_marks) > 0){

                    foreach($classe_marks as $mark){

                        $marks[] = $mark;

                    }

               }

            }

        }

        return $marks;
    }




}
