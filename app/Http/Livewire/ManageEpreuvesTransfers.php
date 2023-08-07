<?php

namespace App\Http\Livewire;

use App\Events\LocalTransfertCreatedEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\TransferFile;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ManageEpreuvesTransfers extends Component
{
    public $tables = [
        "Mano" => "15 Mb",
        "Belgarod" => "1 Mb",
        "Faotef" => "150 Mb",
        "Telsph" => "11 Mb",
        "Destch" => "25 Mb",
        "Updalings" => "2.25 Mb",
        "Gifraree" => "150 Mb"

    ];

    public $pendingFiles = [];

    public $semestre = 1;


    use WithFileUploads;

    use ModelQueryTrait;


    public function render()
    {
        $semestre_type = 'Semestre';

        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $semestre_type = 'Trimestre';

        }

        $school_year_model = $this->getSchoolYear();

        $subjects = $school_year_model->subjects;


        return view('livewire.manage-epreuves-transfers', compact('semestres', 'semestre_type', 'school_year_model'));
    }


    public function initiateTransfer()
    {
        $user = auth()->user();

        $this->validate(['pendingFiles.*' => ['file', 'mimes:docx,pdf', 'max:1000']]);


        $transfer = $user->transfers()->create();

        $transfer->files()->saveMany(
            collect($this->pendingFiles)->map(function(TemporaryUploadedFile $pendFile) use($user){

                $school_year_model = $this->getSchoolYear();

                $subject = $user->teacher->speciality();

                $level_id = $user->teacher->speciality()->level_id;

                $subject_id = $subject->id;

                $classe_id = 3;

                $semestre = 1;

                $teacher_id = $user->teacher->id;

                $name = $semestre . 'D' . str_replace(' ', '', $school_year_model->school_year) . getdate()['year'].''.getdate()['mon'].''.getdate()['mday'].''.getdate()['hours'].''.getdate()['minutes'].''.getdate()['seconds']. '.' . $pendFile->extension();

                $file = $pendFile->storeAs('epreuvesFolder/', $name);

                return new TransferFile([

                    'path' => $pendFile->getRealPath(),
                    'size' => $pendFile->getSize(),
                    'name' => $name,
                    'subject_id' => $subject_id,
                    'classe_id' => $classe_id,
                    'target' => 'devoir',
                    'semestre' => $semestre,
                    'teacher_id' => $teacher_id,
                    'level_id' => $level_id,
                ]);

            })

        );

        $this->pendingFiles = [];

        
        LocalTransfertCreatedEvent::dispatch($transfer);
        

    }


    public function updatedPendingFiles()
    {
        $this->resetErrorBag();
    }
}
