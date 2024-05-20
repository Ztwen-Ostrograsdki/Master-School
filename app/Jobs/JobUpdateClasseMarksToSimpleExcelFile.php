<?php

namespace App\Jobs;

use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\User;
use Illuminate\Bus\Batchable;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Writer\Xls;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

class JobUpdateClasseMarksToSimpleExcelFile implements ShouldQueue
{
    use Batchable, Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public $classe;

    public $school_year_model;

    public $file_name;

    public $semestre;

    public $file_sheet;

    public $file_path;

    public $subject;

    public $user;

    public $pupil_id = null;



    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct(Classe $classe, $file_name, $file_path, $file_sheet, SchoolYear $school_year_model, $semestre, Subject $subject, User $user, $pupil_id = null)
    {
        $this->classe = $classe;

        $this->school_year_model = $school_year_model;

        $this->semestre = $semestre;

        $this->file_sheet = $file_sheet;

        $this->file_name = $file_name;

        $this->file_path = $file_path;

        $this->subject = $subject;

        $this->user = $user;

        $this->pupil_id = $pupil_id;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        $counter = 0;

        $classe = $this->classe;

        $school_year_model = $this->school_year_model;

        $semestre = $this->semestre;

        $subject_id = $this->subject->id;

        $rank = "Non Classé";

        $path = $this->file_sheet; 

        $spreadsheet = IOFactory::load($path);

        $sheet = $spreadsheet->getActiveSheet();

        $averageEPETab = $classe->getMarksAverage($subject_id, $semestre, $school_year_model->school_year, 'epe');

        foreach($sheet->getRowIterator() as $k => $row){

            if($counter++ === 0) continue;

            $matricule = $sheet->getCell("B" . $k)->getValue();

            $pupil = Pupil::where('ltpk_matricule', $matricule)->orWhere('educmaster', $matricule)->orWhere('matricule', $matricule)->first();



            if($pupil){

                $marks = $pupil->getMarks($subject_id, $semestre, $school_year_model->school_year);

                $moy_int = "-";

                $dev1 = "-";

                $dev2 = "-";

                $devs_to = [];

                $devs = [];

                if(count($marks)){

                    

                    if(isset($marks[$subject_id]) && isset($marks[$subject_id]['devoir']) && count($marks[$subject_id]['devoir'])){

                        $datas = $marks[$subject_id];

                        $devs = $datas['devoir'];

                        foreach($devs as $dev){

                            $devs_to[] = $dev;

                        }

                        if($devs_to){

                            if(isset($devs_to[0])){

                                $dev1 = $devs_to[0]->value;

                            }

                            if(isset($devs_to[1])){

                                $dev2 = $devs_to[1]->value;

                            }

                        }

                    }

                    if(count($averageEPETab) && isset($averageEPETab[$pupil->id])){

                        $moy_int = $averageEPETab[$pupil->id];

                    }


                    $e = 'E' . $k;
                    $f = 'F' . $k;
                    $g = 'G' . $k;

                    $sheet->setCellValue($e, $moy_int);

                    $sheet->setCellValue($f, $dev1);

                    $sheet->setCellValue($g, $dev2);


                }


            }

            

           
        }

        // $file_name = "Notes-" . $this->classe->name . "-" . $this->subject->name . ".XLS";

        // $path = storage_path() . '/app/excels';

        $writer = (new Xls($spreadsheet))->save($this->file_path . '/' . $this->file_name);

    }
}
