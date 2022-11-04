<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\PupilAbsences;
use App\Models\PupilLates;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\Schema;

trait PupilTraits{

    use ModelQueryTrait;



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
                    $epes = $school_year_model->marks()
                                           ->where('pupil_id', $this->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('type', 'epe')
                                           ->orderBy('id', 'asc')->get();
                    $parts = $school_year_model->marks()
                                           ->where('pupil_id', $this->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('type', 'participation')
                                           ->orderBy('id', 'asc')->get();
                    $devs = $school_year_model->marks()
                                           ->where('pupil_id', $this->id)
                                           ->where('semestre', $semestre)
                                           ->where('subject_id', $subject->id)
                                           ->where('classe_id', $this->classe_id)
                                           ->where('type', 'devoir')
                                           ->orderBy('id', 'asc')->get();
                    
                    $allMarks[$subject->id] = [
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



    public function getMarksTypeLenght($subject_id = null, $semestre = 1, $school_year = null, $type = 'epe')
    {
        $subjectsMarksLenght = [];
        $max = 0;

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
                    $marksLenght = $school_year_model->marks()
                                           ->where('pupil_id', $this->id)
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


    public function resetAllAbsences(int $school_year, int $semestre, int $subject_id, bool $allyears = false)
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

    public function resetAllLates(int $school_year, int $semestre, int $subject_id, bool $allyears = false)
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


    public function resetAllMarks($school_year = null, $semestre = null, $subject_id, $type = null, bool $allyears = false)
    {
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
                if(is_numeric($school_year)){
                    $school_year_model = SchoolYear::where('id', $school_year)->first();
                }
                else{
                    $school_year_model = SchoolYear::where('school_year', $school_year)->first();
                }
                $marks = $school_year_model->marks()
                                           ->where('semestre', $semestre)
                                           ->where('pupil_id', $this->id)
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