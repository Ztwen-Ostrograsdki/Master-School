<?php

namespace App\Http\Controllers;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\SchoolYear;
use PDF;
use Illuminate\Http\Request;

class ClasseListDownload extends Controller
{
    use ModelQueryTrait;

    public function index($classe_id)
    {

        $school_year = session('school_year_selected');
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
        $school_years = SchoolYear::all();
        $pupils = [];

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
            $subject_id = intval(session('classe_subject_selected'));
            if($classe && in_array($subject_id, $classe->subjects->pluck('id')->toArray())){
                session()->put('classe_subject_selected', $subject_id);
                $classe_subject_selected = $subject_id;
            }
            else{
                $classe_subject_selected = null;
            }
        }
        else{
            $classe_subject_selected = null;
        }

        if($classe){
            $pupils = $classe->getPupils($school_year_model->id);
        }

        $data = ['classe' => $classe, 'pupils' => $pupils, 'classe_subject_selected', $classe_subject_selected];

        return view('pdf.classe-list-download', $data);

    }


    public function createPDF($classe_id)
    {
        $school_year = session('school_year_selected');
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
        $school_years = SchoolYear::all();
        $pupils = [];

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
            $subject_id = intval(session('classe_subject_selected'));
            if($classe && in_array($subject_id, $classe->subjects->pluck('id')->toArray())){
                session()->put('classe_subject_selected', $subject_id);
                $classe_subject_selected = $subject_id;
            }
            else{
                $classe_subject_selected = null;
            }
        }
        else{
            $classe_subject_selected = null;
        }

        if($classe){
            $pupils = $classe->getPupils($school_year_model->id);
        }

        $data = ['classe' => $classe, 'pupils' => $pupils, 'classe_subject_selected', $classe_subject_selected];
        if($data){
            view()->share($data);
            $pdf = PDF::loadView('pdf.classe-list-download', $data);
            return $pdf->output();
        }

    }
}
