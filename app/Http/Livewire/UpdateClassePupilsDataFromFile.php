<?php

namespace App\Http\Livewire;

use App\Events\InitiateClassePupilsDataUpdatingFromFileEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Imports\ImportPupilPersoDataFromFile;
use Livewire\Component;
use Livewire\WithFileUploads;
use Maatwebsite\Excel\Facades\Excel;
use PhpOffice\PhpSpreadsheet\Reader\Xls;
use PhpOffice\PhpSpreadsheet\Reader\Xlsx;

class UpdateClassePupilsDataFromFile extends Component
{
    use WithFileUploads, ModelQueryTrait;

    protected $listeners = ['UpdateClassePupilsPersoDataFromFile' => 'openModal'];


    public $data_file = null;

    public $classe_id = null;

    public $classe = null;

    public $title = "Mise à jour des données des apprenants via un fichier";

    protected $rules = [
        'data_file' => 'required|max:300|mimes:xlsx,xls'
    ];

    public function render()
    {
        return view('livewire.update-classe-pupils-data-from-file');
    }


    
    public function importedData()
    {
        $this->validate();

        // Excel::import(new ImportPupilPersoDataFromFile, $this->data_file);

        $path = $this->data_file->getRealPath();

        // $data = Excel::import(new ImportPupilPersoDataFromFile, $this->data_file);

        $sheet = (new Xls)->load($path)->getActiveSheet();

        $counter = 0;

        foreach($sheet->getRowIterator() as $row){

            if($counter++ === 0) continue;

            $cells = $row->getCellIterator();

            $cells->setIterateOnlyExistingCells(true);

            $pupil_data = [];

            foreach($cells as $cell){

                $v = $cell->getValue();

                $pupil_data[]  = $v;

            }

            $ltpk_matricule = $pupil_data[1];

            $firstName = strtoupper($pupil_data[2]);

            $lastName = ucwords($pupil_data[3]);

            $pupil = [
                'ltpk_matricule' => $ltpk_matricule, 
                'firstName' => $firstName, 
                'lastName' => $lastName
            ];


            $data[] = $pupil;

            
        }


        if($data){

            $classe = $this->classe;

            $user = auth()->user();

            InitiateClassePupilsDataUpdatingFromFileEvent::dispatch($classe, $data, $user);

        }

        $this->dispatchBrowserEvent('hide-form');

        $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS LANCE', 'message' => "Le fichier de mise à jour des données a été soumis avec succès!", 'type' => 'success']);

        $this->resetErrorBag();

        $this->reset('classe_id', 'classe', 'data_file');
        
    }


    public function openModal($classe_id)
    {
        if($classe_id){

            $school_year_model = $this->getSchoolYear();

            $classe = $school_year_model->findClasse($classe_id);

            $this->classe = $classe;

            $user = auth()->user();

            $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);

            if($classe){

                if($not_secure || ($user->isAdminAs('master'))){

                    $this->classe_id = $classe_id;

                    // $pupils = $classe->getNotAbandonnedPupils();

                    // foreach($pupils as $p){

                        // $this->matricule_data[$p->id] = $p->ltpk_matricule;

                    // }

                    $this->dispatchBrowserEvent('modal-updateClassePupilsDataFromFile');

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
