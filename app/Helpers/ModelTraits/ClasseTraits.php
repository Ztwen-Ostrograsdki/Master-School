<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
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



    public function getMarksAverage($subject_id, $semestre = 1, $school_year = null, $type = 'epe', $takeBonus = true, $takeSanctions = true)
    {
        $marksEPEs = [];
        $marksPARTs = [];
        $averageTab = [];

        if(!$subject_id){

            return $averageTab;
        }

        $allMarks = $this->getMarks($subject_id, $semestre, $school_year);

        

        foreach ($allMarks as $pupil_id => $markTab1) {
            $marksEPEs[$pupil_id] = $markTab1[$type];
            $marksPARTs[$pupil_id] = $markTab1['participation'];
        }
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

        $modality = $this->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('subject_id', $subject_id)->first();

        foreach ($marksEPEs as $pupil_id => $epeMarks) {
            $epeSom = 0;
            $partSom = 0;
            $total = 0;

            $pupilMaxMarksCount = count($epeMarks);
            $max = $pupilMaxMarksCount;



            if($modality && $modality->activated && $modality->modality < $pupilMaxMarksCount){
                $max = $modality->modality;

                $epeBestMarksAll = [];
                $epeBestMarks = [];


                foreach ($epeMarks as $epe_b) {
                    $epeBestMarksAll[$epe_b->id] = $epe_b->value;
                }

                while (count($epeBestMarks) < $max) {
                    $m = max($epeBestMarksAll);
                    $key = array_search($m, $epeBestMarksAll);
                    $epeBestMarks[$key] = $m;
                    unset($epeBestMarksAll[$key]);
                }

                $epeSom = array_sum($epeBestMarks);


            }
            else{

                foreach ($epeMarks as $epe) {
                    $epeSom = $epeSom + $epe->value;
                }
            }



            $partsMarks = $marksPARTs[$pupil_id];

            $max  = $max + count($partsMarks);



            foreach ($partsMarks as $part) {
                $partSom = $partSom + $part->value;
            }


            $total = $total + $epeSom + $partSom;

            if($type == 'epe'){

                $bonus_counter =  array_sum($school_year_model->related_marks()->where('pupil_id', $pupil_id)->where('classe_id', $this->id)->where('subject_id', $subject_id)->where('semestre', $semestre)->where('type', 'bonus')->pluck('value')->toArray());
                $minus_counter =  array_sum($school_year_model->related_marks()->where('pupil_id', $pupil_id)->where('classe_id', $this->id)->where('subject_id', $subject_id)->where('semestre', $semestre)->where('type', 'minus')->pluck('value')->toArray());

            }

            $total = $total - $minus_counter + $bonus_counter;

            if($total < 0){
                $total = 0;
            }

            if($max == 0){
                $averageTab[$pupil_id] = null;
            }
            else{
                $averageTab[$pupil_id] = floatval(number_format(($total /($max)), 2));

            }

        }

        return $averageTab;

    }

    /**
        * Get the pupil marks average about the epe marks and the devs marks
        * @return array
     */
    public function getAverage($subject_id, $semestre = 1, $school_year = null, $takeBonus = true, $takeSanctions = true)
    {

        $epeAperages = $this->getMarksAverage($subject_id, $semestre, $school_year, 'epe', $takeBonus, $takeSanctions);

        $allMarks = $this->getMarks($subject_id, $semestre, $school_year);

        $devsMarks = [];
        $averageTab = [];
        $total = 0;

        foreach ($allMarks as $pupil_id => $markTab1) {
            $devsMarks[$pupil_id] = $markTab1['dev'];
        }


        foreach ($devsMarks as $pupil_id => $devs) {
            $epeAperage = null;
            $max = count($devs);
            if(count($epeAperages) > 0){
                $epeAperage = $epeAperages[$pupil_id];
            }

            $pupilDevsMarks = [];

            if(count($devs) > 0){
                foreach ($devs as $dev) {
                    $pupilDevsMarks[$dev->id] = $dev->value;
                }
            }

            if($epeAperage !== null){
                $max = $max + 1;
                $total = $epeAperage;
            }

            if($max == 0){
                $averageTab[$pupil_id] = null;
            }
            else{
                $total = array_sum($pupilDevsMarks) + $epeAperage;
                $averageTab[$pupil_id] = floatval(number_format(($total /($max)), 2));

            }
        }



        return $averageTab;

    }


    public function getClasseRank($subject_id, $semestre = 1, $school_year = null, $takeBonus = true, $takeSanctions = true)
    {

        $averagesTab = $this->getAverage($subject_id, $semestre, $school_year, $takeBonus, $takeSanctions);
        
        $ranksTab = [];
        $ranksTab_init = [];
        $ranksTab_init_associative = [];

        foreach ($averagesTab as $pupil_id_1 => $average_1) {
            if ($average_1 !== null) {
                $ranksTab_init[$pupil_id_1] = $average_1;
            }
        }
        

        $size = count($ranksTab_init);

        if($size > 0){

            $k = 1;
            while (count($ranksTab_init_associative) < $size) {
                $av = max($ranksTab_init);
                $pupil_id = array_search($av, $ranksTab_init);
                $ranksTab_init_associative[$k] = [
                    'id' => $pupil_id,
                    'moy' => $av
                ];
                unset($ranksTab_init[$pupil_id]);
                $k++;
            }

            if($ranksTab_init_associative !== []){
                $size = count($ranksTab_init_associative);

                $index = 1;
                foreach ($ranksTab_init_associative as $key => $pupil_tab) {
                    $moy = $pupil_tab['moy'];
                    $id = $pupil_tab['id'];

                    $pupil_model = Pupil::find($id);

                    if($index == 1){
                        $rank = 1;
                        $exp = $pupil_model->sexe == 'male' ? 'er' : 'ère';
                        $base = null;
                    }
                    else{
                        $prev = $ranksTab_init_associative[$prev_index];

                        if ($prev_rank == 1) {
                            if($moy == $prev_moy){
                                $rank = 1;
                                $exp = $pupil_model->sexe == 'male' ? 'er' : 'ère';
                                $base = 'ex';
                            }
                            else{
                                if($moy == $prev_moy){
                                    $rank = $prev_rank;
                                    $exp = $pupil_model->sexe == 'male' ? 'e' : 'ème';
                                    $base = 'ex';
                                }
                                else{
                                    $rank = $index;
                                    $exp = $pupil_model->sexe == 'male' ? 'e' : 'ème';
                                    $base = null;
                                }
                            }
                        }
                        else{
                            if($moy == $prev_moy){
                                $rank = $prev_rank;
                                $exp = $pupil_model->sexe == 'male' ? 'e' : 'ème';
                                $base = 'ex';
                            }
                            else{
                                $rank = $index;
                                $exp = $pupil_model->sexe == 'male' ? 'e' : 'ème';
                                $base = null;
                            }

                        }

                        

                    }

                    $prev_rank = $rank;
                    $prev_moy = $moy;
                    $prev_index = $index;

                    $ranksTab[$id] = [
                        'rank' => $rank,
                        'exp' => $exp,
                        'base' => $base,
                        'moy' => $moy,
                        'id' => $pupil_model->id,
                    ];

                    $index++;
                    
                }

            }

        }
        else{
            return $ranksTab;
        }

        return $ranksTab;

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