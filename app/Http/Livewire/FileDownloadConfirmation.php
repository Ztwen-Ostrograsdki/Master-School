<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class FileDownloadConfirmation extends Component
{

    protected $listeners = ['ClasseMarksToSimpleExcelFileCompletedLiveEvent' => 'openModal'];

    public $file_path;

    public $file_to_download;

    public $title = "Téléchargement du fichier renseigné";


    public function render()
    {
        return view('livewire.file-download-confirmation');
    }


    public function openModal($file_name)
    {
        // $path = storage_path() . '/app/excels/' . $file_name;

        $this->file_to_download = $file_name;

        $this->dispatchBrowserEvent('modal-excelFileDownloadConfirmation');

    }


    public function accepted()
    {
        // return Storage::download(public_path('excels/'. $this->file_to_download));
        return Storage::disk('excels')->download('storage/excels/'. $this->file_to_download);
    }


    public function cancel()
    {
        dd($this);
    }



}
