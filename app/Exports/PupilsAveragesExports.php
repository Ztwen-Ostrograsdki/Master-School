<?php

namespace App\Exports;

use App\Helpers\Tools\Tools;
use App\Models\Classe;
use Maatwebsite\Excel\Concerns\Exportable;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;

class PupilsAveragesExports implements FromCollection, ShouldAutoSize, WithHeadings
{

    use Exportable; 

    public $classe;

    public $school_year_model;

    public $semestre_type;

    public $semestre;

    public $subject;

    public $withRank;

    public $all = false;



   public function __construct(Classe $classe, $school_year_model, $semestre, $subject, $all = false, $withRank = false)
    {
        $this->classe = $classe;

        $this->semestre_type = session('semestre_type');

        $this->semestre = $semestre;

        $this->classe_id = $classe->id;

        $this->subject = $subject;

        $this->school_year_model = $school_year_model;

        $this->withRank = $withRank;

        $this->all = $all;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        $data = [];

        $ranksTab = [];

        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        $semestre = $this->semestre;

        $subject = $this->subject;


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


            }

            if($this->all == false){

                $data[$p->id] = [
                    "N° d'ordre" => $k,
                    "Matricule" => $p->ltpk_matricule,
                    'Nom et Prenoms' => $p->getName(),
                    'Moy Int' => $averageEPETab[$p->id],
                    'DEV 1' => $dev1,
                    'DEV 2' => $dev2,
                    'MOY.' => $moy,
                    'Moy. Coef.' => $moy_coef,
                    'OBS' => $mention,

                ];

            }
            else{


            }

            $k++;


        }

        return collect($data);
    }


    public function headings(): array
    {
        if($this->all == false){

            return ["N° d'ordre", 'MATRICULE', 'NOM ET PRENOMS', 'MOY. INT', 'DEV 1', 'DEV 2', 'MOY', 'MOY. COEF', 'OBS'];

        }
        else{

            return ["N° d'ordre", 'MATRICULE', 'NOM ET PRENOMS', 'MOY. INT', 'DEV 1', 'DEV 2', 'MOY', 'MOY. COEF', 'OBS'];

        }
    }


}
