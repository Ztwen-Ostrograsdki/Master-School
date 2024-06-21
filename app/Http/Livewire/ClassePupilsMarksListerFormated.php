<?php

namespace App\Http\Livewire;

use App\Events\UpdateClasseSanctionsEvent;
use App\Exports\ExportPupils;
use App\Exports\PupilsAveragesExports;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\Subject;
use Illuminate\Support\Facades\View;
use Livewire\Component;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class ClassePupilsMarksListerFormated extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'classeSubjectUpdated' => 'reloadData',
        'classePupilListUpdated' => 'reloadData',
        'ReloadClasseListDataAbandonLiveEvent' => 'reloadData',
        'schoolYearChangedLiveEvent' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'semestreWasChanged',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
        'UpdatedGlobalSearch' => 'reloadClasseDataOnSearch',
        'UpdatedRelaodNowLiveEvent' => 'updatedRelaodNow',
        'NewClasseMarksInsert' => 'reloadData',
        'InitiateClasseDataUpdatingLiveEvent' => 'loadingDataStart',
        'ClasseDataLoadedSuccessfully' => 'dataWasLoaded',
        'ClasseDisplayingRankUpdatedLiveEvent' => 'reloadRankToFetch',
        'selectedClasseSubjectChangeLiveEvent' => 'reloadDataFormSelectedSubjectChanged',
        'PrintSingleMarksAsExcelFileLiveEvent' => 'printSingleMarksAsExcelFile',
        'ClasseDataWasUpdated' => 'reloadData',
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

        $devMaxLenght = 1;

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


            $devMaxLenght = $classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_yea, 'devoir');


            if($devMaxLenght <= 2){

                $devMaxLenght = 2;
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

        return view('livewire.classe-pupils-marks-lister-formated', 
                    compact(
                        'classe',
                        'current_period',
                        'pupils', 'marks', 'devMaxLenght', 'noMarks', 'modality', 'modalitiesActivated', 'hasModalities', 'averageEPETab', 'averageTab', 'classe_subject_coef', 'ranksTab', 'classe_subjects', 'school_year_model', 'printing',
                    )
                );
    }



    public function displayRank()
    {
        $this->computedRank = true;
    }

    public function hideRank()
    {
        $this->computedRank = false;
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


    public function reloadDataFormSelectedSubjectChanged($subject_id)
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


    public function refreshClasseMarks($classe_id)
    {
        if ($classe_id) {

            $classe = Classe::find($classe_id);
        }
        else{
            $classe = Classe::whereSlug($this->slug)->first();
        }
        if ($classe) {

            $school_year_model = $this->getSchoolYear();

            $semestre = $this->semestre_selected;

            if (session()->has('semestre_selected') && session('semestre_selected')) {

                $semestre = session('semestre_selected');
            }

            $subject_id = session('classe_subject_selected');

            $this->emit('ThrowClasseMarksDeleterLiveEvent', $classe->id, $school_year_model->id, $semestre, $subject_id);

        }
        
    }


    public function activated($classe_id)
    {

        $user = auth()->user();

        $school_year_model = $this->getSchoolYear();

        $subject = $this->subject_selected;

        $semestre = session('semestre_selected');

        $classe = $school_year_model->findClasse($classe_id);

        UpdateClasseSanctionsEvent::dispatch($classe, $user, $school_year_model, $semestre, $subject, true);
    }


    public function desactivated($classe_id)
    {
        $user = auth()->user();

        $school_year_model = $this->getSchoolYear();

        $subject = $this->subject_selected;

        $semestre = session('semestre_selected');

        $classe = $school_year_model->findClasse($classe_id);

        UpdateClasseSanctionsEvent::dispatch($classe, $user, $school_year_model, $semestre, $subject, false);
    }


    public function printSingleMarksAsExcelFile()
    {
        $classe = Classe::find($this->classe_id);

        $school_year_model = $this->getSchoolYear();

        $semestre_type = $this->semestre_type;

        $semestre = $this->semestre_selected;

        $subject = $this->subject_selected;

        if($subject && $semestre && $semestre_type){

            // $file_name = 'Les-notes-de-' . $subject->name. '-de-la-classe-de-' . strtoupper($classe->name) . '-du-' . $semestre_type . '-' . $semestre . '-' . $school_year_model->school_year . ' - ' . time() . '.xlsx';

            $file_name = strtoupper($classe->name) . '-' . $subject->name . '-SIMPLIFIEES.XLS';


            return Excel::download(new PupilsAveragesExports($classe, $school_year_model, $semestre, $subject), $file_name);


        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'REQUETE INCOMPLETE', 'message' => "Vous bien définir les données du formulaire de téléchargement, certaines données n'ont pas été renseignées!", 'type' => 'warning']);

        }
    }


   
}

