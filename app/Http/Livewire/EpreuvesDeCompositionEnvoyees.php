<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Epreuves;
use App\Models\Teacher;
use App\Models\TransferFile;
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

        $to_fetch = [];

        $epreuves_targets = config('app.local_epreuves_targets');

        if(session()->has('epreuves_target_selected')){

            $this->target_selected = session('epreuves_target_selected');
        }

        $subjects = [];

        $teachers = [];

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

        // $teachers_ids = TransferFile::query()->select('teacher_id')->with('teacher', '')->groupBy('teacher_id')->get();

        // $teachers = Teacher::whereIn($teachers_ids)->get();

        // if(count($teachers)){

        //     foreach($teachers as $teacher){

        //         // $eprs = $teacher->epreuves()->where('transfer_files.school_year_id', $school_year_model->id)->where('transfer_files.semestre', $semestre)->get();

        //         // $epreuves[$teacher->id] = ['epreuves' => $eprs, 'teacher' => $teacher];

        //     }


        // }

        $epreuves = [];

        

        // if($this->target_selected && $this->target_selected !== 'all'){

        //     $epreuves = $school_year_model->epreuves()->where('transfer_files.target', $this->target_selected)->where('transfer_files.semestre', $this->semestre_selected)->groupBy('teacher_id')->get();
        // }
        // elseif($this->target_selected == 'all'){

        //     $epreuves = $school_year_model->epreuves()->groupBy('teacher_id')->get();
        // }
        // else{

        //     $epreuves = $school_year_model->epreuves()->whereNull('transfer_files.target')->groupBy('teacher_id')->get();
        // }


        return view('livewire.epreuves-de-composition-envoyees', compact('semestres', 'semestre_type', 'school_year_model', 'subjects', 'epreuves', 'epreuves_targets', 'teachers'));
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

    public function downloadPDF($name)
    {
        $path = storage_path().'/app/epreuvesFolder/' . $name;

        return response()->download($path);
    }

    public function delete($name)
    {
        dd($name);
        
    }
}
