<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\SchoolYear;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\DB;

class JobProccessingSettingsOnMarks implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $user;

    public $classe;

    public $school_year_model;

    public $data = [];

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(User $user, Classe $classe, SchoolYear $school_year_model, array $data = [])
    {
        $this->user = $user;

        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->data = $data;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $this->doJob();
    }


    public function doJob()
    {
        $data = $this->data;

        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        DB::transaction(function($e) use($classe, $data, $school_year_model) {

            $marks = [];

            if($data != []){

                $subject = $data['subject'];

                $semestre = $data['semestre'];

                $type = $data['type'];

                $action = $data['action'];

                $index = $data['index'];

                $start = null;

                $end = null;

                if($subject && $semestre && $type){

                    if($end && $start){

                    }
                    else{

                        if($subject == 'all' && $type == 'all' && $index == 'all'){

                            $marks = $classe->marks()
                                            ->where('marks.school_year_id', $school_year_model->id)
                                            ->where('marks.semestre', $semestre)
                                            ->get();

                        }
                        elseif($subject == 'all' && $index == 'all'){

                            $marks = $classe->marks()
                                            ->where('marks.school_year_id', $school_year_model->id)
                                            ->where('marks.semestre', $semestre)
                                            ->where('marks.type', $type)
                                            ->get();

                        }
                        elseif($subject == 'all' && $type == 'all'){

                            $subjects = $classe->subjects;

                            $subMarks = [];

                            foreach($subjects as $sub){

                                $subMarks[] = $this->getMarksOfAllTypeBySubject($classe, $index, $semestre, $school_year_model, $sub->id);

                            }

                            if($subMarks){

                                foreach($subMarks as $s_Marks){

                                    foreach($s_Marks as $sub_mark){

                                        $marks[] = $sub_mark;


                                    }

                                }

                            }

                        }
                        elseif($index == 'all' && $type == 'all'){

                            $marks = $this->getMarksOfAllTypeBySubject($classe, 'all', $semestre, $school_year_model, $subject->id);

                        }
                        else{
                            if($index == 'all'){
                                $marks = $classe->marks()->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.type', $type)
                                        ->where('marks.semestre', $semestre)
                                        ->where('marks.subject_id', $subject->id)
                                        ->get();

                            }
                            elseif($type == 'all'){

                                $marks = [];

                                if(is_object($subject)){

                                    $subject_id = $subject->id;

                                }
                                elseif(is_numeric($subject)){

                                    $subject_id = $subject;

                                }

                                
                                $marks = $this->getMarksOfAllTypeBySubject($classe, $index, $semestre, $school_year_model, $subject_id);

                            }
                            elseif($subject == 'all'){

                                $marks = [];

                                $marksAsArray = [];

                                $subjects = $classe->subjects;

                                foreach($subjects as $sub){

                                    $ind_sub = $this->getIndex($classe, $index, $semestre, $school_year_model->id, $sub->id, $type);

                                    $marksAsArray[] = $classe->marks()
                                        ->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.semestre', $semestre)
                                        ->where('marks.subject_id', $sub->id)
                                        ->where('marks.type', $type)
                                        ->where('marks.mark_index', $ind_sub)
                                        ->get();


                                }

                                if($marksAsArray !== []){

                                    foreach($marksAsArray as $marks_as_arr){

                                        foreach($marks_as_arr as $mark_s){

                                            $marks[] = $mark_s;

                                        }

                                    }

                                }
                            }
                            else{

                                $indd = $this->getIndex($classe, $index, $semestre, $school_year_model->id, $subject->id, $type);

                                $marks = $classe->marks()
                                        ->where('marks.school_year_id', $school_year_model->id)
                                        ->where('marks.semestre', $semestre)
                                        ->where('marks.type', $type)
                                        ->where('marks.mark_index', $indd)
                                        ->where('marks.subject_id', $subject->id)
                                        ->get();

                            }

                        }

                    }

                }

                if($marks !== []){

                    if($action){

                        if($action == 'delete'){

                            foreach($marks as $mark){

                                $mark->delete();

                            }

                        }
                        elseif($action == 'normalized'){

                            foreach($marks as $mark){

                                $mark->update(['forget' => false, 'forced_mark' => false, 'blocked' => false]);

                            }

                        }
                        elseif($action == 'forget'){

                            foreach($marks as $mark){

                                $mark->update(['forget' => true]);

                            }

                        }
                        elseif($action == 'must'){

                            foreach($marks as $mark){

                                $mark->update(['forced_mark' => true]);

                            }

                        }

                    }

                }
            }

        });
    }



    public function getIndex($classe, $index, $semestre, $school_year_id, $subject_id, $type)
    {

        if($index == 'all'){

            $index = 'all';

        }
        elseif($index == 'f'){

            $index = 1;

        }
        elseif($index == 'l'){

            if($type && $type !== 'all'){

                $mark = $classe->marks()
                            ->where('marks.school_year_id', $school_year_id)
                            ->where('marks.semestre', $semestre)
                            ->where('marks.subject_id', $subject_id)
                            ->where('marks.type', $type)
                            ->orderBy('marks.mark_index', 'desc')
                            ->first();

                if($mark){

                    $index = $mark->mark_index;

                }
            }
        }
        elseif(is_numeric($index)){

            $index = (int)$index;

        }

        return $index;

    }


    public function getMarksOfAllTypeBySubject($classe, $index, $semestre, $school_year_model, $subject_id)
    {
        $marks = [];

        if($index == 'all'){

            $epeMarks = $classe->marks()
                ->where('marks.school_year_id', $school_year_model->id)
                ->where('marks.semestre', $semestre)
                ->where('marks.subject_id', $subject_id)
                ->where('marks.type', 'epe')
                ->get();

            $devMarks = $classe->marks()
                    ->where('marks.school_year_id', $school_year_model->id)
                    ->where('marks.semestre', $semestre)
                    ->where('marks.subject_id', $subject_id)
                    ->where('marks.type', 'devoir')
                    ->get();

            $partMarks = $classe->marks()
                    ->where('marks.school_year_id', $school_year_model->id)
                    ->where('marks.semestre', $semestre)
                    ->where('marks.subject_id', $subject_id)
                    ->where('marks.type', 'participation')
                    ->get();


        }
        else{

            $epeInd = $this->getIndex($classe, $index, $semestre, $school_year_model->id, $subject_id, 'epe');

            $devInd = $this->getIndex($classe, $index, $semestre, $school_year_model->id, $subject_id, 'devoir');

            $partInd = $this->getIndex($classe, $index, $semestre, $school_year_model->id, $subject_id, 'participation');

            $epeMarks = $classe->marks()
                ->where('marks.school_year_id', $school_year_model->id)
                ->where('marks.semestre', $semestre)
                ->where('marks.subject_id', $subject_id)
                ->where('marks.type', 'epe')
                ->where('marks.mark_index', $epeInd)
                ->get();

            $devMarks = $classe->marks()
                    ->where('marks.school_year_id', $school_year_model->id)
                    ->where('marks.semestre', $semestre)
                    ->where('marks.subject_id', $subject_id)
                    ->where('marks.type', 'devoir')
                    ->where('marks.mark_index', $devInd)
                    ->get();

            $partMarks = $classe->marks()
                    ->where('marks.school_year_id', $school_year_model->id)
                    ->where('marks.semestre', $semestre)
                    ->where('marks.subject_id', $subject_id)
                    ->where('marks.type', 'participation')
                    ->where('marks.mark_index', $partInd)
                    ->get();


        }

        if($epeMarks && $epeMarks !== []){

            foreach($epeMarks as $em){

                $marks[] = $em;

            }

        }

        if($devMarks && $devMarks !== []){

            foreach($devMarks as $dm){

                $marks[] = $dm;

            }

        }

        if($partMarks && $partMarks !== []){

            foreach($partMarks as $pm){

                $marks[] = $pm;

            }

        }

        return $marks;

    }
}
