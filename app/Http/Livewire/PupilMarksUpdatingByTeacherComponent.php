<?php

namespace App\Http\Livewire;

use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Mark;
use Livewire\Component;

class PupilMarksUpdatingByTeacherComponent extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        "UpdatePupilsMarksUpdatingRequestsLiveEvent" => "reloadData",
    ];

    public $classe_id;

    public $counter = 0;

    public $selecteds = [];

    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $semestre = session('semestre_selected');

        $marks_requests = [];

        if($this->classe_id){

            $classe = $school_year_model->findClasse($this->classe_id);

            if($classe){

                $marks_requests = $classe->marks()->where('marks.updating', true)->where('marks.school_year_id', $school_year_model->id)->where('marks.semestre', $semestre)->get();

            }

        }
        
        return view('livewire.pupil-marks-updating-by-teacher-component', compact('classe', 'marks_requests', 'school_year_model'));
    }


   

    public function reloadData($data = null)
    {
        $time = now();

        $this->counter = $time;
    }


    public function authorized($mark_id)
    {
        $user = auth()->user();

        $mark = Mark::find($mark_id);

        if($mark){

            $selecteds = $this->selecteds;

            if(isset($selecteds[$mark_id])){

                unset($selecteds[$mark_id]);

            }

            $this->selecteds = $selecteds;

            $new_value = $mark->editing_value;

            if($new_value !== null){

                if($mark->update(['value' => $new_value])){

                    $mark->forceFill(['editing_value' => null, 'updating' => false])->save();

                    UpdateClasseAveragesIntoDatabaseEvent::dispatch($user, $mark->classe, $mark->semestre, $mark->school_year);

                    $this->reloadData();

                }
            }
        }
    }


    public function refused($mark_id)
    {
        $mark = Mark::find($mark_id);

        if($mark){

            $selecteds = $this->selecteds;

            if(isset($selecteds[$mark_id])){

                unset($selecteds[$mark_id]);

            }

            $this->selecteds = $selecteds;

            $mark->forceFill(['editing_value' => null, 'updating' => false])->save();

        }

        $this->reloadData();
    }

    public function delete($mark_id)
    {
        return $this->refused($mark_id);
    }


    public function pushToSelecteds($mark_id)
    {
        $selecteds = $this->selecteds;

        if(isset($selecteds[$mark_id])){

            unset($selecteds[$mark_id]);

        }
        else{

            $selecteds[$mark_id] = $mark_id;

        }

        $this->selecteds = $selecteds;
    }
}
