<?php
namespace App\Helpers\ModelTraits;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassePupilSchoolYear;
use App\Models\ClassesSecurity;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\Teacher;
use Illuminate\Support\Facades\DB;




trait ClasseTraits{


    use ModelQueryTrait;

    public function classeWasFreeInThisTime($start, $end, $day, $school_year_id = null, $except = null)
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


    public function isClasseOfThisYear($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $classe = $school_year_model->classes()->where('classes.id', $this->id)->first();

        return $classe ? true : false;
    }



    public function lockMarksUpdatingForThisTeacher($teacher_id, $duration = 24)
    {
        $school_year_model = $this->getSchoolYear();

        $locked_marks_updating = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->where('teacher_id', $teacher_id)->get();

        if(!$locked_marks_updating){

            $locked = ClassesSecurity::create([
                'teacher_id' => $teacher_id,
                'classe_id' => $this->id,
                'school_year_id' => $school_year_model->id,
                'duration' => $duration,
                'locked_marks_updating' => true,
            ]);

            return $locked ? $locked : false;
        }
        return false;
    }



    public function getClassePupils($school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);
        
        if($school_year_model){

            $allPupils = [];

            $pupils_of_this_year = $school_year_model->pupils()->where('level_id', $this->level_id)->get()->pluck('id')->toArray();

            $classe = $school_year_model->classes()->where('classes.id', $this->id)->first();

            if($pupils_of_this_year > 0 && $classe){
                
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


    public function getClasseCurrentPupils($school_year_id)
    {
        $school_year_model = $this->getSchoolYear($school_year_id);

        $pupils = [];

        $data = $school_year_model->classeWithPupils()->where('classe_id', $this->id)->get();

        if(count($data) > 0){

            foreach($data as $datum){

                $pupils = $school_year_model->pupils()->where('pupils.id', $datum->pupil_id)->orderBy('pupils.firstName', 'asc')->orderBy('pupils.lastName', 'asc')->get();
            }
        }

        return $pupils;
    }




    public function getMarks($subject_id, $semestre = 1, $take_forget = true, $school_year = null)
    {
        $allMarks = [];

        $school_year_model = $this->getSchoolYear($school_year);

        if($school_year_model){

            $pupils = $this->getPupils($school_year_model->id);

            foreach($pupils as $pupil){

                $epes = [];

                $parts = [];

                $devs = [];

                $forced_marks = [];

                $not_forced_marks = [];

                if($take_forget == 2){

                    $epes = $this->marks()
                                          ->where('marks.school_year_id', $school_year_model->id)
                                 ->where('semestre', $semestre)
                                 ->where('subject_id', $subject_id)
                                 ->where('pupil_id', $pupil->id)
                                 ->where('type', 'epe')
                                 ->orderBy('id', 'asc')->get();

                    $parts = $this->marks()
                                  ->where('marks.school_year_id', $school_year_model->id)
                                   ->where('semestre', $semestre)
                                   ->where('subject_id', $subject_id)
                                   ->where('pupil_id', $pupil->id)
                                   ->where('type', 'participation')
                                   ->orderBy('id', 'asc')->get();
                }
                else{

                    $epes = $this->marks()
                                          ->where('marks.school_year_id', $school_year_model->id)
                                          ->where('semestre', $semestre)
                                          ->where('subject_id', $subject_id)
                                          ->where('pupil_id', $pupil->id)
                                          ->where('type', 'epe')
                                          ->where('forget', !$take_forget)
                                          ->orderBy('id', 'asc')->get();

                    $parts = $this->marks()
                                  ->where('marks.school_year_id', $school_year_model->id)
                                   ->where('semestre', $semestre)
                                   ->where('subject_id', $subject_id)
                                   ->where('pupil_id', $pupil->id)
                                   ->where('forget', !$take_forget)
                                   ->where('type', 'participation')
                                   ->orderBy('id', 'asc')->get();

                }

                $forced_marks = $this->marks()
                                      ->where('marks.school_year_id', $school_year_model->id)
                                      ->where('semestre', $semestre)
                                      ->where('subject_id', $subject_id)
                                      ->where('pupil_id', $pupil->id)
                                      ->where('type', 'epe')
                                      ->where('forget', !$take_forget)
                                      ->where('forced_mark', true)
                                      ->orderBy('id', 'asc')->get();

                $not_forced_marks = $this->marks()
                                          ->where('marks.school_year_id', $school_year_model->id)
                                          ->where('semestre', $semestre)
                                          ->where('subject_id', $subject_id)
                                          ->where('pupil_id', $pupil->id)
                                          ->where('type', 'epe')
                                          ->where('forget', !$take_forget)
                                          ->where('forced_mark', false)
                                          ->orderBy('id', 'asc')->get();

                $devs = $this->marks()
                              ->where('marks.school_year_id', $school_year_model->id)
                              ->where('semestre', $semestre)
                              ->where('subject_id', $subject_id)
                              ->where('pupil_id', $pupil->id)
                              ->where('type', 'devoir')
                              ->where('forget', !$take_forget)
                              ->orderBy('id', 'asc')->get();

                
                
                $allMarks[$pupil->id] = [
                    'epe' => $epes,
                    'forced_marks' => $forced_marks,
                    'not_forced_marks' => $not_forced_marks,
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

        $not_forced_marks = [];

        $forced_marks = [];

        if(!$subject_id){

            return $averageTab;
        }

        $allMarks = $this->getMarks($subject_id, $semestre, $school_year, false);


        foreach ($allMarks as $pupil_id => $markTab1) {
            // $marksEPEs[$pupil_id] = $markTab1[$type];
            $marksEPEs[$pupil_id] = $markTab1['not_forced_marks'];
        }


        $school_year_model = $this->getSchoolYear($school_year);

        $modality = $this->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('subject_id', $subject_id)->first();

        foreach ($marksEPEs as $pupil_id => $epeMarks) {
            $epeSom = 0;
            $partSom = 0;
            $forcedSom = 0;
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



            $partsMarks = $allMarks[$pupil_id]['participation'];
            $forced_marks = $allMarks[$pupil_id]['forced_marks'];

            $max  = $max + count($partsMarks) + count($forced_marks);

            foreach ($partsMarks as $part) {
                $partSom = $partSom + $part->value;
            }

            foreach ($forced_marks as $forced_m) {
                $forcedSom = $forcedSom + $forced_m->value;
            }



            $total = $total + $epeSom + $partSom + $forcedSom;

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
                        'pupil' => $pupil_model,
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

    public function getClasseStats($semestre = 1, $school_year = null, $subject_id_selected = null, $takeBonus = true, $takeSanctions = true)
    {
        $data = [];
        $subjects = [];

        if($subject_id_selected){
            $subject = Subject::find($subject_id_selected);
            $subjects[] = $subject;
        }
        else{
            $subjects = $this->subjects;
        }

        if(count($subjects) > 0){
            foreach ($subjects as $subject) {
                $averagesTab = $this->getClasseRank($subject->id, $semestre, $school_year, $takeBonus, $takeSanctions);
                $bestGirl = null;
                $weakGirl = null;
                $girlFaileds_percentage = 0;
                $girlFaileds_number = 0;

                $girlSucceeds_number = 0;
                $girlSucceeds_percentage = 0;

                $girl_moy = 0;
                $girl_moys = [];
                
                $weakBoy = null;
                $bestBoy = null;
                $boyFaileds_number = 0;
                $boySucceeds_number = 0;
                $boySucceeds_percentage = 0;
                $boyFaileds_percentage = 0;
                $boyFaileds_number = 0;
                $boyFaileds_percentage = 0;

                $boy_moy = 0;
                $boy_moys = [];

                $classe_moy = 0;


                $totalFaileds_number = 0;
                $totalFaileds_percentage = 0;
                $totalSucceeds_number = 0;
                $totalSucceeds_percentage = 0;

                $stats = [];

                $girls = [];
                $boys = [];

                $total_boys = 0;
                $total_girls = 0;
                $total_pupils = 0;


                $failed_girls = [];
                $succeed_girls = [];

                $failed_boys = [];
                $succeed_boys = [];

                if(count($averagesTab) > 0){
                    foreach ($averagesTab as $pupil_id => $average) {
                        
                        if($average['pupil']->sexe == 'male'){
                            $boys[$pupil_id] = $average;
                        }
                        else{
                            $girls[$pupil_id] = $average;
                        }
                    }

                }



                if($girls !== []){
                    $max_girl = 0;
                    $min_girl = 20;
                    

                    foreach ($girls as $girl_id => $girl) {
                        $moy = $girl['moy'];
                        $girl_moys[] = $moy;

                        if($moy >= $max_girl){
                            $max_girl = $moy;
                            $bestGirl = $girl;
                        }

                        if ($moy <= $min_girl) {
                            $min_girl = $moy;
                            $weakGirl = $girl;
                        }

                        if($moy >= 10){
                            $succeed_girls[$girl_id] = $girl;
                        }
                        else{
                            $failed_girls[$girl_id] = $girl;
                        }


                        $girlSucceeds_number = count($succeed_girls);
                        $girlSucceeds_percentage = floatval(number_format((count($succeed_girls) / count($girls)) * 100, 2));

                        $girlFaileds_number = count($failed_girls);
                        $girlFaileds_percentage = floatval(number_format((count($failed_girls) / count($girls)) * 100, 2));
                    }


                }

                if($boys !== []){
                    $max_boy = 0;
                    $min_boy = 20;
                    foreach ($boys as $boy_id => $boy) {
                        $moy = $boy['moy'];
                        $boy_moys[] = $moy;

                        if($moy >= $max_boy){
                            $max_boy = $moy;
                            $bestBoy = $boy;
                        }

                        if ($moy <= $min_boy) {
                            $min_boy = $moy;
                            $weakBoy = $boy;
                        }

                        if($moy >= 10){
                            $succeed_boys[$boy_id] = $boy;
                        }
                        else{
                            $failed_boys[$boy_id] = $boy;
                        }


                        $boySucceeds_number = count($succeed_boys);
                        $boySucceeds_percentage = floatval(number_format((count($succeed_boys) / count($boys)) * 100, 2));

                        $boyFaileds_number = count($failed_boys);
                        $boyFaileds_percentage = floatval(number_format((count($failed_boys) / count($boys)) * 100, 2));
                        
                    }

                }

                if($boy_moys !== []){
                    $boy_moy = floatval(number_format((array_sum($boy_moys) / count($boy_moys)), 2));
                } 

                if($girl_moys !== []){
                    $girl_moy = floatval(number_format((array_sum($girl_moys) / count($girl_moys)), 2));
                }

                $moy_size = count($girl_moys) + count($boy_moys);


                if($moy_size > 0){
                    $moys = 0;
                    if(count($girl_moys) > 0){
                        $moys = $moys + array_sum($girl_moys);
                    }

                    if(count($boy_moys) > 0){
                        $moys = $moys + array_sum($boy_moys);
                    }

                    $classe_moy = floatval(number_format($moys / $moy_size , 2));
                }



                $total_boys = count($boys);
                $total_girls = count($girls);

                $total_pupils = $total_boys + $total_girls;



                $totalFaileds_number = $girlFaileds_number + $boyFaileds_number;
                $totalSucceeds_number = $girlSucceeds_number + $boySucceeds_number;

                $total = $totalFaileds_number + $totalSucceeds_number;

                if($total !== 0){
                    $totalSucceeds_percentage = floatval(number_format(($totalSucceeds_number / $total) * 100, 2));
                    $totalFaileds_percentage = floatval(number_format(($totalFaileds_number / $total) * 100, 2));
                }

                $data[$subject->id] = [
                    'subject_name' => $subject->name,
                    'bestBoy' => $bestBoy,
                    'weakBoy' => $weakBoy,
                    'bestGirl' => $bestGirl,
                    'weakGirl' => $weakGirl,

                    'stats' => [
                        'succeed' => [
                            'G' => [
                                'percentage' => $boySucceeds_percentage,
                                'number' => $boySucceeds_number,
                            ],

                            'F' => [
                                'percentage' => $girlSucceeds_percentage,
                                'number' => $girlSucceeds_number,
                            ],
                            'T' => [
                                'percentage' => $totalSucceeds_percentage,
                                'number' => $totalSucceeds_number,
                            ],
                        ],

                        'failed' => [
                            'G' => [
                                'percentage' => $boyFaileds_percentage,
                                'number' => $boyFaileds_number,
                            ],
                            'F' => [
                                'percentage' => $girlFaileds_percentage,
                                'number' => $girlFaileds_number,
                            ],
                            'T' => [
                                'percentage' => $totalFaileds_percentage,
                                'number' => $totalFaileds_number,
                            ],
                        ],
                    ],

                    'moyenne' => [
                        'girl_moy' => $girl_moy,
                        'boy_moy' => $boy_moy,
                        'classe_moy' => $classe_moy
                    ],

                    'effectif' =>[
                        'G' => $total_boys,
                        'F' => $total_girls,
                        'T' => $total_pupils,

                    ],

                ];

                
            }
            
        }

        return $data;
        
    }







    public function getMarksTypeLenght($subject_id, $semestre = 1, $school_year = null, $type = 'epe')
    {
        $max = 0;
        $school_year_model = $this->getSchoolYear($school_year);

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



    public function deleteClasseAbsences($semestre = null, $school_year = null, $subject_id = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $pupils = $this->getClassePupils($school_year_model->id);

        if($pupils){

            foreach ($pupils as $p) {

                $p->deletePupilAbsences($semestre, $school_year_model->id, $subject_id);
            }
        }
    }


    public function deleteClasseLates($semestre = null, $school_year = null, $subject_id = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $pupils = $this->getClassePupils($school_year_model->id);

        if($pupils){

            foreach ($pupils as $p) {
                
                $p->deletePupilLates($semestre, $school_year_model->id, $subject_id);
            }
        }
    }


    public function deleteClasseMarks($semestre = null, $school_year = null, $subject_id, $type = null)
    {
        DB::transaction(function($e) use($school_year, $semestre, $subject_id, $type){

            $school_year_model = $this->getSchoolYear($school_year);

            $classe_id = $this->id;

            if($subject_id && $type && $semestre){

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
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
                                  ->where('marks.classe_id', $classe_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->where('marks.semestre', $semestre)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id && $type){

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($semestre && $type){

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
                                  ->where('marks.semestre', $semestre)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id){

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
                                  ->where('marks.subject_id', $subject_id)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }

            elseif($type){

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
                                  ->where('marks.type', $type)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($semestre){

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
                                  ->where('marks.semestre', $semestre)
                                  ->each(function($mark){
                                        $school_year_model->marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            else{

                $school_year_model->marks()
                                  ->where('marks.classe_id', $classe_id)
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


    public function deleteClasseRelatedMarks($semestre = null, $school_year = null, $subject_id = null)
    {
        DB::transaction(function($e) use($school_year, $semestre, $subject_id){

            $school_year_model = $this->getSchoolYear($school_year);

            $classe_id = $this->id;

            if($subject_id && $semestre){

                $school_year_model->related_marks()
                                  ->where('related_marks.classe_id', $classe_id)
                                  ->where('related_marks.subject_id', $subject_id)
                                  ->where('related_marks.semestre', $semestre)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($subject_id){

                $school_year_model->related_marks()
                                  ->where('related_marks.classe_id', $classe_id)
                                  ->where('related_marks.subject_id', $subject_id)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            elseif($semestre){

                $school_year_model->related_marks()
                                  ->where('related_marks.classe_id', $classe_id)
                                  ->where('related_marks.semestre', $semestre)
                                  ->each(function($mark){

                                        $school_year_model->related_marks()->detach($mark->id);

                                    $mark->forceDelete();
                                });

            }
            else{

                $school_year_model->related_marks()
                                  ->where('related_marks.classe_id', $classe_id)
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


    public function getClasseMarksIndexes($subject_id, $semestre = 1, $school_year = null, $type = 'epe')
    {
        $indexes = [];
        $school_year_model = $this->getSchoolYear($school_year);
        if($school_year_model){
            $marks = $school_year_model->marks()->where('marks.subject_id', $subject_id)->where('semestre', $semestre)->where('marks.classe_id', $this->id)->where('type', $type)->pluck('mark_index')->toArray();
            if($marks && count($marks) > 0){
                $indexes = array_unique($marks);
            }
            
        }
        return $indexes;
    }


    public function generateClassesSecurity($secure_column, $teacher_id = null, $subject_id = null, $duration = 48)
    {
        $school_year_model = $this->getSchoolYear();
        
        if($teacher_id){
            $already_secure = $this->classeWasNotSecureColumn($teacher_id, $secure_column);
            if($already_secure){
                $secure = ClassesSecurity::create([
                    $secure_column => true,
                    'duration' => $duration,
                    'level_id' => $this->level_id,
                    'classe_id' => $this->id,
                    'teacher_id' => $teacher_id,
                    'subject_id' => $subject_id,
                    'school_year_id' => $school_year_model->id
                ]);
                return $secure;
            }
            return null;
        }
        else{
            $already_secure = $this->hasSecurities(null, $secure_column);
            if($already_secure){
                $secure = ClassesSecurity::create([
                    $secure_column => true,
                    'duration' => $duration,
                    'level_id' => $this->level_id,
                    'classe_id' => $this->id,
                    'school_year_id' => $school_year_model->id
                ]);
                return $secure;
            }
            return null;

        }
    }


    public function lockClasseUpdatedMarks($teacher_id = null, $subject_id = null, $duration = 48)
    {
        $school_year_model = $this->getSchoolYear();
        
        if($teacher_id){
            $already_secure = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->where('teacher_id', $teacher_id)->count();

            if($already_secure == 0){
                $secure = ClassesSecurity::create([
                    'locked_marks_updating' => true,
                    'duration' => $duration,
                    'level_id' => $this->level_id,
                    'classe_id' => $this->id,
                    'teacher_id' => $teacher_id,
                    'subject_id' => $subject_id,
                    'school_year_id' => $school_year_model->id
                ]);
                return $secure;
            }
            return null;
        }
        else{
            $already_secure = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->whereNull('teacher_id')->count();

            if($already_secure == 0){
                $secure = ClassesSecurity::create([
                    'locked_marks_updating' => true,
                    'duration' => $duration,
                    'level_id' => $this->level_id,
                    'classe_id' => $this->id,
                    'school_year_id' => $school_year_model->id
                ]);
                return $secure;
            }
            return null;

        }
    }



    public function hasSecurities($school_year = null, $secure_column = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($secure_column){
            return $this->securities()->where('school_year_id', $school_year_model->id)->where($secure_column, true)->whereNull('teacher_id')->count() > 0;
        }
        $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->whereNull('teacher_id')->count();
        $req2 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->whereNull('teacher_id')->count();
        $req3 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_classe', true)->whereNull('teacher_id')->count();
        $req4 = $this->securities()->where('school_year_id', $school_year_model->id)->where('closed_classe', true)->whereNull('teacher_id')->count();
        $req5 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_marks_updating', true)->whereNull('teacher_id')->count();

        return $req1 !== 0 || $req2 !== 0 || $req2 !== 0 || $req4 !== 0 || $req5 !== 0;

    }

    public function classeNotSecureFor($teacher_id, $protection = 'closed', $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);
        $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('teacher_id', $teacher_id)->where($protection . '_classe', true)->count();
        $req2 = $this->securities()->where('school_year_id', $school_year_model->id)->where('teacher_id', $teacher_id)->where($protection, true)->count();
        $req3 = $school_year_model->securities()->where('teacher_id', $teacher_id)->where($protection, true)->count();

        return $req1 == 0 && $req2 == 0 && $req3 == 0;
    }


    public function classeWasNotSecureColumn($teacher_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);
        if($secure_column){
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('teacher_id', $teacher_id)->where($secure_column, true)->count();
            return $req1 == 0;
        }
        else{
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->where('teacher_id', $teacher_id)->count();
            $req2 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->where('teacher_id', $teacher_id)->count();
            $req3 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_classe', true)->where('teacher_id', $teacher_id)->count();
            $req4 = $this->securities()->where('school_year_id', $school_year_model->id)->where('closed_classe', true)->where('teacher_id', $teacher_id)->count();
            if($req1 == 0 && $req2 == 0 && $req3 == 0 && $req4 == 0){
                return true;
            }
            return false;

        }
    }





    public function classeWasNotSecureForTeacher($teacher_id, $secure_column = null)
    {
        $school_year_model = $this->getSchoolYear();
        $classe_id = $this->id;
        $teacher = $school_year_model->teachers()->where('teachers.id', $teacher_id)->first();
        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

        if($classe && $teacher){
            $teacher_classes = auth()->user()->teacher->getTeachersCurrentClasses();

            if(array_key_exists($classe->id, $teacher_classes)){
                if(!$classe->hasSecurities()){
                    if($classe->classeWasNotSecureColumn($teacher->id)){
                        return true;
                    }
                    else{
                        return false;
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
        else{
            return false;

        }
    }

    public function classeWasNotClosedForTeacher($teacher_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);
        if($secure_column){
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('teacher_id', $teacher_id)->where($secure_column, true)->count();
            return $req1 == 0;
        }
        else{
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('closed', true)->where('teacher_id', $teacher_id)->count();
            $req2 = $this->securities()->where('school_year_id', $school_year_model->id)->where('closed_classe', true)->where('teacher_id', $teacher_id)->count();
            if($req1 == 0 && $req2 == 0){
                return true;
            }
            return false;

        }
    }


    public function classeWasNotLockedForTeacher($teacher_id, $secure_column = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);
        if($secure_column){
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('teacher_id', $teacher_id)->where($secure_column, true)->count();
            return $req1 == 0;
        }
        else{
            $req1 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked', true)->where('teacher_id', $teacher_id)->count();
            $req2 = $this->securities()->where('school_year_id', $school_year_model->id)->where('locked_classe', true)->where('teacher_id', $teacher_id)->count();
            if($req1 == 0 && $req2 == 0){
                return true;
            }
            return false;

        }
    }




    public function getTimePlan($day, $start, $end, $subject_id = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        if($subject_id){

            return $this->timePlans()
                        ->where('time_plans.school_year_id', $school_year_model->id)
                        ->where('time_plans.day', $day)
                        ->where('time_plans.subject_id', $subject_id)
                        ->where('time_plans.start', '<=', $start)
                        ->where('time_plans.end', '>=', $end)
                        ->first();

        }

        return $this->timePlans()
                    ->where('time_plans.school_year_id', $school_year_model->id)
                    ->where('time_plans.day', $day)
                    ->where('time_plans.start', '<=', $start)
                    ->where('time_plans.end', '>=', $end)
                    ->first();
    }


    public function getclasseTimePlans($subject_id = null, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        return $this->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->get();
    } 


    public function classeHasTimePlans($school_year = null)
    {
        return count($this->getclasseTimePlans($school_year)) > 0;
    }


    public function getTimePlanSubject($day, $start, $end, $subject_id = null, $school_year = null)
    {
        $time_plan = $this->getTimePlan($day, $start, $end, $subject_id, $school_year);

        return $time_plan ? $time_plan->subject->getSimpleName() : ' - ';
            
    }


    /**
     * Use to delete all data or tables joined to a specific Classe::class
     */
    public function classeDeleter($school_year_id = null)
    {
        if(!$school_year_id){

            $school_year_model = $this->getSchoolYear();
        }
        else{
            $school_year_model = SchoolYear::find($school_year_id);
        }

        $classe = $this;

        $classe_id = $classe->id;

        DB::transaction(function($e) use($classe, $classe_id, $school_year_model){
            $a = 1;
            $times_plans = $classe->timePlans()->where('school_year_id', $school_year_model->id)->get();
            
            $teachers = $classe->getClasseCurrentTeachers();

            $principal = $classe->currentPrincipal();

            $average_modalities = $classe->averageModalities()->where('school_year', $school_year_model->school_year)->get();

            $pupils = $classe->getPupils($school_year_model->id);

            if(count($teachers) > 0){

                $teachers_ids = [];

                if($principal){

                    $principal->delete();
                }
                $classe->teacherCursus()->where('teacher_cursuses.school_year_id', $school_year_model->id)->each(function($cursus) use ($teachers_ids){

                    $teachers_ids[] = $cursus->teacher_id;

                    $cursus->delete();

                });

                if(count($teachers_ids) > 0){

                    foreach($teachers_ids as $teacher_id){

                        $teacher = Teacher::find($teacher_id);

                        $teacher->lates()->where('teacher_lates.school_year_id', $school_year_model->id)->each(function($late){

                            $late->delete();

                        });
                        $teacher->absences()->where('teacher_absences.school_year_id', $school_year_model->id)->each(function($abs){

                            $abs->delete();
                        });
                    }
                }

            }


            if(count($times_plans) > 0){

                foreach($times_plans as $t){

                    $t->delete();
                }
            }

            if(count($average_modalities) > 0){

                foreach($average_modalities as $av){

                    $av->delete();
                }
            }

            if(count($pupils) > 0){

                foreach($pupils as $pupil){

                    $pupil->pupilDeleter($school_year_model->id, false);
                    
                }

                $responsibles = $classe->currentRespo();

                $responsibles ? $responsibles->delete() : $a = null;
            }


            $school_year_model->classes()->detach($classe_id);

        });
        
    }


    public function canPushThisDurationTo($subject_id, $duration, $school_year = null, $except = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $quotaModel = $this->hasQuota($subject_id, $school_year_model->id);

        $total_duration = 0;

        if($quotaModel){

            $according_quota = $quotaModel->quota;

            if($except){

                $times_plans = $this->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->where('time_plans.subject_id', $subject_id)->where('time_plans.id','<>', $except)->pluck('duration')->toArray();

            }
            else{
                $times_plans = $this->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->where('time_plans.subject_id', $subject_id)->pluck('duration')->toArray();

            }
            if($times_plans){

                $total_duration = array_sum($times_plans);

                return $according_quota >= ($total_duration + $duration);
            }
            else{

                return true;
            }
        }
        else{

            return false;
        }

        

        





    }

}