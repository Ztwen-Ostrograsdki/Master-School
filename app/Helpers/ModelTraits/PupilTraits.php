<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobPupilDeleterFromDatabase;
use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\PupilAbsences;
use App\Models\PupilLates;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

trait PupilTraits{

    use ModelQueryTrait;


    /**
     * Get and return the polyvalente classe
     */
    public function polyvalenteClasse($level = 'secondary')
    {
        $target = '%' . 'polyvalente' . '%';

        $level_id = $this->level->id;

        return Classe::where('name', 'like', $target)->where('level_id', $level_id)->first();
    }


    public function pupilDeleter($school_year = null, $destroy = false)
    {
        
        if(!$destroy){

            $school_year_model = $this->getSchoolYear($school_year);

            $pupil = $this;

            $pupil->lates()->where('pupil_lates.school_year_id', $school_year_model->id)->each(function($late){
                $late->delete();

            });

            $pupil->absences()->where('pupil_absences.school_year_id', $school_year_model->id)->each(function($abs){
                $abs->delete();
            });

            $pupil->marks()->where('marks.school_year_id', $school_year_model->id)->each(function($mark){
                $mark->delete();
            });

            $pupil->related_marks()->where('related_marks.school_year_id', $school_year_model->id)->each(function($r_m){
                $r_m->delete();
            });


            $classe = $this->getCurrentClasse($school_year_model->id);

            if($classe){

                $pupil->pupilClassesHistoriesBySchoolYears()->where('classe_pupil_school_years.school_year_id', $school_year_model->id)
                  ->where('classe_pupil_school_years.classe_id', $classe->id)
                  ->first()
                  ->delete();

                $classe->classePupils()->detach($pupil->id);

                $classeVolante = $this->polyvalenteClasse();

                if($classeVolante){

                    ClassePupilSchoolYear::create(
                        [
                            'classe_id' => $classeVolante->id,
                            'pupil_id' => $pupil->id,
                            'school_year_id' => $school_year_model->id,
                        ]
                    );

                    $pupil->update(['classe_id' => $classeVolante->id]);

                    $classeVolante->classePupils()->attach($pupil->id);
                }

            }


        }
        else{

            dispatch(new JobPupilDeleterFromDatabase($this))->delay(Carbon::now()->addSeconds(30));

        }
        
    }

    public function pupilDestroyer($school_year)
    {
        $this->pupilDeleter($school_year, true);
    }


    public function lockPupilMarksUpdating($duration = 48, $classe_id = null, $subject_id = null, $school_year = nll)
    {

    }


    public function lockPupilMarksInsertion($duration = 48, $classe_id = null, $subject_id = null, $school_year = nll)
    {

    }

    public function unlockPupilMarksUpdating($classe_id = null, $subject_id = null, $school_year = null)
    {
        if(!$classe_id){

            $classe_id = $this->getCurrentClasse()->id;
        }

        $canUpdate = $this->canUpdateMarksOfThisPupil($classe_id, $subject_id, $school_year);

        $unlocked = false;

        if(!$canUpdate){

            if($subject_id){

                $unlocked = $this->securities()
                                 ->where('classes_securities.school_year_id', $school_year_model->id)
                                 ->where('classes_securities.subject_id', $subject_id)
                                 ->where('locked_marks_updating', true)
                                 ->where('classes_securities.classe_id', $classe_id)
                                 ->delete();
            }
            else{
                $unlocked = $this->securities()
                                 ->where('classes_securities.school_year_id', $school_year_model->id)
                                 ->where('locked_marks_updating', true)
                                 ->where('classes_securities.classe_id', $classe_id)
                                 ->delete();
            }
        }

        return $unlocked;

    }


    public function unlockPupilMarksInsertion($classe_id = null, $subject_id = null, $school_year = null)
    {
        if(!$classe_id){
            
            $classe_id = $this->getCurrentClasse()->id;
        }

        $canInsert = $this->canInsertOrUpdateMarksOfThisPupil($classe_id, $subject_id, $school_year);

        $unlocked = false;

        if(!$canInsert){
            if($subject_id){
                $unlocked = $this->securities()
                                 ->where('classes_securities.school_year_id', $school_year_model->id)
                                 ->where('classes_securities.subject_id', $subject_id)
                                 ->where('classes_securities.classe_id', $classe_id)
                                 ->where('locked_marks_updating', true)
                                 ->orWhere('locked_marks', true)
                                 ->delete();
            }
            else{
                $unlocked = $this->securities()
                                 ->where('classes_securities.school_year_id', $school_year_model->id)
                                 ->where('classes_securities.classe_id', $classe_id)
                                 ->where('locked_marks_updating', true)
                                 ->orWhere('locked_marks', true)
                                 ->delete();
            }
        }

        return $unlocked;

    }

