<?php
namespace App\Helpers\ModelTraits;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassesSecurity;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;


trait TeachersTrait{

	use ModelQueryTrait;


	public function teacherCanUpdateMarksInThisClasse($classe_id)
    {
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

        if($classe){

            $teacher_classes = $this->getTeachersCurrentClasses();

            if(array_key_exists($classe->id, $teacher_classes)){

                if($classe->hasSecurities()){

                    $locked_marks_updating_for_teacher = $classe->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->where('teacher_id', $this->id)->get();

                    $locked_marks_updating_for_all = $classe->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->get();

                    if(!$locked_marks_updating_for_all && !$locked_marks_updating_for_teacher){

                        return true;
                    }
                    else{
                        return false;
                    }
                }
                else{
                    return true;
                }
            }
            else{
                return false;
            }
        }
        else{
            return false;

        }
    }


    public function getCurrentTimePlans($classe_id = null, $school_year_id = null)
    {
        $school_year_model = $this->getSchoolYear($school_year_id);

        $school_year_id = $school_year_model->id;

        
        if($classe_id){
            return $this->timePlans()->where('time_plans.classe_id', $classe_id)->where('time_plans.school_year_id', $school_year_id)->where('time_plans.subject_id', $this->speciality()->id)->orderBy('day_index', 'asc')->get();
        }
        else{
            return $this->timePlans()->where('time_plans.school_year_id', $school_year_id)->where('time_plans.subject_id', $this->speciality()->id)->orderBy('day_index', 'asc')->get();
        }
    }

    public function getLastTeachingDate()
    {
        $date = $this->last_teaching_date;

        $formatted_date = $this->__getDateAsString($date, null);

        return  ucwords($this->__getDateAsString($date, null)) ;
        
    }


    public function getInsertToClasseSince($classe_id, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $cursus = $this->cursus()->where('teacher_cursuses.school_year_id', $school_year_model->id)
                                 ->where('teacher_cursuses.classe_id', $classe_id)
                                 ->where('teacher_cursuses.end', null)
                                 ->first();
        if($cursus){
            $date = $cursus->updated_at;

            $formatted_date = $this->__getDateAsString($date, null);

            return  ucwords($this->__getDateAsString($date, null)) ;

        }
        return 'Inconnue';
        
    }


    public function teacherHasCourseAtThis($classe_id, $start, $end, $day = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if(!$day){
            setlocale(LC_TIME, "fr_FR.utf8", 'fra');

            $day = strftime('%A', time());

        }

        $subject_id = $this->speciality()->id;
        
        $has = $this->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->where('time_plans.classe_id', $classe_id)->where('time_plans.subject_id', $subject_id)->where('time_plans.day', $day)->where('time_plans.start', $start)->where('time_plans.end', $end)->first();

        return $has ? true : false;

    }

    public function hasTimePlansForThisClasse($classe_id, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $subject_id = $this->speciality()->id;

        $has = $this->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->where('time_plans.classe_id', $classe_id)->where('time_plans.subject_id', $subject_id)->first();

        return $has ? true : false;

    }


    public function teacherWasFreeInThisTime($start, $end, $day, $school_year_id = null, $except = null)
    {
        if(!$school_year_id){
            $school_year_id = $this->getSchoolYear()->id;
        }

        if($except){
            
            $times = $this->timePlans()->where('time_plans.school_year_id', $school_year_id)->where('time_plans.day', $day)->where('time_plans.id', '<>', $except)->get();
        }
        else{

            $times = $this->timePlans()->where('time_plans.school_year_id', $school_year_id)->where('time_plans.day', $day)->get();

        }

        if(count($times) > 0){
            foreach($times as $time){
                $time_start = $time->start;
                $time_end = $time->end;

                if($end >= $time_start && $end <= $time_end){
                    return false ;
                    break;
                }
                elseif($start >= $time_start && $end <= $time_end){
                    return false;
                    break;
                }
                elseif($start <= $time_end && $end >= $time_end){
                    return false;
                    break;
                }
                elseif($start <= $time_start && $end >= $time_end){
                    return false;
                    break;
                }

            }

            return true;

        }
        return true;
    }


