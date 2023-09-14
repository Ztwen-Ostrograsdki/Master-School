<?php

namespace App\Models;

use App\Helpers\DateFormattor;
use App\Helpers\ModelTraits\TeachersTrait;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AE;
use App\Models\Classe;
use App\Models\ClassesSecurity;
use App\Models\Level;
use App\Models\PrincipalTeacher;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\TeacherAbsences;
use App\Models\TeacherCursus;
use App\Models\TeacherLates;
use App\Models\TimePlan;
use App\Models\TransferFile;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Carbon;

class Teacher extends Model
{
    use HasFactory;
    use SoftDeletes;
    use ModelQueryTrait;
    use TeachersTrait;
    use DateFormattor;

    protected $fillable = [
        'name',
        'surname',
        'contacts',
        'residence',
        'level_id',
        'user_id',
        'birth_day',
        'nationality',
        'authorized',
        'teaching',
        'last_teaching_date',
        'marital_status',
    ];


    public $imagesFolder = 'teachersPhotos';

    public function epreuves()
    {
        return $this->hasMany(TransferFile::class);
    }

    public function school_years()
    {
        return $this->morphToMany(SchoolYear::class, 'schoolable');
    }

    public function timePlans()
    {
        return $this->hasMany(TimePlan::class);
    }
    
    public function cursus()
    {
        return $this->hasMany(TeacherCursus::class);
    }

    public function teacherCursus()
    {
        return $this->hasMany(TeacherCursus::class);
    }

    public function aes()
    {
        return $this->hasMany(AE::class);
    }

    public function principals()
    {
        return $this->hasMany(PrincipalTeacher::class);
    }


    public function ae($school_year)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $has = $this->aes()->where('a_e_s.school_year_id', $school_year_model->id)->first();

        return $has ? $has : null;

    }


    public function principal($school_year)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $has = $this->principals()->where('principal_teachers.school_year_id', $school_year_model->id)->first();

        return $has ? $has : null;

    }

    public function user()
    {
        return $this->belongsTo(User::class);
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

   

    public function getTeachersCurrentClasses($withDuration = false, $school_year = null, $classe_id = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($classe_id){

            $classe = null;

            $cursus = $school_year_model->teacherCursus()->where('teacher_id', $this->id)->where('classe_id', $classe_id)->whereNull('end')->first();

            if($cursus){

                $classe = $cursus->classe;

            }

            return $classe;

        }

        $current_classes = [];
        
        if($this->hasClasses($school_year)){

            if(!$withDuration){

                $classes_id = $school_year_model->teacherCursus()->where('teacher_id', $this->id)->whereNull('end')->pluck('classe_id')->toArray();

                foreach($classes_id as $classe_id){

                    $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
                    
                    $current_classes[$classe_id] = $classe;
                }
            }
            else{
                $cursuses = $school_year_model->teacherCursus()->where('teacher_id', $this->id)->whereNull('end')->get();

                foreach($cursuses as $cursus){
                    
                    $classe = $school_year_model->classes()->where('classes.id', $cursus->classe_id)->first();

                    $current_classes[$cursus->classe_id] = ['classe' => $classe, 'cursus' => $cursus, 'asWorkedDuration' => $cursus->canMarksAsWorkedForTeacher()];
                }

            }


        }
        return $current_classes;

    }


    public function hasClasses($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $cursuses = $school_year_model->teacherCursus()->where('teacher_id', $this->id)->whereNull('end')->count();

        return $cursuses > 0;
    }

    public function subjects()
	{
		return $this->morphToMany(Subject::class, 'subjectable');
	}


    public function speciality()
    {
        $has = $this->subjects;
        if($has){
            $subject = $this->subjects()->first();
            if($subject){
                return $subject;
            }
        }

        return null;

    }

    public function level()
    {
        return $this->belongsTo(Level::class);
    }

    public function lates()
    {
        return $this->hasMany(TeacherLates::class);
    }
    
    public function absences()
    {
        return $this->hasMany(TeacherAbsences::class);
    }


    public function securities()
    {
        return $this->hasMany(ClassesSecurity::class);
    }


    public function hasSecurities($school_year = null, $secure_column = null, $classe_id = null)
    {
        $school_year_model = $this->getSchoolYear();

        if($secure_column){

            if($classe_id){

                return $this->securities()->where('school_year_id', $school_year_model->id)->where($secure_column, true)->where('classes_securities.classe_id', $classe_id)->count() > 0;

            }

            return $this->securities()->where('school_year_id', $school_year_model->id)->where($secure_column, true)->count() > 0;

        }
        else{

            if($classe_id){

                return $this->securities()->where('school_year_id', $school_year_model->id)->where('classes_securities.classe_id', $classe_id);

            }

            return $this->securities()->where('school_year_id', $school_year_model->id)->count() > 0;

        }

    }

    public function teacherCanAccess($classe_id, $secure_column = 'closed', $school_year = null)
    {

        if($this->hasSecurities()){

            $school_year_model = $this->getSchoolYear();

            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where($secure_column, true)->count();

            $req2 = $school_year_model->securities()->where('classe_id', $classe_id)->where($secure_column, true)->count();

            return $req1 == 0 && $req2 == 0;
        }
        return true;
    }

    public function teacherCanAccessMarks($classe_id, $school_year = null)
    {
        if($this->hasSecurities()){
            $school_year_model = $this->getSchoolYear();
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where('locked_marks', true)->count();
            $req2 = $school_year_model->securities()->where('school_year_id', $school_year_model->id)->where('classe_id', $classe_id)->where('locked_marks', true)->count();
            $req3 = $school_year_model->securities()->where('classe_id', $classe_id)->where('locked', true)->count();
            $req4 = $school_year_model->securities()->where('classe_id', $classe_id)->where('closed', true)->count();

            return $req1 == 0 && $req2 == 0 && $req3 == 0 && $req4 == 0;
        }
        return true;
    }







}
