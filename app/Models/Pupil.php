<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelTraits\PupilTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Helpers\ZtwenManagers\GaleryManager;
use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\Image;
use App\Models\Level;
use App\Models\Mark;
use App\Models\PupilAbsences;
use App\Models\PupilCursus;
use App\Models\PupilLates;
use App\Models\RelatedMark;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Pupil extends Model
{
    use HasFactory;
    use SoftDeletes;
    use GaleryManager;
    use DateFormattor;
    use PupilTraits;
    use ModelQueryTrait;

    protected $fillable = [
        'firstName',
        'lastName',
        'matricule',
        'classe_id',
        'contacts',
        'sexe',
        'birth_day',
        'nationality',
        'birth_city',
        'failed',
        'blocked',
        'authorized',
        'level_id',
        'residence',
        'last_school_from',
        'bonus_counter',
        'minus_counter',
        'last_related_mark',
    ];

    public function classesSchoolYears()
    {
        return $this->hasMany(ClassePupilSchoolYear::class);
    }

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }
    public function classes()
    {
        return $this->morphToMany(Classe::class, 'classable');
    }

    public function getDateAgoFormated($created_at = false)
    {
        $this->__setDateAgo();
        if($created_at){
            return $this->dateAgoToString;
        }
        return $this->dateAgoToStringForUpdated;
    }



    public function images()
    {
        return $this->morphToMany(Image::class, 'imageable');
    }


    public function classe()
    {
        return $this->belongsTo(Classe::class);
    }


    public function cursus()
    {
        return $this->hasMany(PupilCursus::class);
    }


    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function related_marks()
    {
        return $this->hasMany(RelatedMark::class);
    }

    public function getRelatedMarksCounter($classe_id, $subject_id, $semestre, $school_year, $type = 'bonus', $signed = false)
    {
        $this->bonus_counter = 0; 
        $this->minus_counter = 0; 
        if($classe_id && $subject_id && $semestre && $school_year){
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::where('id', $school_year)->first();
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }
            if($school_year_model){
                $school_year_model->related_marks()->where('pupil_id', $this->id)->where('classe_id', $classe_id)->where('subject_id', $subject_id)->where('semestre', $semestre)->where('type', $type)->each(function($mark) use ($type){
                    if($type == 'bonus'){
                        $this->bonus_counter = $this->bonus_counter + $mark->value;
                    }
                    elseif ($type == 'minus') {
                        $this->minus_counter = $this->minus_counter + $mark->value;
                    }
                });
            }
        }
        if($type == 'bonus'){
            return( $signed && $this->bonus_counter > 0) ?  ' + '. $this->bonus_counter : $this->bonus_counter ;
        }
        elseif ($type == 'minus') {
            return ($signed && $this->minus_counter > 0) ? ' - '. $this->minus_counter : $this->minus_counter ;
        }
    }


    public function getLastRelatedMark(int $classe_id, $subject_id, int $semestre, $school_year, $signed = false)
    {
        if($classe_id && $subject_id && $semestre && $school_year){
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::where('id', $school_year)->first();
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }
            if($school_year_model){
                $this->last_related_mark = $school_year_model->related_marks()->where('pupil_id', $this->id)->where('classe_id', $classe_id)->where('subject_id', $subject_id)->where('semestre', $semestre)->orderBy('related_marks.id', 'desc')->first();
            }
        }
        return $this->last_related_mark;

    }

    public function getLastRelatedMarkValue(int $classe_id, $subject_id, int $semestre, $school_year, $signed = false)
    {
        $mark = $this->getLastRelatedMark($classe_id, $subject_id, $semestre, $school_year);
        if($mark){
            return $signed ? ($mark->type == 'bonus' ? ' + '. $mark->value : ' - ' . $mark->value) : $mark->value;
        }
        return null;

    }

    public function getLastRelatedMarkDate(int $classe_id, $subject_id, int $semestre, $school_year)
    {
        $mark = $this->getLastRelatedMark($classe_id, $subject_id, $semestre, $school_year);
        if($mark){
            return $mark->__getDateAsString($mark->date);
        }
        return null;

    } 

    public function getLastRelatedMarkHoraire(int $classe_id, $subject_id, int $semestre, $school_year)
    {
        $mark = $this->getLastRelatedMark($classe_id, $subject_id, $semestre, $school_year);
        if($mark){
            return $mark->horaire;
        }
        return null;

    }







    public function parents()
    {
        return $this->hasMany(ParentPupil::class);
    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function lates()
    {
        return $this->hasMany(PupilLates::class);
    }
    
    public function absences()
    {
        return $this->hasMany(PupilAbsences::class);
    }


    public function getName()
    {
        return $this->firstName . ' ' . $this->lastName;
    }


    public function getSexe()
    {
        $sexe = $this->sexe;

        if($sexe == 'male' || $sexe == 'masculin'){
            return 'M';
        }
        elseif($sexe == 'female' || $sexe == 'feminin'){
            return 'F';
        }
        else{
            return 'Inconnu';

        }



    }




    public function getArchives()
    {
        $all_classes = [];
        $school_years = SchoolYear::all();

        $pupil_school_years = $this->school_years;
        if(count($pupil_school_years) > 0){
            $classesSchoolYears = $this->classesSchoolYears;
            if($classesSchoolYears){
                foreach ($classesSchoolYears as $school_year_classe) {
                    $classe = $school_year_classe->classe;
                    $school_year = $school_year_classe->school_year;
                    
                    if($classe && $school_year){
                        $all_classes[] = [
                            'classe' => $classe,
                            'school_year' => $school_year
                        ];
                    }
                }
            }
           
            return $all_classes;
        }
        else{
            return null;
        }
    }


    public function getCurrentClasse($school_year = null)
    {
        $classe = null;
        if($school_year){
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::where('id', $school_year)->first();
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }
        }
        else{
            $school_year_model = $this->getSchoolYear();

        }

        $relation = $this->classesSchoolYears()->where('school_year_id', $school_year_model->id)->first();
        if($relation){
            $classe = $relation->classe;
        }

        return $classe;


    }



    public function isDoingThisClasseInThisSchoolYear($classe, $school_year)
    {



    }






}
