<?php

namespace App\Http\Livewire;

use Carbon\Carbon;
use App\Models\Pupil;
use App\Models\PupilAbsences;
use App\Models\PupilLates;
use App\Models\School;
use App\Models\Subject;
use Livewire\Component;
use App\Models\SchoolYear;

class ClassePresenceAbsence extends Component
{
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'selectedClasseSubjectChangeLiveEvent' => 'reloadSelectedSubject',
        'semestreWasChanged',
    ];
    
    
    public $classe_id;

    public $counter = 0;
    public $activeData = [];
    public $makePresence = false;
    public $horaire;
    public $start;
    public $duration;
    public $coming_hour;
    public $coming_hour_H;
    public $coming_hour_M;
    public $date;
    public $end;
    public $school_year_model;
    public $school_year;
    public $classe_subject_selected;
    public $semestre_selected = 1;


    public function render()
    {
        $subject_selected = null;
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();

        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        $subjects = [];
        $pupils = [];

        if($classe){
            $subject_selected = $classe->subjects()->first();
            $subjects = $classe->subjects;
            $pupils = $classe->getPupils($school_year_model->id);
        }

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
            $subject_id = intval(session('classe_subject_selected'));
            if($subjects){
                if(in_array($subject_id, $subjects->pluck('id')->toArray())){
                    session()->put('classe_subject_selected', $subject_id);
                    $this->classe_subject_selected = $subject_id;
                    $subject_selected = Subject::find($subject_id);
                }
            }
            else{
                $this->classe_subject_selected = null;
                session()->forget('classe_subject_selected');
            }
        }
        else{
            if($subject_selected){
                session()->put('classe_subject_selected', $subject_selected->id);
            }
        }

        return view('livewire.classe-presence-absence', compact('classe', 'pupils', 'subjects', 'subject_selected'));
    }

    public function editClasseSubjects($classe_id = null)
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $classe = $school_year_model->classes()->where('id', $this->classe_id)->first();
        if($classe){
            $this->emit('manageClasseSubjectsLiveEvent', $classe->id);
            $this->dispatchBrowserEvent('modal-manageClasseSubjects');
        }

    }

    public function throwPresence($classe_id)
    {
        $this->emit('makeClassePresence', $classe_id);
    }

    public function cancelPresence()
    {
        $this->reset('makePresence', 'school_year_model', 'horaire', 'start', 'end', 'coming_hour', 'duration');
    }



    public function cancelLate($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $yet = $pupil->wasLateThisDayFor($this->date, $this->school_year_model->id, $this->semestre_selected, $this->classe_subject_selected);
            if($yet){
                $yet->delete();
            }
        }
        $this->makePresence = true;
    }

    public function semestreWasChanged($semestre_selected = null)
    {
        session()->put('semestre_selected', $semestre_selected);
        $this->semestre_selected = $semestre_selected;
    }


    public function reloadSelectedSubject($subject_selected = null)
    {
        session()->put('classe_subject_selected', $subject_selected);
        $this->classe_subject_selected = $subject_selected;
    }
    
    public function updatedDate($date)
    {
        $this->date = $date;
    }
    public function updatedStart($start)
    {
        $this->end = $start + 1;
    }
    public function updatedComingHourM()
    {
        $this->duration =  $this->coming_hour_M;
    }
    
    public function reloadClasseData($school_year = null)
    {
        $this->counter = 1;
    }
}
