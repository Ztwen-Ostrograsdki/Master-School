<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseMarksExcelFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Livewire\Component;

class ClassesMarksExcelFilesCompleted extends Component
{
    protected $listeners = [
        'ClasseMarksToSimpleExcelFileCompletedLiveEvent' => 'reloadFiles',
        'ClasseExcelsFilesWasUpdatedLiveEvent' => 'reloadFiles',
    ];

    use ModelQueryTrait;

    public $classe_id;

    public $classe;

    public $counter = 1;


    public function render()
    {
        $classe_files = [];

        $school_year_model = $this->getSchoolYear();

        $classe = null;

        if($this->classe_id){

            $classe = $school_year_model->findClasse($this->classe_id);

            if($classe){

                $classe_files = ClasseMarksExcelFile::where('classe_id', $classe->id)->where('school_year_id', $school_year_model->id)->get();

            }

        }

        return view('livewire.classes-marks-excel-files-completed', compact('classe_files', 'classe', 'school_year_model'));
    }


    public function reloadFiles($last = null)
    {
        $this->counter = rand(2, 12);
    }

    public function downloadTheFile($file_id)
    {
        $file_model = ClasseMarksExcelFile::find($file_id);

        if($file_model){

            if(!$file_model->secure){

                $full_path = $file_model->path . '/' . $file_model->name;

                $d = response()->download($full_path, $file_model->name);

                if($d){

                    $dc =  $file_model->downloaded_counter + 1;

                    $file_model->update(['downloaded' => true, 'downloaded_counter' => $dc]);

                    $this->dispatchBrowserEvent('Toast', ['title' => 'TELECHARGEMENT LANCE', 'message' => "Le fichier est en cours de téléchargement", 'type' => 'success']);

                    return $d;

                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Une erreure s'est produite au cours du téléchargement!", 'type' => 'error']);

                }

            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'FICHIER VERROUILLE', 'message' => "Le téléchargement du fichier a été bloqué, vous ne pouvez pas le télécharger!", 'type' => 'info']);

            }

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'FICHIER INEXISTANT OU SUPPRIME', 'message' => "Le fichier n'a pas été trouvé !", 'type' => 'warning']);

        }
    }

    public function secureTheFile($file_id)
    {
        $file_model = ClasseMarksExcelFile::find($file_id);

        if($file_model){

            if($file_model->secure){

                $file_model->update(['secure' => true]);

                $this->dispatchBrowserEvent('Toast', ['title' => 'VEROUILLAGE TERMINE', 'message' => "Le fichier a été verrouillé avec succès", 'type' => 'success']);


            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'INFO', 'message' => "Il semble que le fichier ait déjà été verrouillé!", 'type' => 'info']);

            }

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'FICHIER INEXISTANT OU SUPPRIME', 'message' => "Le fichier n'a pas été trouvé !", 'type' => 'warning']);

        }
    }


    public function unsecureTheFile($file_id)
    {
        $file_model = ClasseMarksExcelFile::find($file_id);

        if($file_model){

            if(!$file_model->secure){

                $file_model->update(['secure' => false]);

                $this->dispatchBrowserEvent('Toast', ['title' => 'DEVEROUILLAGE TERMINE', 'message' => "Le fichier a été déverrouillé avec succès", 'type' => 'success']);


            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'INFO', 'message' => "Il semble que le fichier ait déjà été déverrouillé!", 'type' => 'info']);

            }

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'FICHIER INEXISTANT OU SUPPRIME', 'message' => "Le fichier n'a pas été trouvé !", 'type' => 'warning']);

        }
    }

    public function deleteTheFile($file_id)
    {
        $file_model = ClasseMarksExcelFile::find($file_id);

        if($file_model){

            if(!$file_model->secure){

                $full_path = $file_model->path . '/' . $file_model->name;

                $del = $file_model->delete();

                if($del){

                    $dele_from_storage = File::delete($full_path);

                    $this->dispatchBrowserEvent('Toast', ['title' => 'SUPPRESSION TERMINEE', 'message' => "Le fichier a été supprimé avec succès", 'type' => 'success']);

                }
                else{

                    $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE SERVEUR', 'message' => "Une erreure s'est produite lors de la suppression du fichier", 'type' => 'error']);

                }

            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'FICHIER VERROUILLE', 'message' => "Le fichier a été bloqué, vous ne pouvez pas le supprimer!", 'type' => 'info']);

            }

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'FICHIER INEXISTANT OU SUPPRIME', 'message' => "Le fichier n'a pas été trouvé !", 'type' => 'warning']);

        }
    }






}
