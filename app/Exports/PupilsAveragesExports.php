<?php

namespace App\Exports;

use App\Helpers\Tools\Tools;
use App\Models\Classe;
use App\Models\SchoolYear;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithStyles;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Protection;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PupilsAveragesExports implements FromCollection, ShouldAutoSize, WithHeadings, WithEvents, WithStyles
{

    use Exportable; 

    public $classe;

    public $school_year_model;

    public $semestre_type;

    public $semestre;

    public $subject;

    public $withRank;

    public $epeMaxLenght = 1;

    public $all = false;



   public function __construct(Classe $classe, SchoolYear $school_year_model, $semestre, $subject, $all = false, $withRank = false)
    {
        $this->classe = $classe;

        $this->semestre_type = session('semestre_type');

        $this->semestre = $semestre;

        $this->classe_id = $classe->id;

        $this->subject = $subject;

        $this->school_year_model = $school_year_model;

        $this->withRank = $withRank;

        $this->all = $all;

        if($this->all){

            $this->epeMaxLenght = $classe->getMarksTypeLenght($subject->id, $semestre, $school_year_model->school_year, 'epe');

        }
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];

        $ranksTab = [];

        $epeMaxLenght = 0;

        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        $semestre = $this->semestre;

        $subject = $this->subject;

        $rank = "Non Classé";

        $epes = [];

        $parts = [];

        $pupils = $classe->getNotAbandonnedPupils($school_year_model->id);

        $marks = $classe->getMarks($subject->id, $semestre, 2, $school_year_model->school_year);

        $averageEPETab = $classe->getMarksAverage($subject->id, $semestre, $school_year_model->school_year, 'epe');

        $averageTab = $classe->getAverage($subject->id, $semestre, $school_year_model->school_year);

        if($this->withRank){

            $ranksTab = $classe->getClasseRank($subject->id, $semestre, $school_year_model->school_year);
        }
        else{

            $ranksTab = [];

        }

        $classe_subject_coef = $classe->get_coefs($subject->id, $school_year_model->id, true);

        $k = 1;

        foreach($pupils as $p){

            $devs = $marks[$p->id]['dev'];

            $dev1 = ' - ';

            $dev2 = ' - ';

            $moy = ' - ';

            $moy_coef = ' - ';

            $mention = ' - ';



            if($devs){

                if(count($devs) == 2){

                    $dev1 = $devs[0]->value;

                    $dev2 = $devs[1]->value;

                }
                elseif(count($devs) == 1){

                    $dev1 = $devs[0]->value;

                }


            }

            if($averageTab && isset($averageTab[$p->id])){

                $av = $averageTab[$p->id];

                if($av){

                    $moy = $av;

                    $moy_coef = $av * $classe_subject_coef;

                    $mention = Tools::getMention($moy);

                }

                if($this->withRank && isset($ranksTab[$p->id])){

                    $rank = $ranksTab[$p->id]['rank'] . ' ' . $ranksTab[$p->id]['exp'] . ' ' . $ranksTab[$p->id]['base']; 

                }


            }

            if($this->all == false){

                $data[$p->id] = [
                    "N° d'ordre" => $k,
                    "Matricule" => $p->ltpk_matricule,
                    "Nom " => $p->firstName,
                    'Prenoms' => $p->lastName,
                    'Moy Int' => $averageEPETab[$p->id],
                    'DEV 1' => $dev1,
                    'DEV 2' => $dev2,
                    'MOY. Semestre' => $moy,
                    // 'Moy. Coef.' => $moy_coef,
                    'OBS' => $mention,

                ];

            }
            else{

                $to_fetch = [
                    "N° d'ordre" => $k,
                    "Matricule" => $p->ltpk_matricule,
                    "Nom " => $p->firstName,
                    'Prenoms' => $p->lastName,

                ];

                $all_marks = $p->getMarks($subject->id, $semestre);



                if(count($all_marks) > 0){

                    $all_marks = $all_marks[$subject->id];

                    $epes = $all_marks['epe'];

                    $parts = $all_marks['participation'];

                    for($ii = 0; $ii < $this->epeMaxLenght; $ii++){

                        $id = $ii + 1;

                        if(isset($epes[$ii])){

                            $epe = $epes[$ii]->value;

                            $to_fetch["INT" . $id] = $epe == 0.0 ? '00' : $epe; 

                        }
                        else{

                            $to_fetch["INT" . $id] = " - "; 

                        }

                    }

                    if(count($parts)){

                        $part = $parts[0];

                        $to_fetch["PART."] = $part->value == 0.0 ? '00' : $part->value; 

                    }
                    else{

                        $to_fetch["PART."] = " - "; 

                    }

                }

                $to_fetch["Moy Int"] = $averageEPETab[$p->id];
                $to_fetch["DEV 1"] = $dev1;
                $to_fetch["DEV 2"] = $dev2;
                $to_fetch["Moy"] = $moy;
                $to_fetch["Moy Coef"] = $moy_coef;
                $to_fetch["RANG"] = $rank;
                $to_fetch["OBS"] = $mention;

                $data[$p->id] = $to_fetch;

            }

            $k++;


        }

        // dd($data);

        return collect($data);
    }


    public function headings(): array
    {
        if($this->all == false){

            return ["N° d'ordre", 'MATRICULE', 'NOM', 'PRENOMS', 'MOY. INT', 'DEV 1', 'DEV 2', 'MOY. Semestre', 'OBS'];

        }
        else{

            $epe_size = $this->epeMaxLenght;

            $headers = ["N° d'ordre", "MATRICULE", "NOM", "PRENOMS"];

            $default_headers = ['MOY. INT', 'DEV 1', 'DEV 2', 'MOY', 'MOY. COEF', 'RANG', 'OBS'];


            for ($i=1; $i <= $epe_size ; $i++) { 
                
                $headers[] = "INT " . $i;
            }

            $headers[] = "PART.";

            foreach($default_headers as $h){

                $headers[] = $h;

            }

            return $headers;

        }
    }


    public function styles(Worksheet $sheet)
    {

            // $sheet->getParent()->getActiveSheet()->getProtection()->setSheet(true);

            // $sheet->getParent()->getActiveSheet()
            //                    ->getStyle('T2:T10')
            //                    ->getProtection()
            //                    ->setLocked(Protection::PROTECTION_UNPROTECTED);

        // $sheet->getProtection()->setSheet(true);

        // $sheet->getStyle('A1:A10')->getProtection()->setLocked(Protection::PROTECTION_UNPROTECTED);


    }


    public function registerEvents() : array
    {
        return [

            AfterSheet::class => function(AfterSheet $event){

                $event->sheet->getDelegate()->freezePane("A1");

                // $event->sheet->getDelegate()->freezePane("C1");

            }


        ];
    }


}
