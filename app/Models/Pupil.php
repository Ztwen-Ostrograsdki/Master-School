<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelTraits\PupilTraits;
use App\Helpers\ZtwenManagers\GaleryManager;
use App\Models\Classe;
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

    protected $fillable = [
        'firstName',
        'lastName',
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
        'last_school_from'
    ];

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
        $classes = [];
        $school_years = SchoolYear::all();

        $pupil_school_years = $this->school_years;
        if(count($pupil_school_years) > 0){
            $pupil_classes = $this->classes()->pluck('classes.id')->toArray();
            foreach ($pupil_school_years as $school_year) {
                $classe = $school_year->classes()->whereIn('classes.id', $pupil_classes)->first();
                if($classe){
                    $classes[] = [
                        'classe' => $classe,
                        'school_year' => $school_year
                    ];

                }
            }
            return $classes;

        }
        else{

            return null;

        }



    }






}
