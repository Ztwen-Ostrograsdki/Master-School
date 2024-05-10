<?php

namespace App\Http\Livewire;

use Barryvdh\DomPDF\PDF;
use Livewire\Component;

class PDFPrinter extends Component
{
    public function render()
    {
        return view('livewire.p-d-f-printer');
    }


    public function to_print()
    {
        // $pdf = PDF::loadView('livewire.p-d-f-printer', ['aime' => 'sec']);

        // dd($pdf);

        // return $pdf->stream('PDFFF.pdf');


    }
}
