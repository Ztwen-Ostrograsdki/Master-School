<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\SchoolYear;
use Illuminate\Support\Facades\DB;




trait ClasseTraits{


    use ModelQueryTrait;

    public function getClassePupils($school_year = null)
    {
        if(!$school_year){
            $school_year = date('Y') . ' - ' . intval(date('Y') + 1);
            if(session()->has('school_year_selected') && session('school_year_selected')){
                $school_year = session('school_year_selected');
                session()->put('school_year_selected', $school_year);
            }
            else{
                session()->put('school_year_selected', $school_year);
            }
            $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        }
        else{
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::find($school_year);
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }
        }
        

        if($school_year_model){
            $pupils_of_this_year = $school_year_model->pupils()->where('level_id', $this->level_id)->get()->pluck('id')->toArray();
            $classe = $school_year_model->classes()->where('classes.id', $this->id)->first();
            if($pupils_of_this_year > 0 && $classe){
                $allPupils = [];
                foreach($classe->pupils()->orderBy('firstName', 'asc')->get() as $p){
                    if(in_array($p->id, $pupils_of_this_year)){
                        $allPupils[] = $p;
                    }
                }

                return $allPupils;
            }
        }
        return [];

    }


    public function deleteAllRelatedMarks(int $subject_id, int $semestre, $school_year)
    {
        $action = false;
        if($subject_id && $semestre && $school_year){
            DB::transaction(function($e) use ($subject_id, $semestre, $school_year, $action){
                if(is_numeric($school_year)){
                    $school_year_model = SchoolYear::where('id', $school_year)->first();
                }
                else{
                    $school_year_model = SchoolYear::where('school_year', $school_year)->first();
                }
                if($school_year_model){
                    $action = $school_year_model->related_marks()->where('classe_id', $this->id)->where('semestre', $semestre)->where('subject_id', $subject_id)->each(function($mark) use ($school_year_model){
                        $detach = $school_year_model->related_marks()->detach($mark->id);
                        if($detach){
                            $mark->delete();
                        }
                    });

                }
            });
            DB::afterCommit(function() use ($action){
                return true;
            });
        }
    }


    public function getMarks($subject_id, $semestre = 1, $school_year = null)
    {
        $allMarks = [];
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
            $pupils = $this->getPupils($school_year_model->id);
                foreach($pupils as $pupil){
                    $epes = [];
                    $parts = [];
                    $devs = [];

                    $epes = $school_year_model->marks()
                                              ->where('semestre', $semestre)
                                              ->where('subject_id', $subject_id)
                                              ->where('pupil_id', $pupil->id)
                                              ->where('classe_id', $pupil->classe_id)
                                              ->where('type', 'epe')
                                              ->orderBy('id', 'asc')->get();

                    $devs = $school_year_model->marks()
                                              ->where('semestre', $semestre)
                                              ->where('subject_id', $subject_id)
                                              ->where('pupil_id', $pupil->id)
                                              ->where('classe_id', $pupil->classe_id)
                                              ->where('type', 'devoir')
                                              ->orderBy('id', 'asc')->get();

                    $parts = $school_year_model->marks()
                                               ->where('semestre', $semestre)
                                               ->where('subject_id', $subject_id)
                                               ->where('pupil_id', $pupil->id)
                                               ->where('classe_id', $pupil->classe_id)
                                               ->where('type', 'participation')
                                               ->orderBy('id', 'asc')->get();
                    
                    $allMarks[$pupil->id] = [
                        'epe' => $epes,
                        'participation' => $parts,
                        'dev' => $devs
                    ];
                }
        }
        return $allMarks;
    }



    public function getMarksTypeLenght($subject_id, $semestre = 1, $school_year = null, $type = 'epe')
    {
        $max = 0;
        if(!$school_year){
            $school_year_model = $this->getSchoolYear();
        }
        else{
            $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        }
        if($school_year_model){
            $pupils_of_this_year = $school_year_model->pupils()->where('level_id', $this->level_id)->get()->pluck('id')->toArray();
            $classe = $school_year_model->classes()->where('classes.id', $this->id)->first();
            if($pupils_of_this_year > 0 && $classe){
                $allMarks = [];
                foreach($classe->pupils as $pupil){
                    $pupilMarks = [];
                    if(in_array($pupil->id, $pupils_of_this_year)){
                        $epes = [];
                        $devs = [];
                        $marks = $pupil->marks()->where('subject_id', $subject_id)->where('semestre', $semestre)->where('type', $type)->get();
                        if($marks && count($marks) > 0){
                            foreach($marks as $mark){
                                if($mark->school_year() && $mark->school_year()->school_year == $school_year_model->school_year){
                                    $pupilMarks[] = $mark;
                                }
                                
                            }

                            $allMarks[$pupil->id] = count($pupilMarks);
                        }
                    }
                }

                if(count($allMarks)){
                    $max = max($allMarks);
                }
            }
        }
        return $max;
    }



    public function resetAllAbsences(int $school_year, int $semestre, int $subject_id)
    {
        if($school_year && $semestre && $subject_id){
            $school_year_model = SchoolYear::find($school_year);
            if ($school_year_model) {
                $pupils = $this->getClassePupils($school_year_model->school_year);
                if($pupils){
                    foreach ($pupils as $p) {
                        $absences = $p->absences()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('subject_id', $subject_id)->get();
                        if($absences){
                            foreach ($absences as $ab) {
                                $ab->delete();
                            }
                        }
                    }
                }
            }

        }
    }


    public function resetAllLates($school_year, $semestre, $subject_id)
    {
        if($school_year && $semestre && $subject_id){
            $pupils = $this->getClassePupils($school_year);

            if($pupils){
                foreach ($pupils as $p) {
                    $lates = $p->lates()->where('school_year', $school_year)->where('semestre', $semestre)->where('subject_id', $subject_id)->get();
                    if($lates){
                        $lates->delete();
                    }
                }
            }

        }
    }


    public function resetAllMarks($school_year = null, $semestre = null, $subject_id, $type = null)
    {
        if($school_year && $semestre && $subject_id){
            if(is_numeric($school_year)){
                $school_year_model = SchoolYear::where('id', $school_year)->first();
            }
            else{
                $school_year_model = SchoolYear::where('school_year', $school_year)->first();
            }
            $marks = $school_year_model->marks()
                                       ->where('semestre', $semestre)
                                       ->where('classe_id', $this->id)
                                       ->where('subject_id', $subject_id);
            if($marks->get()->count()){
                foreach ($marks->get() as $mark) {
                    $school_year_model->marks()->detach($mark->id);
                }
                return $marks->forceDelete();

            }

        }
        else {
            $school_years = SchoolYear::all();

            foreach ($school_years as $school_year_model) {
                $marks = $school_year_model->marks()->where('classe_id', $this->id);
                if($marks->get()->count()){
                    // foreach ($marks->get() as $mark) {
                    //     $school_year_model->marks()->detach($mark->id);
                    // }
                    return $marks->forceDelete();

                }
            }

            
        }
    }


}