    public function getTeacherTodayCourses($classe_id, $day = null, $school_year = null)
    {
        $school_year_id = $this->getSchoolYear($school_year)->id;

        $time_plans = [];

        

        if(!$day){
            setlocale(LC_TIME, "fr_FR.utf8", 'fra');

            $day = strftime('%A', time());

        }

        $times = $this->timePlans()->where('time_plans.school_year_id', $school_year_id)->where('time_plans.classe_id', $classe_id)->where('time_plans.day', $day)->get();


        if(count($times) > 0){

            foreach($times as $time){

                $now_hour = date('H');

                $now_timestamp = Carbon::now()->timestamp;

                $start = $time->start;

                $start_timestamp = Carbon::parse($start)->timestamp;

                $s = $start . 'H';

                $end = $time->end;

                $end_timestamp = Carbon::parse($end)->timestamp;

                $e = $end . 'H';

                $duration = $time->duration;

                $d = $duration . 'H de cours';



                if($start_timestamp < $now_timestamp && ($start_timestamp + $duration) < $now_timestamp){

                    $time_plans[$time->id] = "Le prof fera cours aujourd'hui de $s à $e ! ($d)";

                }
                elseif($start_timestamp <= $now_timestamp && $end_timestamp >= $now_timestamp){

                    $time_plans[$time->id] = "Le prof est actuellement au cours depuis $s, il finira à $e ! ($d)" ;

                }
                elseif($start_timestamp > $now_timestamp){

                    $time_plans[$time->id] = "Le prof a fait cours aujourd'hui de $s à $e ! ($d)";

                }
            }

        }
        else{

            $time_plans[] = "Le prof n'a pas cours aujourd'hui avec cette classe";

        }

        return $time_plans;

    }


    public function isTeacherOfThisYear($school_year = null)
    {

        $school_year_model = $this->getSchoolYear($school_year);

        $is = $school_year_model->teachers()->where('teachers.id', $this->id)->first();

        return $is ? true : false;
    }

    public function isNotTeacherOfThisYear($school_year = null)
    {
        
        return !($this->isTeacherOfThisYear($school_year));
    }


    public function teacherDeleter($destroy = false)
    {

        $school_year_model = $this->getSchoolYear();

        DB::transaction(function($e) use ($school_year_model, $destroy){

            $this->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->each(function($tp){

                $tp->update(['teacher_id' => null]);

            });

            $this->securities()->where('classes_securities.school_year_id', $school_year_model->id)->each(function($sec){

                $sec->delete();

            });
            
            $this->lates()->where('teacher_lates.school_year_id', $school_year_model->id)->each(function($late){

                $late->delete();

            });
            $this->absences()->where('teacher_absences.school_year_id', $school_year_model->id)->each(function($abs){

                $abs->delete();
            });

            $this->cursus()->where('teacher_cursuses.school_year_id', $school_year_model->id)->each(function($cursus){

                $cursus->delete();

            });


            $principal = $this->principal($school_year_model->id);

            $ae = $this->ae($school_year_model->id);

            if($principal){

                $principal->delete();
            }

            if($ae){
                
                $ae->delete();
            }


            $school_year_model->teachers()->detach($this->id);


            if($destroy){

                $this->timePlans()->each(function($tp){

                $tp->update(['teacher_id' => null]);

                });

                $this->securities()->each(function($sec){

                    $sec->delete();

                });
                
                $this->lates()->each(function($late){

                    $late->delete();

                });

                $this->absences()->each(function($abs){

                    $abs->delete();
                });

                $this->principals()->each(function($pr){

                    $pr->delete();
                });

                $this->aes()->each(function($ae){

                    $ae->delete();
                });

                $this->school_years()->each(function($school_year){

                    $school_year->teachers()->detach($this->id);

                });

                $this->cursus()->each(function($cursus){

                    $cursus->delete();

                });

                $this->forceDelete();

            }


        });


    }






}
