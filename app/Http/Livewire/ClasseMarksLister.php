<?php

namespace App\Http\Livewire;

use App\Events\UpdateClasseSanctionsEvent;
use App\Exports\PupilsAveragesExports;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\Subject;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ClasseMarksLister extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'classeSubjectUpdated' => 'reloadData',
        'ReloadClasseListDataAbandonLiveEvent' => 'reloadData',
        'classePupilListUpdated' => 'reloadData',
        'schoolYearChangedLiveEvent' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'semestreWasChanged',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
        'ClasseDataWasUpdated' => 'reloadData',
        'UpdatedGlobalSearch' => 'reloadClasseDataOnSearch',
        'UpdatedRelaodNowLiveEvent' => 'updatedRelaodNow',
        'NewClasseMarksInsert' => 'reloadData',
        'InitiateClasseDataUpdatingLiveEvent' => 'loadingDataStart',
        'ClasseDataLoadedSuccessfully' => 'dataWasLoaded',
        'ClasseDisplayingRankUpdatedLiveEvent' => 'reloadRankToFetch',
        'selectedClasseSubjectChangeLiveEvent' => 'reloadDataFormSelectedSubjectChanged',
        'PrintMarksAsExcelFileLiveEvent' => 'printMarksAsExcelFile',

    ];

    public $classe_id;
    public $pupil_id;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $classe_subject_selected;
    public $subject_selected;
    public $semestre_selected = 1;
    public $classe_marks = [];
    public $edit_mark_value = 0;
    public $edit_mark_type = 'epe';
    public $editing_mark = false;
    public $invalid_mark = false;
    public $edit_key;
    public $mark_key;
    public $targetedMark;
    public $count = 0;
    public $search = null;
    public $computedRank = false;
    public $teacher_profil = false;
    public $relaodNow = false;
    public $is_loading = false;
    public $simpleFormat = false;


    public function showFormattedView($classe_id = null)
    {

        if(is_null($classe_id)){

            $classe_id = $this->classe_id;

        }

        $this->simpleFormat = !$this->simpleFormat;

    }


    public function reloadClasseDataOnSearch($value)
    {
        $this->search = $value;
    }

    public function updatedSearch($value)
    {
        $this->search = $value;
    }

    public function dataWasLoaded()
    {
        $this->is_loading = false;
    }

    public function loadingDataStart()
    {
        $this->is_loading = true;
    }

    public function reloadRankToFetch($display_rank)
    {
        $this->computedRank = $display_rank;
    }

    public function render()
    {
        $pupils = [];

        $printing = false;

        $marks = [];

        $noMarks = false;

        $modality = null;

        $modalitiesActivated = null;

        $hasModalities = false;

        $averageEPETab = [];

        $averageTab = [];

        $ranksTab = [];

        $classe_subject_coef = 1;


        $marks_lenght = 1;

        $epeMaxLenght = 1;

        $devMaxLenght = 1;

        $participMaxLenght = 1;

        $classe_subjects = [];


        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);

        if($classe){

            $classe_subjects = $classe->subjects;

        }
        else{

            $classeSelf = Classe::find($this->classe_id);

            $classe_subjects = $classeSelf->subjects;

        }

        if(session()->has('semestre_selected') && session('semestre_selected')){

            $semestre = intval(session('semestre_selected'));

            session()->put('semestre_selected', $semestre);

            $this->semestre_selected = $semestre;
        }

        if(session()->has('semestre_type') && session('semestre_type')){

            $semestre_type = session('semestre_type');

            session()->put('semestre_type', $semestre_type);

            $this->semestre_type = $semestre_type;
        }
        else{
            session()->put('semestre_type', $this->semestre_type);
        }

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){

            $subject_id = intval(session('classe_subject_selected'));

            if($classe && ($classe_subjects && $classe_subjects !== [])){

                $subjects_ids = $classe_subjects->pluck('id')->toArray();

                if(in_array($subject_id, $subjects_ids)){

                    session()->put('classe_subject_selected', $subject_id);

                    $this->classe_subject_selected = $subject_id;

                    $this->subject_selected = Subject::find($this->classe_subject_selected);
                }

            }
            else{

                $this->reset('classe_subject_selected', 'subject_selected');
            }
        }

        if($classe){

            $pupils = $classe->getNotAbandonnedPupils($school_year_model->id, $this->search);

            $marks = $classe->getMarks($this->classe_subject_selected, $this->semestre_selected, 2, $school_year_model->school_year);

            $averageEPETab = $classe->getMarksAverage($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year, 'epe');

            $averageTab = $classe->getAverage($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year);

            if($this->computedRank){

                $ranksTab = $classe->getClasseRank($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year);
            }
            else{

                $ranksTab = [];

            }

            $classe_subject_coef = $classe->get_coefs($this->classe_subject_selected, $school_year_model->id, true);


            $epeMaxLenght = $classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year, 'epe') + 1;

            $devMaxLenght = $classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year, 'devoir');

            $participMaxLenght = $classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year, 'participation');

            if(($epeMaxLenght < 1 && $participMaxLenght < 1 && $devMaxLenght < 1)){

                $noMarks = true;
            }

            if($epeMaxLenght < 2){

                $epeMaxLenght = 2;
            }
            if($participMaxLenght < 1){

                $participMaxLenght = 1;
            }
            if($devMaxLenght <= 2){

                $devMaxLenght = 2;
            }
            if(!($epeMaxLenght && $devMaxLenght && $participMaxLenght)){

                $noMarks = true;
            }

            if($this->semestre_selected && $this->subject_selected){

                $semestre = $this->semestre_selected;

                $modality = $this->subject_selected->getAverageModalityOf($classe->id, $school_year_model->school_year, $semestre);

                $modalitiesActivated = $classe->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->where('activated', true)->count() > 0;

                $hasModalities = $classe->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre)->count() > 0;

                if($modality){

                    $modality = $modality->modality;
                }
                else{

                    $modality = null;
                }
            }

        }


       


        $calendar_profiler = $school_year_model->calendarProfiler();

        $current_period = $calendar_profiler['current_period'];

        return view('livewire.classe-marks-lister', 
                    compact(
                        'classe',
                        'current_period',
                        'pupils', 'marks', 'epeMaxLenght', 'devMaxLenght', 'participMaxLenght', 'noMarks', 'modality', 'modalitiesActivated', 'hasModalities', 'averageEPETab', 'averageTab', 'classe_subject_coef', 'ranksTab', 'classe_subjects', 'school_year_model', 'printing'
                    )
                );
    }


    public function setTargetedMark($mark_id)
    {
        if ($mark_id) {
            $this->emit('editPupilMarkLiveEvent', $mark_id);
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => "Une erreure s'est produite", 'message' => "La note est introuvable", 'type' => 'error']);
        }
    }

    public function editClasseSubjects($classe_id = null)
    {
        $school_year = session('school_year_selected');
        $classe = Classe::where('id', $classe_id)->first();
        if($classe){
            $this->emit('manageClasseSubjectsLiveEvent', $classe->id);
        }

    } 

    public function editClasseGroup($classe_id = null)
    {
        $this->emit('editClasseGroupLiveEvent', $classe_id);
    }

    

    public function insertMarks($pupil_id, $type = 'epe')
    {
        $subject_id = session('classe_subject_selected');

        if($subject_id){

            $semestre = session('semestre_selected');

            $school_year_model = $this->getSchoolYear();

            $this->emit('addNewsMarksLiveEvent', $pupil_id, $this->classe_id, $subject_id, $semestre, $school_year_model->id, $type);
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une matière en premier!", 'type' => 'error']);
        }
    }




    public function insertRelatedMark($pupil_id, $semestre = null, $school_year = null)
    {
        $subject_id = session('classe_subject_selected');

        if($subject_id){

            $semestre = session('semestre_selected');

            $school_year_model = $this->getSchoolYear();

            $this->emit('insertRelatedMarkLiveEvent', $pupil_id, $subject_id, $semestre, $school_year_model->id);
        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une matière en premier!", 'type' => 'error']);
        }
    }


    public function reloadDataFormSelectedSubjectChanged($subject_id = null)
    {
        $this->classe_subject_selected = $subject_id;

        if($subject_id){

            $this->subject_selected = Subject::find($subject_id);

            session()->put('classe_subject_selected', $subject_id);

        }
        else{
            session()->forget('classe_subject_selected');

        }

        $this->reloadData();

    }

    public function semestreWasChanged($semestre_selected = null)
    {

        session()->put('semestre_selected', $semestre_selected);

        $this->semestre_selected = $semestre_selected;
    }

    public function reloadData($data = null)
    {
        $this->count = 1;

    }


    public function updatedRelaodNow($value = true)
    {
        $this->reloadData();
    }


    public function printMarksAsExcelFile()
    {
        $classe = Classe::find($this->classe_id);

        $school_year_model = $this->getSchoolYear();

        $semestre_type = $this->semestre_type;

        $semestre = $this->semestre_selected;

        $subject = $this->subject_selected;

        if($subject && $semestre && $semestre_type){

            // $file_name = 'Les-notes-simplifiees-de-' . $subject->name. '-de-la-classe-de-' . strtoupper($classe->name) . '-du-' . $semestre_type . '-' . $semestre . '-' . $school_year_model->school_year . ' - ' . time() . '.xlsx';

            $file_name = strtoupper($classe->name) . '-' . $subject->name . '-SIMPLIFIEES.XLS';

            return Excel::download(new PupilsAveragesExports($classe, $school_year_model, $semestre, $subject, true), $file_name);


        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'REQUETE INCOMPLETE', 'message' => "Vous bien définir les données du formulaire de téléchargement, certaines données n'ont pas été renseignées!", 'type' => 'warning']);

        }
    }

    public function exportToExcelFormat()
    {
        $classe = Classe::find($this->classe_id);

        $school_year_model = $this->getSchoolYear();

        $semestre_type = $this->semestre_type;

        $semestre = $this->semestre_selected;

        $subject = $this->subject_selected;

        if($subject && $semestre && $semestre_type){

            // $file_name = 'Les-notes-completes-de-' . $subject->name. '-de-la-classe-de-' . strtoupper($classe->name) . '-du-' . $semestre_type . '-' . $semestre . '-' . $school_year_model->school_year . ' - ' . time() . '.xlsx';

            $file_name = strtoupper($classe->name) . '-' . $subject->name . '-COMPLETES.xlsx';

            return Excel::download(new PupilsAveragesExports($classe, $school_year_model, $semestre, $subject, true, true), $file_name);


        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'REQUETE INCOMPLETE', 'message' => "Vous bien définir les données du formulaire de téléchargement, certaines données n'ont pas été renseignées!", 'type' => 'warning']);

        }
    }


}
