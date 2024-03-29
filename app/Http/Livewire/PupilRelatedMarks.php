<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use App\Models\RelatedMark;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PupilRelatedMarks extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadPupilData',
        'classePupilListUpdated' => 'reloadPupilData',
        'pupilUpdated' => 'reloadPupilData',
        'ClasseDataLoadedSuccessfully' => 'reloadPupilData',
        'NewClasseMarksInsert' => 'reloadPupilData',
        'semestreWasChanged',
    ];

    public $slug;
    public $pupil_id;
    public $classe;
    public $counter = 0;
    public $semestre_type = 'semestre';
    public $school_year;
    public $semestre_selected = 1;


    public function render()
    {
        $school = School::find(1);
        $semestres = [1, 2];
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        $school_year_model = $this->getSchoolYear();
        $school_year = session('school_year_selected');

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }
        else{
            $this->semestre_selected = 1;
            session()->put('semestre_selected', $this->semestre_selected);
        }


        $pupil_id = $this->pupil_id;

        if($pupil_id){

            $pupil = Pupil::find($pupil_id);

            if($pupil){
                
                $related_marks = $school_year_model->related_marks()
                                                   ->where('pupil_id', $pupil_id)
                                                   ->where('semestre', $this->semestre_selected)
                                                   ->get();

            }
            else{
                $related_marks = [];
                $pupil = null;
            }
        }
        return view('livewire.pupil-related-marks', compact('pupil', 'related_marks'));
    }



    public function insertRelatedMark()
    {
        $subject_id = session('classe_subject_selected');

        $semestre = session('semestre_selected');

        $school_year_model = $this->getSchoolYear();

        if($subject_id){

            $this->emit('insertRelatedMarkLiveEvent', $this->pupil_id, $subject_id, $semestre, $school_year_model->id);
        }
        else{
            if(!$subject_id && $semestre){

                $user = auth()->user();

                if($user->teacher){

                    $subject_id = $user->teacher->speciality()->id;

                    $this->emit('insertRelatedMarkLiveEvent', $this->pupil_id, $subject_id, $semestre, $school_year_model->id);
                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir la matière et le semestre en premier!", 'type' => 'error']);

                }

            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE: AUCUN SEMESTRE/TRIMESTRE EN SESSION', 'message' => "Veuillez choisir le semestre/trimestre en premier!", 'type' => 'error']);

            }

        }
    }


    public function editRelatedMark($related_mark_id)
    {
        $this->emit('UpdatePupilRelatedMark', $related_mark_id);
    }

    public function delete($mark_id)
    {
        if($mark_id){

            $mark = RelatedMark::find($mark_id);

            if($mark){

                DB::transaction(function($e) use ($mark){

                    $school_year_model = $mark->school_year;

                    $detach = $school_year_model->related_marks()->detach($mark->id);

                    if($detach){

                        $m = $mark->delete();
                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Mise à jour échouée!", 'type' => 'error']);
                    }
                });
                
                DB::afterCommit(function(){

                    $this->emit('pupilUpdated');

                    $this->emit('classeUpdated');

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Mise à jour réussie avec succès!", 'type' => 'success']);

                });
            }
        }
    }



    public function reloadPupilData($school_year = null)
    {
        $this->counter = 1;
    }

    public function semestreWasChanged($semestre_selected = null)
    {
        session()->put('semestre_selected', $semestre_selected);
        $this->semestre_selected = $semestre_selected;
    }
}
