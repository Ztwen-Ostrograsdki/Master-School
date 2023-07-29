<?php

namespace App\Models;

use App\Helpers\ModelTraits\ClasseTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AverageModality;
use App\Models\Averages;
use App\Models\ClasseGroup;
use App\Models\ClassePupilSchoolYear;
use App\Models\ClassesSecurity;
use App\Models\Coeficient;
use App\Models\Image;
use App\Models\Level;
use App\Models\PrincipalTeacher;
use App\Models\Pupil;
use App\Models\PupilCursus;
use App\Models\QotHour;
use App\Models\RelatedMark;
use App\Models\Responsible;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use App\Models\TeacherCursus;
use App\Models\TimePlan;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\DB;

class Classe extends Model
{
    use ModelQueryTrait;
    use HasFactory;
    use SoftDeletes;
    use ClasseTraits;

    public $imagesFolder = 'classesPhotos';

    protected $fillable = [
        'name',
        'slug',
        'description',
        'level_id',
        'position',
        'classe_group_id',
        'closed',
        'locked',
        'teacher_id'
    ];

    public function averages()
    {
        return $this->hasMany(Averages::class);
    }

    public function averages_of($semestre = null, $school_year = null)
    {

        $school_year_model = $this->getSchoolYear($school_year);

        return $this->averages()->where('averages.semestre', $semestre)->where('averages.school_year_id', $school_year_model->id)->get();
        
    }


    public function qotHours()
    {
        return $this->hasMany(QotHour::class);
    }

    public function getSlug()
    {
        return $this->slug;
    }


    public function teacherCursus()
    {
        return $this->hasMany(TeacherCursus::class);
    }

    /**
     * Get and return the polyvalente classe
     */
    public function polyvalenteClasse($level = 'secondary')
    {
        $target = '%' . 'polyvalente' . '%';

        $level_id = $this->level->id;

        return Classe::where('name', 'like', $target)->where('level_id', $level_id)->first();
    }

    /**
     * Asset is a current classe model is an polyvalente classe
     */
    public function isNotPolyvalente()
    {
        $name = strtoupper($this->name);

        $match = preg_match_all('/POLYVALENTE/', $name, $matches);

        return $match ? false : true;
    }


    public function timePlans()
    {
        return $this->hasMany(TimePlan::class);
    }


    public function classePupilSchoolYear()
    {
        return $this->hasMany(ClassePupilSchoolYear::class);
    }

    public function related_marks()
    {
        return $this->hasMany(RelatedMark::class);
    }


    public function marks()
    {
        return $this->hasMany(Mark::class);
    }

    public function averageModalities()
    {
        return $this->hasMany(AverageModality::class);
    }


    public function getClasseCurrentPupils($school_year = null)
    {
        $pupils = [];

        $school_year_model = $this->getSchoolYear($school_year);

        $cursuses = $this->classePupilSchoolYear()->where('classe_pupil_school_years.school_year_id', $school_year_model->id)->pluck('pupil_id')->toArray();

        if($cursuses){

            $pupils = Pupil::whereIn('id', $cursuses)->orderBy('pupils.firstName', 'asc')->orderBy('pupils.lastName', 'asc')->get();

        }

        return $pupils;

    }


    public function getPupils($school_year = null, $search = null, $sexe = null, $onlyIds = false, $orderByData = [], $orderBy = 'desc')
    {
        $pupils = [];

        $school_year_model = $this->getSchoolYear($school_year);

        $cursuses = $this->classePupilSchoolYear()->where('classe_pupil_school_years.school_year_id', $school_year_model->id);
        

        if($cursuses->get() && count($cursuses->get()) > 0){

            $pupils_ids = $cursuses->pluck('pupil_id')->toArray();


            if($search && strlen($search) > 2){

                if($sexe){

                    $pupils = Pupil::whereIn('id', $pupils_ids)
                             ->where('sexe', $sexe)
                             ->where('firstName', 'like', '%' . $search . '%')
                             ->orWhere('lastName', 'like', '%' . $search . '%')
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc');
                }
                else{

                    $pupils = Pupil::whereIn('id', $pupils_ids)
                             ->where('firstName', 'like', '%' . $search . '%')
                             ->orWhere('lastName', 'like', '%' . $search . '%')
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc');
                }
            }
            else{

                if($sexe){

                    $pupils = Pupil::whereIn('id', $pupils_ids)
                            ->where('sexe', $sexe)
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc');

                }
                else{

                    $pupils = Pupil::whereIn('id', $pupils_ids)
                             ->orderBy('firstName', 'asc')
                             ->orderBy('lastName', 'asc');
                }
            }

            if($onlyIds){

                return $pupils->pluck('id')->toArray();

            }

            return $pupils->get();
        }

    }