    public function canUpdateMarksOfThisPupil($classe_id = null, $subject_id = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if(!$classe_id){

            $current_classe = $this->getCurrentClasse();

            if($current_classe){

                $classe_id = $current_classe->id;
            }
            else{
                return false;

            }
            
        }

        if($subject_id){
            $secure = $this->securities()
                           ->where('classes_securities.school_year_id', $school_year_model->id)
                           ->where('classes_securities.subject_id', $subject_id)
                           ->where('locked_marks_updating', true)
                           ->where('classes_securities.classe_id', $classe_id)
                           ->first();
        }
        else{
            $secure = $this->securities()
                           ->where('classes_securities.school_year_id', $school_year_model->id)
                           ->where('locked_marks_updating', true)
                           ->where('classes_securities.classe_id', $classe_id)
                           ->first();
        }

        return $secure ? false : true;
        
    }


    public function canInsertOrUpdateMarksOfThisPupil($classe_id = null, $subject_id = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $current_classe = $this->getCurrentClasse();

        if($current_classe){

            $classe_id = $current_classe->id;
        }
        else{
            return false;

        }

        if($subject_id){
            $secure = $this->securities()
                           ->where('classes_securities.school_year_id', $school_year_model->id)
                           ->where('classes_securities.subject_id', $subject_id)
                           ->where('classes_securities.classe_id', $classe_id)
                           ->where('locked_marks_updating', true)
                           ->orWhere('locked_marks', true)
                           ->first();
        }
        else{
            $secure = $this->securities()
                           ->where('classes_securities.school_year_id', $school_year_model->id)
                           ->where('classes_securities.classe_id', $classe_id)
                           ->where('locked_marks_updating', true)
                           ->orWhere('locked_marks', true)
                           ->first();
        }

        return $secure ? false : true;
        
    }


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


    public function deletePupilAbsences($subject_id = null, $semestre = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($school_year){

            if($subject_id){

                if($semestre){

                    $this->absences()->where('pupil_absences.school_year_id', $school_year_model->id)->where('pupil_absences.subject_id', $subject_id)->where('pupil_absences.semestre', $semestre)->each(function($abs){

                        $abs->delete();
                    });

                }
                else{

                    $this->absences()->where('pupil_absences.school_year_id', $school_year_model->id)->where('pupil_absences.subject_id', $subject_id)->each(function($abs){
                        
                        $abs->delete();
                    });


                }

            }
            else{
                $this->absences()->where('pupil_absences.school_year_id', $school_year_model->id)->where('pupil_absences.semestre', $semestre)->each(function($abs){
                        
                    $abs->delete();
                });

            }

        }
        else{

            if($subject_id){

                if($semestre){

                    $this->absences()->where('pupil_absences.subject_id', $subject_id)->where('pupil_absences.semestre', $semestre)->each(function($abs){

                        $abs->delete();
                    });

                }
                else{

                    $this->absences()->where('pupil_absences.subject_id', $subject_id)->each(function($abs){
                        
                        $abs->delete();
                    });


                }

            }
            else{
                $this->absences()->where('pupil_absences.semestre', $semestre)->each(function($abs){
                        
                    $abs->delete();
                });

            }

        }
    }



    public function deletePupilLates($subject_id = null, $semestre = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($school_year){

            if($subject_id){

                if($semestre){

                    $this->lates()->where('pupil_lates.school_year_id', $school_year_model->id)->where('pupil_lates.subject_id', $subject_id)->where('pupil_lates.semestre', $semestre)->each(function($late){

                        $late->delete();
                    });

                }
                else{

                    $this->lates()->where('pupil_lates.school_year_id', $school_year_model->id)->where('pupil_lates.subject_id', $subject_id)->each(function($late){
                        
                        $late->delete();
                    });


                }

            }
            else{
                $this->lates()->where('pupil_lates.school_year_id', $school_year_model->id)->where('pupil_lates.semestre', $semestre)->each(function($late){
                        
                    $late->delete();
                });

            }

        }
        else{

            if($subject_id){

                if($semestre){

                    $this->lates()->where('pupil_lates.subject_id', $subject_id)->where('pupil_lates.semestre', $semestre)->each(function($late){

                        $late->delete();
                    });

                }
                else{

                    $this->lates()->where('pupil_lates.subject_id', $subject_id)->each(function($late){
                        
                        $late->delete();
                    });


                }

            }
            else{
                $this->lates()->where('pupil_lates.semestre', $semestre)->each(function($late){
                        
                    $late->delete();
                });

            }

        }
    }



