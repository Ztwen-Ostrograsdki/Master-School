<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class EpreuvesDeCompositionEnvoyees extends Component
{

    use ModelQueryTrait;

    protected $listeners = ['NewEpreuveWasUploadedLiveEvent' => 'newDataWasSent'];

    public $counter = 0;

    public $semestre_selected;

    public $target_selected = 'devoir';





    public function render()
    {
        $aes = [];

        $epreuves_targets = config('app.local_epreuves_targets');

        if(session()->has('epreuves_target_selected')){

            $this->target_selected = session('epreuves_target_selected');
        }

        $subjects = [];

        $semestre_type = 'Semestre';

        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $semestre_type = 'Trimestre';

        }

        if(!$this->semestre_selected){

            $this->semestre_selected = session('semestre_selected');

        }

        $school_year_model = $this->getSchoolYear();

        $subjects = $school_year_model->subjects;

        if($this->target_selected && $this->target_selected !== '2000'){

            $epreuves = $school_year_model->epreuves()->where('transfer_files.target', $this->target_selected)->where('transfer_files.semestre', $this->semestre_selected)->get();
        }
        elseif($this->target_selected == '2000'){

            $epreuves = $school_year_model->epreuves;
        }
        else{

            $epreuves = $school_year_model->epreuves()->whereNull('transfer_files.target')->get();
        }


        return view('livewire.epreuves-de-composition-envoyees', compact('semestres', 'semestre_type', 'school_year_model', 'subjects', 'epreuves', 'epreuves_targets'));
    }

    public function updatedSemestreSelected($semestre)
    {
        $this->semestre_selected = $semestre;

        session()->put('semestre_selected', $semestre);
    }


    public function updatedTargetSelected($target)
    {
        session()->put('epreuves_target_selected', $target);
        $this->target_selected = $target;
    }

    public function newDataWasSent()
    {
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'NOUVELLE EPREUVE RECUE', 'message' => "Des épreuves sont été reçues!", 'type' => 'success']);

        $this->reloadData();
    }

    public function reloadData()
    {
        $this->counter = rand(0, 14);
    }

    public function ztwen($name = 2)
    {
        Storage::download('epreuvesFolder', 'elle'.$name);
    }

    public function delete($name)
    {
        dd($name);
        
    }
}
