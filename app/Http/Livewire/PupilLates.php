<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Pupil;
use App\Models\PupilLates as Lates;
use App\Models\School;
use Livewire\Component;

class PupilLates extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadPupilData',
        'classePupilListUpdated' => 'reloadPupilData',
        'pupilUpdated' => 'reloadPupilData',
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
                $lates = $pupil->lates()
                               ->where('school_year_id', $school_year_model->id)
                               ->where('semestre', $semestre)
                               ->get();

            }
            else{
                $lates = [];
                $pupil = null;
            }
        }
        return view('livewire.pupil-lates', compact('pupil', 'lates'));
    }


    public function justified($late_id)
    {
        if($late_id){
            $late = Lates::find($late_id);
            if($late){
                $m = $late->update(['justified' => true, 'motif' => 'Justifié!']);
                if($m){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Mise à jour réussie avec succès!", 'type' => 'success']);
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Mise à jour échouée!", 'type' => 'error']);

                }
                $this->emit('pupilUpdated', $late->pupil_id);
            }
        }
    }

    public function unjustified($late_id)
    {
        if($late_id){
            $late = Lates::find($late_id);
            if($late){
                $m = $late->update(['justified' => false, 'motif' => 'Sans motif']);
                if($m){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Mise à jour réussie avec succès!", 'type' => 'success']);
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Mise à jour échouée!", 'type' => 'error']);

                }
                $this->emit('pupilUpdated', $late->pupil_id);
            }
        }
    }


    public function edit($late_id)
    {

    }

    public function delete($late_id)
    {
        if($late_id){
            $late = Lates::find($late_id);
            if($late){
                $m = $late->delete();
                if($m){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Mise à jour réussie avec succès!", 'type' => 'success']);
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Mise à jour échouée!", 'type' => 'error']);

                }
                $this->emit('pupilUpdated', $late->pupil_id);
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
