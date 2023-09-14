<?php

namespace App\Http\Livewire;

use App\Events\InitiateSettingsOnMarksEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MarksSettingsModal extends Component
{

    use ModelQueryTrait;

    protected $listeners = ['onMarksSettingsEvent' => 'openModal'];

    protected $rules = ['semestre_id' => 'required|int|min:1|max:3'];

    public $classe_id;

    public $classe;

    public $semestre_type = 'Semestre';

    public $semestre_id = 1;

    public $subject_id = 'all';

    public $subject = 'all';

    public $mark_index = 'l';

    public $mark_type = 'epe';

    public function render()
    {
        $subjects = [];

        $marks_indexes = [];

        $classes = [];

        $types_of_marks = [
            'devoir' => 'Devoirs',
            'epe' => 'Interrogations',
            'participation' => 'Participations'

        ];

        $semestres = $this->getSemestres();

        $school = School::first();

        if($school){

            if(count($semestres) == 3){

                $this->semestre_type = 'Trimestre';

                $semestres = [1, 2, 3];
            }

            if($this->classe){
                $marks_indexes = $this->classe->getClasseMarksIndexes($this->subject_selected, $this->semestre_id, null, $this->mark_type);
            }

            if($marks_indexes == []){

                $marks_indexes = [1, 2, 3, 4, 5, 6, 7, 8, 9, 10];
            }

        }

        $school_years = SchoolYear::all();

        $subjects = Subject::all();
        
        return view('livewire.marks-settings-modal', compact('semestres', 'school_years', 'types_of_marks', 'subjects', 'marks_indexes'));
    }

    public function submitMarks()
    {
        $this->close();
    }



    public function close()
    {
        $this->reset('classe_id', 'semestre_id', 'semestre_type', 'subject_id', 'mark_index', 'classe', 'subject');

        $this->dispatchBrowserEvent('hide-form');
    }

    public function toMustMarks()
    {
        $action = 'must';

        $this->processor($action);
    }

    public function toForgetMarks()
    {
        $action = 'forget';

        $this->processor($action);
    }

    public function deleteMarks()
    {
        $action = 'delete';

        $this->processor($action);
    }

    public function toNormalMarks()
    {
        $action = 'normalized';

        $this->processor($action);
    }


    public function processor($action)
    {
        $user = auth()->user();

        $this->validate();

        $school_year_model = $this->getSchoolYear();

        $data = ['action' => $action, 'subject' => $this->subject, 'type' => $this->mark_type, 'index' => $this->mark_index, 'semestre' => $this->semestre_id];

        InitiateSettingsOnMarksEvent::dispatch($user, $this->classe, $school_year_model, $data);

        // $this->doJob($data, $school_year_model);

        $this->close();
    }


    public function updatedSubjectId($subject_id = 'all')
    {
        if($subject_id && $subject_id !== 'all'){

            $this->subject = Subject::find($subject_id);

        }
        else{

            $this->subject = 'all';

        }
    }

    public function updatedMarkType($type)
    {
        $this->mark_type = $type;
    }



    public function openModal($classe_id = null, $semestre = null, $subject_id = null)
    {
        $this->classe_id = $classe_id;

        $this->semestre_id = $semestre;

        $this->subject_selected = $subject_id;

        $this->classe = Classe::find($classe_id);

        $this->dispatchBrowserEvent('modal-marksSettings');
    }


    public function updatedSubjectSelected($subject_id)
    {
        $this->subject_selected = $subject_id;
    }


    public function updatedSemestreId($semestre_id)
    {
        $this->semestre_id = $semestre_id;
    }

    public function doJob($data, $school_year_model)
    {
        $classe = $this->classe;

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
