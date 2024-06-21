<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use Livewire\Component;

class PupilMarksListingForParent extends Component
{

    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadPupilData',
        'classePupilListUpdated' => 'reloadPupilData',
        'MarksStoppingDispatchedLiveEvent' => 'reloadPupilData',
        'ReloadSemestreData' => 'reloadPupilData',
        'ReloadSchoolYearData' => 'reloadPupilData',
        'SchoolYearWasChanged' => 'schoolYearWasChanged',
        'pupilUpdated' => 'reloadPupilData',
    ];


    public $pupil_id;

    public $educmaster;

    public $school_year;

    public $semestre_selected = 1;

    public $school_year_selected;

    public $edit_mark_value = 0;

    public $edit_mark_type = 'epe';

    public $editing_mark = false;

    public $subject_selected;

    public $invalid_mark = false;

    public $display_annual_data = false;

    public $edit_key;

    public $mark_key;

    public $targetedMark;

    public $count = 0;

    public function mount($id)
    {

        if($id){

            $this->pupil_id = $id;

            // $this->educmaster = $educmaster;

            $this->pupil = Pupil::find($id);
        }

        if(session()->has('school_year_selected_for_parent') && session('school_year_selected_for_parent') !== null){

            $this->school_year_selected = session('school_year_selected_for_parent');

        }
    }


    public function render()
    {
        $pupil = null;

        $effectif = 0;
        
        $marks = null;

        $school_years = [];

        $pupil_school_years = [];
        
        $pupil_id = $this->pupil_id;

        if(session()->has('school_year_selected_for_parent') && session('school_year_selected_for_parent') !== null){

            $this->school_year_selected = session('school_year_selected_for_parent');

        }

        $school_year_selected = $this->school_year_selected;

        $school_year_model = $this->getSchoolYear($school_year_selected);

        $devMaxLenght = 2;
        
        $epeMaxLenght = 2;
        
        $participMaxLenght = 1;
        
        $noMarks = false;
        
        $averageEPETabs = [];
        
        $averageTabs = [];
        
        $ranksTabs = [];
        
        $classeCoefTabs = [];
        
        $semestrialAverages = [];
        
        $annualAverage = null;
        
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


        if(session()->has('semestre_selected_for_parent') && session('semestre_selected_for_parent')){

            $semestre = intval(session('semestre_selected_for_parent'));

            session()->put('semestre_selected_for_parent', $semestre);

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

                    $effectif = $classe->getEffectif();

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
                    foreach($semestres as $sm){

                        $semestrialAverages[$sm] = $pupil->average($classe->id, $sm, $school_year_model->id);

                    }


                    $annualAverage = $pupil->annual_average($classe->id, $school_year_model->id);

                }


                $devMaxLenght = $pupil->getMarksTypeLenght(null, $semestre, $school_year_model->school_year, 'devoir') + 1;

                $epeMaxLenght = $pupil->getMarksTypeLenght(null, $semestre, $school_year_model->school_year, 'epe') + 1;

                $participMaxLenght = 1;

                $pupil_school_years = $pupil->school_years()->pluck('school_years.id')->toArray();

            }

            if(($epeMaxLenght < 1 && $participMaxLenght < 1 && $devMaxLenght < 1)){
                $noMarks = true;
            }

            if($epeMaxLenght < 2){
                $epeMaxLenght = 2;
            }
            
            if($devMaxLenght < 2){
                $devMaxLenght = 2;
            }
            if(!($epeMaxLenght && $devMaxLenght && $participMaxLenght)){
                $noMarks = true;
            }

            $school_years = SchoolYear::orderBy('school_year', 'desc')->get();
                
        }
        else{
            return abort(404);
        }


        return view('livewire.pupil-marks-listing-for-parent', compact('pupil', 'marks', 'epeMaxLenght', 'devMaxLenght', 'participMaxLenght', 'noMarks', 'classeCoefTabs', 'averageEPETabs', 'averageTabs', 'ranksTabs', 'school_year_model', 'semestre_type', 'semestres', 'semestrialAverages', 'annualAverage', 'classe', 'effectif', 'school_years', 'pupil_school_years'));
    }

   

    public function updatedSemestreSelected($semestre)
    {
        $this->semestre_selected = $semestre;

        session()->put('semestre_selected_for_parent', $semestre);

        $this->emit('ReloadSemestreData', $semestre);

    }

    public function updatedSchoolYearSelected($school_year)
    {
        $this->school_year_selected = $school_year;

        session()->put('school_year_selected_for_parent', $school_year);

        $this->emit('SchoolYearWasChanged', $school_year);

    }

    public function schoolYearWasChanged($school_year)
    {
        $this->school_year_selected = $school_year;

        session()->put('school_year_selected_for_parent', $school_year);

        $this->emit('ReloadSchoolYearData', $school_year);
    }



    public function reloadPupilData($school_year = null)
    {
        $this->counter = 1;
    }

    
}
