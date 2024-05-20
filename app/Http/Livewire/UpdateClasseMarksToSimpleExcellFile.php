<?php

namespace App\Http\Livewire;

use App\Events\InitiateClassePupilsDataUpdatingFromFileEvent;
use App\Events\UpdateClasseMarksToSimpleExcelFileEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Imports\ImportPupilPersoDataFromFile;
use App\Models\Pupil;
use App\Models\Subject;
use Illuminate\Support\Facades\File;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class UpdateClasseMarksToSimpleExcellFile extends Component
{
    use WithFileUploads, ModelQueryTrait;

    protected $listeners = ['UpdateClasseMarksToSimpleExcelFile' => 'openModal'];


    public $data_file = null;

    public $semestre = null;

    public $school_year_model = null;

    public $subject = null;

    public $classe_id = null;

    public $classe = null;

    public $pupil = null;

    public $target = 'classe';

    public $show = true;

    public $show_form = true;

    public $target_row;

    public $row;

    public $pupil_id;

    public $pupils_data = [];

    public $targets = ['classe' => "Toute la classe", 'pupil' => "Un élève précis"];

    public $title = "Mise à jour du fichier de notes";

    protected $rules = [
        'data_file' => 'required|max:300|mimes:xlsx,xls'
    ];

    public function render()
    {
        $pupils = [];

        if($this->target && $this->classe && $this->target == 'pupil'){

            $pupils = $this->classe->getPupils();

        }
        return view('livewire.update-classe-marks-to-simple-excell-file', compact('pupils'));
    }

    public function updatedShow($value)
    {
        // $this->show = !$this->show;
    }

    public function toShowOrHide()
    {
         $this->show = !$this->show;
    }

    public function toShowOrHideForm()
    {
        $this->show_form = !$this->show_form;
    }


    public function updatedTarget($target)
    {
        $this->target = $target;
    }

    public function getTargetRowData($row)
    {
        if((!$this->row || !$this->target_row) || (($this->row || $this->row == 0) && $this->target_row && $row !== $this->row)){

            $this->reset('target_row', 'row');

            if(($row || $row == 0) && is_int($row)){

                $row = (int)$row;

                if(isset($this->pupils_data[$row])){

                    $this->row = $row;

                    $this->target_row = $this->pupils_data[$row];

                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "La donnée sélectionnée semble ne pas avoir de correspondance dans le fichier ciblé!", 'type' => 'warning']);

                }

            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Les données sélectionnées sont ambigües!", 'type' => 'error']);

            }


        }
    }


    public function updatedPupilId($pupil_id)
    {
        $this->reset('pupil');

        $this->resetErrorBag('pupil_id');

        $p = Pupil::find($pupil_id);

        if($p){

            $this->pupil = $p;

            $this->title = "Mise à jour du fichier de notes de la " . $p->getName();

        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "La sélection n'existe pas dans la base de données!", 'type' => 'error']);

        }
    }


    public function updatedDataFile($data_file)
    {
        $this->reset('pupils_data');

        $this->validate();

        
    }

    public function importedData()
    {
        $this->validate();

        if($this->target == 'pupil'){

            $this->validate(['pupil_id' => 'required|numeric']);

            if($this->target_row){

                $classe = $this->classe;

                $data = $this->target_row;

                $user = auth()->user();

                if($this->pupil){

                    $pupil = $this->pupil;

                    // InitiateClassePupilsDataUpdatingFromFileEvent::dispatch($classe, $data, $user, $this->pupil_id);

                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Veuillez sélectionnez l'apprenant cible!", 'type' => 'error']);

                    $this->addError('pupil_id', "Veuillez sélectionner l'apprenant cible");

                }
            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Veuillez sélectionnez la donnée dans le fichier!", 'type' => 'error']);

            }

        }
        elseif($this->target == 'classe'){

            if($this->data_file && $this->subject && session('semestre_selected') && session('classe_subject_selected')){

                $classe = $this->classe;

                $user = auth()->user();

                $file_sheet = $this->data_file->getRealPath();

                $file_name = "Notes-" . $this->classe->name . "-" . $this->subject->name . ".XLS";

                $path = storage_path() . '/app/excels';

                if(!File::isDirectory($path)){

                    File::makeDirectory($path, 0777, true, true);
                }

                UpdateClasseMarksToSimpleExcelFileEvent::dispatch($classe, $file_name, $path, $file_sheet, $this->school_year_model, $this->semestre, $this->subject, $user, $this->pupil_id);

            }


        }

        $this->dispatchBrowserEvent('hide-form');

        $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS LANCE', 'message' => "Le fichier de mise à jour des données a été soumis avec succès!", 'type' => 'success']);

        $this->resetErrorBag();

        $this->reset('pupils_data', 'target_row', 'row', 'pupil_id', 'pupil', 'show', 'subject', 'semestre');

        $this->data_file = $this->data_file;
        
    }


    public function clearForm()
    {
        $this->reset('pupils_data', 'target_row', 'row', 'pupil_id', 'pupil', 'show');
    }


    public function openModal($classe_id)
    {
        if($classe_id){

            $this->school_year_model = $this->getSchoolYear();

            $classe = $this->school_year_model->findClasse($classe_id);

            $this->classe = $classe;

            $user = auth()->user();

            $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);

            if($classe){

                if($not_secure || ($user->isAdminAs('master'))){

                    $subject = Subject::find(session('classe_subject_selected'));

                    if($subject){

                        $this->subject = $subject;

                        $this->semestre = session('semestre_selected');

                        $this->classe_id = $classe_id;

                        $this->title = "Mise à jour du fichier de notes de la " . $classe->name;

                        $this->dispatchBrowserEvent('modal-updateClasseMarksToSimpleExcelFileModal');

                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'MATIERE INCONNUE', 'message' => "Vous devez préciser la matière!", 'type' => 'warning']);

                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas mettre à jour les données!", 'type' => 'warning']);

                }
            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VIDE', 'message' => "Vous ne pouvez pas mettre à jour les données: la classe est vide!", 'type' => 'warning']);


            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Veuillez sélectionner une classe, un apprenant et une matière valides", 'type' => 'warning']);

        }

    }
}
