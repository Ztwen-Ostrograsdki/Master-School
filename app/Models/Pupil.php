<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelTraits\PupilTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Helpers\ZtwenManagers\GaleryManager;
use App\Models\Averages;
use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\ClassesSecurity;
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
        'classe_id',
        'contacts',
        'abandonned',
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

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array
     */
    protected $secure = [
        'educmaster',
        'matricule',
        'ltpk_matricule'
    ];

    public function updatePupilLTPKMatricule($value)
    {
        return $this->forceFill(['ltpk_matricule' => $value,])->save();
        
    }


    public function updatePupilEducmaster($value)
    {
        return $this->forceFill(['educmaster' => $value,])->save();

        
    }

    public function updatePupilMatricule($value)
    {
        return $this->forceFill(['matricule' => $value,])->save();
    }

    public $imagesFolder = 'pupilsPhotos';

    public function averages()
    {
        return $this->hasMany(Averages::class);
    }


    public function average($classe_id, $semestre = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        return Averages::where('averages.pupil_id', $this->id)
                        ->where('averages.school_year_id', $school_year_model->id)
                        ->where('averages.classe_id', $classe_id)
                        ->where('averages.semestre', $semestre)
                        ->first();
    }


    public function annual_average($classe_id, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $averages = Averages::where('averages.pupil_id', $this->id)
                        ->where('averages.school_year_id', $school_year_model->id)
                        ->where('averages.classe_id', $classe_id)
                        ->whereNull('averages.semestre')
                        ->first();
        return $averages;
    }

    public function securities()
    {
        return $this->hasMany(ClassesSecurity::class);
    }

    public function classesSchoolYears()
    {
        return $this->hasMany(ClassePupilSchoolYear::class);
    }


    public function pupilClassesHistoriesBySchoolYears()
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


    public function getLastRelatedMark(int $classe_id, $subject_id, $semestre, $school_year, $signed = false)
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

    public function getLastRelatedMarkValue(int $classe_id, $subject_id, $semestre, $school_year, $signed = false)
    {
        $mark = $this->getLastRelatedMark($classe_id, $subject_id, $semestre, $school_year);
        if($mark){
            return $signed ? ($mark->type == 'bonus' ? ' + '. $mark->value : ' - ' . $mark->value) : $mark->value;
        }
        return null;

    }

    public function getLastRelatedMarkDate(int $classe_id, $subject_id, $semestre, $school_year)
    {
        $mark = $this->getLastRelatedMark($classe_id, $subject_id, $semestre, $school_year);
        if($mark){
            return $mark->__getDateAsString($mark->date);
        }
        return null;

    } 

    public function getLastRelatedMarkHoraire(int $classe_id, $subject_id, $semestre, $school_year)
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

    public function getFormatedName()
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


    public function getCurrentClasse($school_year = null, $ofTheLastYear = false)
    {
        $classe = null;
        
        if($ofTheLastYear){

            $school_year_model = SchoolYear::orderBy('school_year', 'desc')->first();

        }
        else{

            $school_year_model = $this->getSchoolYear($school_year);
        }

        $relation = $this->classesSchoolYears()->where('school_year_id', $school_year_model->id)->orderBy('classe_pupil_school_years.id', 'desc')->first();

        if($relation){

            $classe = $relation->classe;
        }

        return $classe ? ( $classe->id == $this->classe_id ? $classe : null ) : null;


    }


    public function getMarksCounter($semestre_id = 1, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear();

        $counter = $school_year_model->marks()->where('pupil_id', $this->id)->where('semestre', $semestre_id)->count();
        
        return $counter;

    }

    public function getSucceedsMarksCounter($semestre_id = 1, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear();

        $counter = $school_year_model->marks()->where('pupil_id', $this->id)->where('semestre', $semestre_id)->where('value', '>=', 10)->count();
        return $counter;

    } 


    public function getBestSubject($semestre_id = 1, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear();

        $subjects = $this->classe->subjects;
        $subjects_tab = [];
        $targets = [];

        $best_mark = null;
        $best_mark = null;

        foreach($subjects as $subject){
            $average = 0;
            $marks = $school_year_model->marks()->where('pupil_id', $this->id)->where('semestre',$semestre_id)->where('type', '<>', 'participation')->where('subject_id', $subject->id)->pluck('value')->toArray();
            if(count($marks) > 0){
                $average = array_sum($marks) / count($marks);
            }
            else{
                $average = -1;
            }
            if($average !== -1){
                $subjects_tab[$subject->name] = $average;
            }
        }

        if($subjects_tab !== []){
            $best_mark = max($subjects_tab);
            $best_mark_suject_name = array_search($best_mark, $subjects_tab);

            $weak_mark = max($subjects_tab);
            $weak_mark_subject_name = array_search($weak_mark, $subjects_tab);

            $targets = [$best_mark_suject_name => $best_mark, $weak_mark_subject_name => $weak_mark];

            if($targets !== []){
                if(count($targets) <= 1){
                    $targets['Aucune'] = null;
                }
            }
            else{
                $targets = ['Aucune' => null, 'Aucune' => null];
            }
        }
        else{
            $targets = ['Aucune' => null, 'Aucune' => null];
        }
        return $targets;
    }



    public function isDoingThisClasseInThisSchoolYear($classe, $school_year)
    {



    }


    public function getClasseAndYear($classe_id = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $school_year_classe_pupil = ClassePupilSchoolYear::where('classe_pupil_school_years.classe_id', $classe_id)->where('classe_pupil_school_years.pupil_id', $this->id)->where('classe_pupil_school_years.school_year_id', $school_year_model->id)->first();

        if($school_year_classe_pupil){

            return $school_year_classe_pupil;

        }

        return null;

    }






}
