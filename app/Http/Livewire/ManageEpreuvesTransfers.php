<?php

namespace App\Http\Livewire;

use App\Events\LocalTransfertCreatedEvent;
use App\Events\NewEpreuveWasUploadedEvent;
use App\Events\TeacherFileWasSentWithSuccessEvent;
use App\Helpers\FileManager\ZtwenEpreuveFileManager;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\TransferFile;
use Livewire\Component;
use Livewire\TemporaryUploadedFile;
use Livewire\WithFileUploads;

class ManageEpreuvesTransfers extends Component
{
    public $tables = [
        "1er Devoir du Second semestre" => "150 Mb"

    ];


    public $targets = ['devoir' => 'Devoir', 'epe' => 'interrogation', 'bac' => "Examen BAC", 'bepc' => "Examen BEPC"];

    public $pendingFile;

    public $targets_files = [];

    public $semestre = 1;

    public $index = '';

    public $subject_id;

    public $semestre_type = 'Semestre';

    public $teacher_id;

    public $classe_id;

    public $classe_group_id;

    public $school_year_id;

    public $duration = 3;

    public $level_id;

    public $school_year;

    public $target = 'devoir';

    public $description = 'Une epreuve de composition';

    public $exam_name;

    public $session;


    protected $rules = [

        'pendingFile' => 'file|mimes:docx,pdf|max:1000',
        'subject_id' => 'required|int',
        'duration' => 'required|int|max:6',
        'target' => 'required|string',
    ];

    protected $listeners = ['FileWasSendWithSuccess' => 'dataSentSuccessfully'];


    public function mount()
    {
        $user = auth()->user();

        if(!$user || !$user->teacher){

            return abort('403', "Vous n'êtes pas authorisé à accéder à cette page ou elle n'est pas encore disponible!");

        }



    }


    use WithFileUploads;

    use ModelQueryTrait;


    public function render()
    {
        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $this->semestre_type = 'Trimestre';

        }

        $classes = [];

        $classe_groups = [];

        $user = auth()->user();

        $school_year_model = $this->getSchoolYear();

        $this->school_year_id = $school_year_model->id;

        $this->school_year = $school_year_model->school_year;

        $subjects = $school_year_model->subjects;

        if($user){

            $this->subject_id = $user->teacher->speciality()->id;

            $this->level_id = $user->teacher->level_id;

            $this->teacher_id = $user->teacher->id;

            $classes = $school_year_model->classes()->where('classes.level_id', $user->teacher->level_id)->get();

            $classe_groups = $school_year_model->classe_groups()->where('classe_groups.level_id', $user->teacher->level_id)->get();

        }


        return view('livewire.manage-epreuves-transfers', compact('semestres', 'school_year_model', 'classes', 'classe_groups', 'user', 'subjects'));
    }

    public function initiateTransfer()
    {
        $user = auth()->user();

        $folder = config('app.epreuvesFolder');

        $this->validate();

        if(!$this->classe_group_id){

            $this->validate(['classe_id' => 'required|int']);
        }
        else{

            $this->validate(['classe_group_id' => 'required|int']);

        }

        if($this->data_file && $this->subject && session('semestre_selected') && session('classe_subject_selected')){

            $classe = $this->classe;

            $user = auth()->user();

            $file_sheet = $this->data_file->getRealPath();

            $extension = '.' . $this->data_file->extension();

            $semestre = $this->semestre_type . '-' . $this->semestre;

            $manager = new ZtwenEpreuveFileManager($this->classe, "excels", null, $this->school_year_model, $semestre, $this->subject, $extension);

            $file_name = $manager->file_name;

            $path = $manager->storage_path;
            

            // UpdateClasseMarksToSimpleExcelFileEvent::dispatch($classe, $extension, $file_name, $path, $file_sheet, $this->school_year_model, $this->semestre, $this->subject, $user, $this->pupil_id);

        }

    }


    public function putFile()
    {

    }


    public function removeFile()
    {

    }

    public function refreshFilesTables()
    {
        $this->resetErrorBag();
        
        $this->reset('pendingFile', 'targets_files');
    }


    public function frowTransfer()
    {


        $transfer = $user->transfers()->create();

        // $transfer->files()->saveMany(
        //     collect($this->pendingFiles)->map(function(TemporaryUploadedFile $pendFile) use($folder, $user){

        //         $sub = '';

        //         $cln = '';

        //         if($this->subject_id){

        //             $sub = $user->teacher->speciality()->name;

        //         }

        //         if($this->classe_id){

        //             $classe = Classe::find($this->classe_id);

        //             $cln = $classe->getSlug();

        //         }
        //         elseif($this->classe_group_id){

        //             $classe_group = ClasseGroup::find($this->classe_group_id);

        //             $cln = $classe_group->getSlug();

        //         }

        //         $time = time();

        //         $name = $this->index. 'e-' . $this->target . '-' . $this->semestre . '' . substr($this->semestre_type, 0, 1) . '-' . $cln. '-' . $sub . '-' . str_replace(' ', '', $this->school_year) . 'fichier-' . $time . '.' . $pendFile->extension();

        //         $file = $pendFile->storeAs($folder, $name);

        //         return new TransferFile([

        //             'path' => str_replace('//', '/', $pendFile->getPath()),
        //             'size' => $pendFile->getSize(),
        //             'name' => $name,
        //             'subject_id' => $this->subject_id,
        //             'classe_id' => $this->classe_id,
        //             'school_year_id' => $this->school_year_id,
        //             'classe_group_id' => $this->classe_group_id,
        //             'target' => $this->target,
        //             'semestre' => $this->semestre,
        //             'teacher_id' => $this->teacher_id,
        //             'level_id' => $this->level_id,
        //             'duration' => $this->duration,
        //         ]);

        //     })

        // );

        // $this->pendingFiles = [];

        // NewEpreuveWasUploadedEvent::dispatch($transfer);

        // TeacherFileWasSentWithSuccessEvent::dispatch($transfer, $user);

        // // LocalTransfertCreatedEvent::dispatch($transfer);
        

    }

    public function dataSentSuccessfully()
    {
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'EPREUVE ENVOYEE', 'message' => "Votre épreuve a été soumise avec succès!", 'type' => 'success']);
        
    }


    public function updatedPendingFile()
    {
        $this->resetErrorBag();
    }
}
