<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use Livewire\Component;

class PupilMarksListing extends Component
{

    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadPupilData',
        'classePupilListUpdated' => 'reloadPupilData',
        'semestreWasChanged',
        'pupilUpdated' => 'reloadPupilData',
    ];


    public $pupil_id;
    public $semestre_type = 'semestre';
    public $school_year;
    public $semestre_selected = 1;
    public $edit_mark_value = 0;
    public $edit_mark_type = 'epe';
    public $editing_mark = false;
    public $subject_selected;
    public $invalid_mark = false;
    public $edit_key;
    public $mark_key;
    public $targetedMark;
    public $count = 0;


    public function render()
    {
        $pupil = null;
        $marks = null;
        $pupil_id = $this->pupil_id;
        $school_year_model = $this->getSchoolYear();
        $devMaxLenght = 2;
        $epeMaxLenght = 2;
        $participMaxLenght = 2;
        $noMarks = false;
        $averageEPETabs = [];
        $averageTabs = [];
        $ranksTabs = [];
        $classeCoefTabs = [];

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }
        else{
            $semestre = $this->semestre_selected;

        }

        if ($pupil_id) {
            $pupil = Pupil::find($pupil_id);
            if($pupil){

                $marks = $pupil->getMarks(null, $semestre, $school_year_model->school_year);
                $classe = $pupil->getCurrentClasse($school_year_model->id);

                if($classe){
                    $subjects = $classe->subjects;

                    if($subjects){
                        foreach ($subjects as $subject) {
                            $averageEPETabs[$subject->id] = $classe->getMarksAverage($subject->id, $this->semestre_selected, $school_year_model->school_year, 'epe')[$pupil->id];

                            $averageTabs[$subject->id] = $classe->getAverage($subject->id, $this->semestre_selected, $school_year_model->school_year)[$pupil->id];

                            $ranks = $classe->getClasseRank($subject->id, $this->semestre_selected, $school_year_model->school_year);
                            
                            if(isset($ranks[$pupil->id])){
                                $ranksTabs[$subject->id] = $ranks[$pupil->id];
                            }
                            else{
                                $ranksTabs[$subject->id] = null;
                            }

                            $classeCoefTabs[$subject->id] = $classe->get_coefs($subject->id, $school_year_model->id, true);
                        }

                    }
                }



                


                

                $devMaxLenght = $pupil->getMarksTypeLenght(null, $semestre, $school_year_model->school_year, 'devoir') + 1;

                $epeMaxLenght = $pupil->getMarksTypeLenght(null, $semestre, $school_year_model->school_year, 'epe') + 1;
                $participMaxLenght = $pupil->getMarksTypeLenght(null, $semestre, $school_year_model->school_year, 'participation') + 1;

            }

            if(($epeMaxLenght < 1 && $participMaxLenght < 1 && $devMaxLenght < 1)){
                $noMarks = true;
            }

            if($epeMaxLenght < 2){
                $epeMaxLenght = 2;
            }
            if($participMaxLenght < 2){
                $participMaxLenght = 2;
            }
            if($devMaxLenght < 2){
                $devMaxLenght = 2;
            }
            if(!($epeMaxLenght && $devMaxLenght && $participMaxLenght)){
                $noMarks = true;
            }
                
        }
        else{
            return abort(404);
        }


        return view('livewire.pupil-marks-listing', compact('pupil', 'marks', 'epeMaxLenght', 'devMaxLenght', 'participMaxLenght', 'noMarks', 'classeCoefTabs', 'averageEPETabs', 'averageTabs', 'ranksTabs'));
    }

    public function addNewPupil()
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $classe = $school_year_model->classes()->first();
        if($classe){
            $this->emit('addNewPupilToClasseLiveEvent', $classe->id);
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
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
