<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\PupilAbsences;
use App\Models\PupilLates;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait PupilTraits{

    use ModelQueryTrait;


    public function inPolyvalenceClasse()
    {
        $target = '%' . 'polyvalente' . '%';
        
        $polyvalence = Classe::where('name', 'like', $target)->where('level_id', $this->level_id)->first();
        
        return $polyvalence ? $this->classe_id == $polyvalence->id : false;
    }

    public function inPolyvalenceClasseSince()
    {
        if($this->inPolyvalenceClasse()){
            
            $target = '%' . 'polyvalente' . '%';
            
            $polyvalence = Classe::where('name', 'like', $target)->where('level_id', $this->level_id)->first();
            $school_year_model = $this->getSchoolYear();
            
            $cursus = $this->classesSchoolYears()->where('classe_pupil_school_years.school_year_id', $school_year_model->id)->where('classe_pupil_school_years.classe_id', $polyvalence->id)->first();

            if($cursus){
                return $cursus->getDateAgoFormated(false);
            }

            return null;
        }

        return null;
    }

    public function getPupilPreclasse($school_year = null)
    {
        $data = ['classe' => null, 'school_year' => null];

        $curent_school_year_model = $this->getSchoolYear($school_year);

        $current_school_year = $curent_school_year_model->school_year;
        
        $min_year = (int)trim(explode('-', $current_school_year)[0]);

        $next_school_year = ($min_year - 1) . ' - ' . $min_year;

        $next_school_year_model = SchoolYear::where('school_year', $next_school_year)->first();

        $data['school_year'] = $next_school_year; 

        $next_cursus = $this->classesSchoolYears()->where('classe_pupil_school_years.school_year_id', $next_school_year)->first();
        if($next_cursus){
            $next_classe = Classe::find($next_cursus->classe_id);
        }
        else{
            $next_classe = null;
        }

        $data['classe'] = $next_classe;

        return $data;
    }



    public function getMarks($subject_id = null, $semestre = 1, $school_year = null)
    {
        $allMarks = [];
        $epes = [];
        $devs = [];
        $parts = [];
        if(!$school_year){
            $school_year_model = $this->getSchoolYear();
        }
        else{
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::where('id', $school_year)->first();
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }
        }
        if($school_year_model){
            $subjects = $this->classe->subjects;
            if($subjects){
                foreach ($subjects as $subject) {
                    $epes = $this->marks()
                                           ->where('school_year_id', $school_year_model->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('type', 'epe')
                                           ->orderBy('id', 'asc')->get();
                    $parts = $this->marks()
                                           ->where('school_year_id', $school_year_model->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('type', 'participation')
                                           ->orderBy('id', 'asc')->get();
                    $devs = $this->marks()
                                           ->where('school_year_id', $school_year_model->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('type', 'devoir')
                                           ->orderBy('id', 'asc')->get();
                    
                    $allMarks[$subject->id] = [
                        'id' => $subject->id,
                        'name' => $subject->name,
                        'epe' => $epes,
                        'participation' => $parts,
                        'devoir' => $devs
                    ];
                }
            }
        }
        return $allMarks;
    }

    public function getBestMark(int $semestre, $type = null, $school_year = null)
    {
        $bestMark = null;
        $school_year_model = $this->getSchoolYear($school_year);
        

        if($school_year_model){
            $marks = $this->marks()->where('school_year_id', $school_year_model->id)
                                                ->where('semestre', $semestre)
                                                ->orderBy('value', 'desc')
                                                ->get();

        }

    }
    

    public function getBadMark(int $semestre, $type = null, $school_year = null)
    {
        $bestMark = null;

        $school_year_model = $this->getSchoolYear($school_year);

        if($school_year_model){
            $marks = $this->marks()->where('school_year_id', $school_year_model->id)
                                                ->where('semestre', $semestre)
                                                ->orderBy('value', 'asc')
                                                ->get();

        }

    }



    public function getMarksTypeLenght($subject_id = null, $semestre = 1, $school_year = null, $type = 'epe')
    {
        $subjectsMarksLenght = [];
        $max = 0;

        $school_year_model = $this->getSchoolYear($school_year);
        if($school_year_model){
            $subjects = $this->classe->subjects;
            if($subjects){
                foreach ($subjects as $subject) {
                    $marksLenght = $this->marks()
                                           ->where('school_year_id', $school_year_model->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('type', $type)
                                           ->orderBy('id', 'asc')->count();

                    $subjectsMarksLenght[$subject->id] = $marksLenght;
                }

                if($subjectsMarksLenght){
                    $max = max($subjectsMarksLenght);
                }
            }
            else{
                $max = 0;

            }
        }
        return $max;
    }





    public function isAbsentThisDay($date = null, $school_year = null, $semestre = null, $subject_id = null)
    {
        if($date && $school_year && $semestre && $subject_id){
            $true = PupilAbsences::where('pupil_id', $this->id)
                                ->where('subject_id', $subject_id)
                                ->where('semestre', $semestre)
                                ->where('school_year_id', $school_year)
                                ->where('date', $date)
                                ->first();
            if($true){
                return $true;
            }
            return false;
        }
        return false;
    }


    public function wasLateThisDayFor($date = null, $school_year = null, $semestre = null, $subject_id = null)
    {
        if($date && $school_year && $semestre && $subject_id){
            $true = PupilLates::where('pupil_id', $this->id)
                                ->where('subject_id', $subject_id)
                                ->where('semestre', $semestre)
                                ->where('school_year_id', $school_year)
                                ->where('date', $date)
                                ->first();
            if($true){
                return $true;
            }
            return false;
        }
        return false;
    }



    public function getAbsencesCounter($school_year = null, $semestre = null, $subject_id = null)
    {
        if($school_year && $semestre && $subject_id){
            
        }
        else{
            $subject_id = session('classe_subject_selected');
            $semestre = session('semestre_selected');
            $school_year_model = $this->getSchoolYear();
            $absences = $this->absences()
                              ->where('subject_id', $subject_id)
                              ->where('semestre', $semestre)
                              ->where('school_year', $school_year_model->school_year)
                              ->get();
            return count($absences);
                                
        }
    }


    public function getLatesCounter($school_year = null, $semestre = null, $subject_id = null)
    {
        if($school_year && $semestre && $subject_id){
            
        }
        else{
            $subject_id = session('classe_subject_selected');
            $semestre = session('semestre_selected');
            $school_year_model = $this->getSchoolYear();
            $lates = $this->lates()
                              ->where('subject_id', $subject_id)
                              ->where('semestre', $semestre)
                              ->where('school_year', $school_year_model->school_year)
                              ->get();
            return count($lates);
                                
        }
    }


    public function dateManager()
    {


    }


    public function resetAllAbsences(int $school_year, int $semestre, int $subject_id, $classe_id = null, bool $allyears = false)
    {
        if($school_year && $semestre && $subject_id){
            if($allyears){
                $absences = $this->absences()->where('semestre', $semestre)->where('subject_id', $subject_id)->where('classe_id', $this->classe_id);
                if($absences->get()){
                    $d = $absences->delete();
                    return $diff;
                }
            }
            elseif($school_year && !$allyears){
                $school_year_model = SchoolYear::find($school_year);
                if ($school_year_model) {
                    $absences = $this->absences()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('subject_id', $subject_id)->where('classe_id', $this->classe_id);
                    if($absences->get()){
                        $d = $absences->delete();
                        return $diff;
                    }
                }
                return false;
            }

        }
    }

    public function resetAllLates(int $school_year, int $semestre, int $subject_id, $classe_id = null, bool $allyears = false)
    {
        if($semestre && $subject_id){
            if($allyears){
                $lates = $this->lates()->where('semestre', $semestre)->where('subject_id', $subject_id)->where('classe_id', $this->classe_id);
                if($lates->get()){
                    $d = $lates->delete();
                    return $d;
                }
            }
            elseif($school_year && !$allyears){
                $school_year_model = SchoolYear::find($school_year);
                if ($school_year_model) {
                    $lates = $this->lates()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('subject_id', $subject_id)->where('classe_id', $this->classe_id);
                    if($lates->get()){
                        $d = $lates->delete();
                        return $d;
                    }
                }
                return false;
            }
            else{
                return false;
            }

        }
    }


    public function resetAllMarks($school_year = null, $semestre = null, $subject_id, $classe_id = null, $type = null, bool $allyears = false)
    {
        if($classe_id){
            $classe_id = Classe::find($classe_id);
            if($classe){
                $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe_id);
                if ($not_secure) {
                    if($semestre && $subject_id){
                        if($allyears){
                            $school_years = SchoolYear::all();
                            foreach ($school_years as $school_year_model) {
                                $marks = $school_year_model->marks()->where('pupil_id', $this->id)->where('classe_id', $this->classe_id);
                                if($marks->get()->count()){
                                    return $marks->forceDelete();
                                }
                            }

                        }
                        else{
                            $school_year_model = $this->getSchoolYear($school_year);
                            $marks = $this->marks()
                                           ->where('semestre', $semestre)
                                           ->where('school_year_id', $school_year_model->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('subject_id', $subject_id);
                            if($marks->get()->count()){
                                foreach ($marks->get() as $mark) {
                                    $school_year_model->marks()->detach($mark->id);
                                }
                                return $marks->forceDelete();
                            }
                        }
                    }
                }

            }


        }
        
    }


    public function deleteAllPupilRelatedMarks(int $classe_id, int $subject_id, int $semestre, $school_year)
    {
        $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe_id);
        
        if ($not_secure) {
            if($subject_id && $semestre && $school_year){
                DB::transaction(function($e) use ($subject_id, $semestre, $school_year, $classe_id){
                    $school_year_model = $this->getSchoolYear($school_year);
                    if($school_year_model){
                        $this->related_marks()->where('school_year_id', $school_year_model->id)->where('semestre', $semestre)->where('classe_id', $classe_id)->where('subject_id', $subject_id)->each(function($mark) use ($school_year_model){
                            $detach = $school_year_model->related_marks()->detach($mark->id);
                            if($detach){
                                $mark->delete();
                            }
                        });
                        return true;
                    }
                    return false;
                });
            }
        }
        return false;
    }


    
}