    public function getClassePupilsOnGender($gender, $school_year = null)
    {
        $pupils_all = $this->getClasseCurrentPupils($school_year);

        
        if ($gender !== null) {

            $pupils = [];

            if($pupils_all){

                foreach($pupils_all as $pupil){

                    if($pupil->sexe == $gender){

                        $pupils[] = $pupil;
                    }
                }

            }

            return $pupils;
        }
        else{
            return $pupils_all;

        }

    }


    public function getEffectif($gender = null, $school_year = null)
    {
        if($gender){

            return count($this->getClassePupilsOnGender($gender, $school_year));

        }
        else{

            return count($this->getClasseCurrentPupils($school_year));
        }
    }


    public function securities()
    {
        return $this->hasMany(ClassesSecurity::class);
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
    

    public function principals()
    {
        return $this->hasMany(PrincipalTeacher::class);
    }


    public function currentPrincipal($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        return $this->hasPrincipal($school_year) ? $this->hasPrincipal($school_year)->teacher : null;

    }

    public function hasPrincipal($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $principal = $school_year_model->principals()->where('principal_teachers.classe_id', $this->id)->first();

        return $principal ? $principal : null;
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

    public function currentRespo($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        return Responsible::where('school_year_id', $school_year_model->id)->where('classe_id', $this->id)->first();
    }

    public function hasRespo($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $r = Responsible::where('school_year_id', $school_year_model->id)->where('classe_id', $this->id)
            ->whereNotNull('respo_1' . $rank)
            ->orWhere('respo_2', '<>', null)
            ->orWhere('respo_3', '<>', null)
            ->count();

        return $r > 0 ? true : false;
    }

    public function getRespo($rank, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        return Responsible::where('school_year_id', $school_year_model->id)->where('classe_id', $this->id)->whereNotNull('respo_' . $rank)->first();
    }


    public function pupil_respo1($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($this->getRespo(1)){

            return $school_year_model->pupils()->where('pupils.id', $this->getRespo(1)->respo_1)->first();
        }
        else{
            return null;
        }
    }

    public function pupil_respo2($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($this->getRespo(2)){

            return $school_year_model->pupils()->where('pupils.id', $this->getRespo(2)->respo_2)->first();
        }
        else{
            return null;
        }
    }

    public function pupil_respo($rank)
    {
        $school_year_model = $this->getSchoolYear();

        if($this->getRespo($rank)){

            return $school_year_model->pupils()->where('pupils.id', $this->getRespo($rank)->respo_1)->first();
        }
        else{

            return null;
        }
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

    public function get_coefs($subject_id = null, $school_year = null, $value = true)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($this->classe_group){

            $coef_model = $this->classe_group->coeficients()->where('coeficients.subject_id', $subject_id)->where('coeficients.school_year_id', $school_year_model->id)->first();

            return $value ? ($coef_model ? $coef_model->coef : 1) : $coef_model;
        }

        return 1;
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

                return ['sup' => "", 'idc' => "", 'id' => $this->id, 'root' => $name];
            }

            $parts = explode(' ', $this->name);

            if(count($parts) > 1){

                $idcs = explode('-', $parts[1]);

                if(count($idcs) > 1){

                    $idc = $idcs[0] . '-' . $idcs[1];

                    $card['idc'] = $idc;
                }
                else{

                    $idc = $parts[1];

                    $card['idc'] = $idc;
                }
            }

            return $card;

        }
        else{

            return ['sup' => "", 'idc' => "", 'id' => $this->id, 'root' => $name];
        }

    }