    public function deletePupilMarks($semestre = null, $school_year = null, $subject_id, $type = null)
    {
        DB::transaction(function($e) use($school_year, $semestre, $subject_id, $type){

            $school_year_model = $this->getSchoolYear($school_year);

            $pupil_id = $this->id;

            if($subject_id && $type && $semestre){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->where('marks.semestre', $semestre)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id && $semestre){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->where('marks.semestre', $semestre)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id && $type){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($semestre && $type){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.semestre', $semestre)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }

            elseif($type){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($semestre){

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->where('marks.semestre', $semestre)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            else{

                $school_year_model->marks()
                                  ->where('marks.pupil_id', $pupil_id)
                                  ->each(function($mark){

                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });


            }


        });

        DB::afterCommit(function(){

            return true;
        });

       
    }


    public function deletePupilRelatedMarks($semestre = null, $school_year = null, $subject_id = null)
    {
        DB::transaction(function($e) use($school_year, $semestre, $subject_id){

            $school_year_model = $this->getSchoolYear($school_year);

            $pupil_id = $this->id;

            if($subject_id && $semestre){

                $school_year_model->related_marks()
                                  ->where('related_marks.pupil_id', $pupil_id)
                                  ->where('related_marks.subject_id', $subject_id)
                                  ->where('related_marks.semestre', $semestre)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id){

                $school_year_model->related_marks()
                                  ->where('related_marks.pupil_id', $pupil_id)
                                  ->where('related_marks.subject_id', $subject_id)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($semestre){

                $school_year_model->related_marks()
                                  ->where('related_marks.pupil_id', $pupil_id)
                                  ->where('related_marks.semestre', $semestre)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            else{

                $school_year_model->related_marks()
                                  ->where('related_marks.pupil_id', $pupil_id)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            
        });

        DB::afterCommit(function(){

            return true;
        });
    }



    public function isPupilOfThisYear($school_year = null)
    {

        $school_year_model = $this->getSchoolYear($school_year);

        $is = $school_year_model->pupils()->where('pupils.id', $this->id)->first();

        return $is ? true : false;
    }


    public function getPupilAveragesWithRank($school_year = null, $semestre = null, $all = false)
    {

        $school_year_model = $this->getSchoolYear($school_year);
        
        $semestrialAverage = [];
        
        $annualAverages = [];
        
        $annualAverage = null;

        $data = null;
        
        $semestre_type = 'Semestre';

        $school = School::first();

        $semestres = [1, 2];

        if($school){

            if($school->trimestre){

                $semestre_type = 'Trimestre';

                $semestres = [1, 2, 3];
            }
            else{

                $semestres = [1, 2];
            }
        }

        $pupil = $this;

        $classe = $pupil->getCurrentClasse($school_year_model->id);

        if($classe){

            if(!$semestre && !$all){

                $annualAverages = $classe->getClasseAnnualAverageWithRank($school_year_model->id);

                if(isset($annualAverages[$pupil->id])){

                    $annualAverage = $annualAverages[$pupil->id];
                }

                $data = $annualAverage;

            }
            else{

                if($semestre){

                    $semestrialAverage = $classe->getClasseSemestrialAverageWithRank($semestre, $school_year_model->id);

                    if(isset($semestrialAverage) && isset($semestrialAverage[$pupil->id])){

                        $semestrialAverage = $semestrialAverage[$pupil->id];
                    }
                    else{

                        $semestrialAverage = null;

                    }


                    $data = $semestrialAverage;


                }
                elseif(!$semestre || $all){

                    foreach($semestres as $sm){

                        $semestrialAverage[$sm] = $classe->getClasseSemestrialAverageWithRank($sm, $school_year_model->id);
                        
                        if(isset($semestrialAverage[$sm]) && isset($semestrialAverage[$sm][$pupil->id])){

                            $semestrialAverage[$sm] = $semestrialAverage[$sm][$pupil->id];
                        }
                        else{

                            $semestrialAverage[$sm] = null;

                        }

                    }


                    $data = $semestrialAverage;

                }


            }

            
        }

        return $data;


        
    }


    public function getLastClasse()
    {
        $classes = [];

        $last_classe = null;

        $classes_school_years = $this->classesSchoolYears;

        $current_school_year_model = $this->getSchoolYear();

        if($classes_school_years){

            foreach($classes_school_years as $c_s_y){

                $classe = $c_s_y->classe;

                $school_year_model = $c_s_y->school_year;

                $not_same_school_year = $school_year_model->id !== $current_school_year_model->id;

                $not_same_school_year = true;


                if($classe->isNotPolyvalente() && $not_same_school_year){

                    $index = array_sum(explode(' - ', $school_year_model->school_year));

                    $classes[$classe->id] = ['index' => $index, 'classe' => $classe, 'school_year' => $school_year_model];

                }
            }


            $max = 0;

            foreach($classes as $classe_id => $cl){

                $index = $cl['index'];

                $classe = $cl['classe'];

                $sy_model = $cl['school_year'];

                if($index >= $max && $classe->isNotPolyvalente()){

                    $max = $index;

                    $last_classe = ['classe' => $classe, 'school_year' => $sy_model];

                }

            }

        }

        return $last_classe;

    }


    public function getPupilNullMarks($classe_id, $semestre, $school_year = null, $subject_id = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $marks = [];

        if(!$classe_id){

            $classe = $this->getCurrentClasse();

            if($classe){

                $classe_id = $classe->id;

            }

        }

        if($classe_id){

            if($subject_id){

                $marks = $this->marks()->where('marks.school_year_id', $school_year_model->id)
                                   ->where('marks.subject_id', $subject_id)
                                   ->where('marks.classe_id', $classe_id)
                                   ->where('marks.semestre', $semestre)
                                   ->where('marks.value', 0)
                                   ->get();

            }
            else{
                $marks = $this->marks()->where('marks.school_year_id', $school_year_model->id)
                                   ->where('marks.classe_id', $classe_id)
                                   ->where('marks.semestre', $semestre)
                                   ->where('marks.value', 0)
                                   ->get();

            }

       }

        return $marks;

    }

    /**
     * Assert if a classe or pupil of a classe has nulls marks for a specific subject with subject_id
     */
    public function hasNullsMarks($classe_id, $semestre, $school_year = null, $subject_id = null)
    {

        $marks = $this->getPupilNullMarks($classe_id, $semestre, $school_year, $subject_id);

        return count($marks) > 0;

    }


    public function getChoosenMarks($classe_id, $subject_id, $semestre, $school_year_id = null)
    {
        $school_year_model = $this->getSchoolYear($school_year_id);

        $classe = Classe::find($classe_id);

        if($classe){

            $epeMarks = $this->marks()
                             ->where('marks.school_year_id', $school_year_model->id)
                             ->where('marks.semestre', $semestre)
                             ->where('marks.subject_id', $subject_id)
                             ->where('marks.classe_id', $classe_id)
                             ->where('marks.type', 'epe')
                             ->get();

            $totalMarks = count($epeMarks);

            $max = $totalMarks;

            $modality = $classe->averageModalities()
                               ->where('school_year', $school_year_model->school_year)
                               ->where('semestre', $semestre)
                               ->where('subject_id', $subject_id)
                               ->first();


            if($modality && $modality->activated && $modality->modality < $totalMarks){

                $max = $modality->modality;

                $choosenEpesMarks = [];

                $epeMarksValues = [];

                foreach ($epeMarks as $epe_b) {

                    $epeMarksValues[$epe_b->id] = $epe_b->value;
                }

                while (count($choosenEpesMarks) < $max) {
                    
                    $m = max($epeMarksValues);
                    
                    $key = array_search($m, $epeMarksValues);
                    
                    $choosenEpesMarks[$key] = $key;
                    
                    unset($epeMarksValues[$key]);
                }

                return $choosenEpesMarks;

            }
            elseif($modality && $modality->activated && $modality->modality >= $totalMarks){

                foreach ($epeMarks as $epe) {

                    $choosenEpesMarks[$epe->id] = $epe->id;
                }
            }
            if(!$modality || ($modality && !$modality->activated)){

                //DO ANYTHINK

                $choosenEpesMarks = [];

            }

        }
        else{

        }


        return $choosenEpesMarks;

    }
    
}