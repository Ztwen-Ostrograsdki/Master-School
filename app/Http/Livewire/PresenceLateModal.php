<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use Illuminate\Support\Carbon;
use Livewire\Component;

class PresenceLateModal extends Component
{

    use ModelQueryTrait;

    protected $listeners = ['makeClassePresence' => 'openModal'];

    public $data = [];

    public $school_year_model;

    public $coming_hour_H;
    
    public $coming_hour_M;

    public $classe_id;

    public $classe;

    public $pupil_id;

    public $public;

    public $motif = 'Sans motif';

    public $object = 'absent';

    public $subject_id;

    public $start;

    public $end;


    public function render()
    {

        $pupils = [];

        $semestre_type = 'Semestre';

        $semestres = $this->getSemestres();

        if(count($semestres) > 2){

            $semestre_type = 'Trimestre';

        }



        if($this->classe){

            $pupils = $this->classe->getPupils($this->school_year_model->id);



        }

        return view('livewire.presence-late-modal', compact('pupils', 'semestres', 'semestre_type'));
    }


    public function openModal($classe_id)
    {
        $classe = Classe::find($classe_id);

        if($classe){

            $this->classe = $classe;

            $this->classe_id = $classe->id;

            $this->semestre_selected = session('semestre_selected');

            $this->school_year_model = $this->getSchoolYear();

            $this->date = (new \DateTime(Carbon::today()))->format('Y-m-d');

            $this->start = intval(date('H')) + 1;

            $this->end = intval(date('H')) + 2;

            $this->coming_hour_H = intval(date('H')) + 1;

            $this->coming_hour_M = intval(date('i'));

            $this->duration =  $this->coming_hour_M;

            $this->motif = 'Sans motif';

            $this->dispatchBrowserEvent('modal-makeClassePresenceLate');




        }


        

    }

    public function cancelPresence()
    {
        $this->reset('makePresence', 'school_year_model', 'horaire', 'start', 'end', 'coming_hour', 'duration');
    }


    public function absent($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil && !$pupil->isAbsentThisDay($this->date, $this->school_year_model->id, $this->semestre_selected, $this->classe_subject_selected)){
            $m = PupilAbsences::create([
                'horaire' => $this->start . 'H - ' . $this->end . 'H',
                'motif' => $this->motif,
                'date' => $this->date,
                'school_year_id' => $this->school_year_model->id,
                'school_year' => $this->school_year_model->school_year,
                'pupil_id' => $pupil->id,
                'classe_id' => $pupil->classe_id,
                'semestre' => $this->semestre_selected,
                'subject_id' => $this->classe_subject_selected,
            ]);
        }
        $this->makePresence = true;
    }

    public function cancelAbsence($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $yet = $pupil->isAbsentThisDay($this->date, $this->school_year_model->id, $this->semestre_selected, $this->classe_subject_selected);
            if($yet){
                $yet->delete();
            }
        }
        $this->makePresence = true;

    }


    public function late($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        if($pupil){
            $yet = $pupil->wasLateThisDayFor($this->date, $this->school_year_model->id, $this->semestre_selected, $this->classe_subject_selected);
            if(!$yet){
                $this->coming_hour = $this->coming_hour_H . ':' . $this->coming_hour_M . ':' . 00;
                PupilLates::create([
                    'horaire' => $this->start . 'H - ' . $this->end . 'H',
                    'motif' => $this->motif,
                    'date' => $this->date,
                    'duration' => $this->duration,
                    'coming_hour' => $this->coming_hour,
                    'school_year_id' => $this->school_year_model->id,
                    'school_year' => $this->school_year_model->school_year,
                    'pupil_id' => $pupil->id,
                    'semestre' => $this->semestre_selected,
                    'subject_id' => $this->classe_subject_selected,
                    'classe_id' => $pupil->classe_id,
                ]);
            }
        }
        $this->makePresence = true;
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
}