    public function getClassePosition()
    {
        $name = $this->name;

        $level = null;

        if ($this->level->name === "secondary") {

            if (preg_match_all('/Sixi/', $name)) { 

                $level = 1;
            }
            elseif (preg_match_all('/Cinqui/', $name)) {

                $level = 2;
            }
            elseif (preg_match_all('/Quatriem/', $name)) {

                $level = 3;
            }
            elseif (preg_match_all('/Troisie/', $name)) {

                $level = 4;
            }
            elseif (preg_match_all('/Seconde/', $name)) {

                $level = 5;
            }
            elseif (preg_match_all('/Premi/', $name)) {

                $level = 6;
            }
            elseif (preg_match_all('/Terminale/', $name)) {

                $level = 7;
            }
        }
        elseif($this->level == 'primary'){

            if (preg_match_all('/Maternelle 1/', $name)) {

                $level = 1;
            }
            elseif (preg_match_all('/Maternelle 2/', $name)) {

                $level = 2;
            }
            elseif (preg_match_all('/CP/', $name)) {

                $level = 3;
            }
            elseif (preg_match_all('/CE1/', $name)) {

                $level = 4;
            }
            elseif (preg_match_all('/CE2/', $name)) {

                $level = 5;
            }
            elseif (preg_match_all('/CM1/', $name)) {

                $level = 6;
            }
            elseif (preg_match_all('/CM2/', $name)) {

                $level = 7;
                
            }
        }

        return $level;

    }

    public function getClasseCurrentTeachers($withDuration = false, $subject_id = null)
    {
        $school_year_model = $this->getSchoolYear();

        $current_teachers = [];

        if($this->teachers){

            if(!$withDuration){

                $teachers_id = [];

                if($subject_id){

                    $cursus = $school_year_model->teacherCursus()
                                                     ->where('classe_id', $this->id)
                                                     ->where('teacher_cursuses.subject_id', $subject_id)
                                                     ->whereNull('end')
                                                     ->first();
                    return $cursus ? $school_year_model->teachers()->where('teachers.id', $cursus->teacher_id)->first() : null;

                }
                else{

                    $teachers_id = $school_year_model->teacherCursus()->where('classe_id', $this->id)->whereNull('end')->get();



                    foreach($teachers_id as $cursus){

                        $teacher = $school_year_model->findTeacher($cursus->teacher_id);


                        $current_teachers[$cursus->teacher_id] = $teacher;
                    }

                }

                
            }
            else{

                $cursuses = [];

                if($subject_id){

                    $cursus = $school_year_model->teacherCursus()
                                                  ->where('classe_id', $this->id)
                                                  ->where('teacher_cursuses.subject_id', $subject_id)
                                                  ->whereNull('end')
                                                  ->first();

                    return $cursus ? ['teacher' => $teacher, 'cursus' => $cursus, 'asWorkedDuration' => $cursus->canMarksAsWorkedForTeacher()] : null;
                }
                else{

                    $cursuses = $school_year_model->teacherCursus()
                                                  ->where('classe_id', $this->id)
                                                  ->whereNull('end')
                                                  ->get();

                    foreach($cursuses as $cursus){

                        $teacher = $school_year_model->teachers()->where('teachers.id', $cursus->classe_id)->first();

                        $current_teachers[$cursus->teacher_id] = ['teacher' => $teacher, 'cursus' => $cursus, 'asWorkedDuration' => $cursus->canMarksAsWorkedForTeacher()];
                    }

                }

                

            }


        }


        return $current_teachers;

    }


    public function hasQuota($subject_id, $school_year = null)
    {

        $school_year_model = $this->getSchoolYear($school_year);

        $quota = $this->qotHours()->where('qot_hours.school_year_id', $school_year_model->id)->where('qot_hours.subject_id', $subject_id)->first();

        return $quota ? $quota : false;


    }




}
