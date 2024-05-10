<?php

namespace App\Http\Livewire;

use App\Events\InitiateClasseParticipationMarksEvent;
use App\Events\UpdateClasseSanctionsEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\Subject;
use Livewire\Component;
use PDF;

class ClasseMarksHeaderComponent extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'classeSubjectUpdated' => 'reloadData',
        'classePupilListUpdated' => 'reloadData',
        'schoolYearChangedLiveEvent' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'semestreWasChanged',
        'UpdatedClasseListOnSearch' => 'reloadClasseDataOnSearch',
        'UpdatedGlobalSearch' => 'reloadClasseDataOnSearch',
        'UpdatedRelaodNowLiveEvent' => 'updatedRelaodNow',
        'NewClasseMarksInsert' => 'reloadData',
        'InitiateClasseDataUpdatingLiveEvent' => 'loadingDataStart',
        'ClasseDataLoadedSuccessfully' => 'dataWasLoaded',
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


    public function mount()
    {

    }


    public function showFormattedView($classe_id = null)
    {

        if(is_null($classe_id)){

            $classe_id = $this->classe_id;

        }

        if($this->simpleFormat == true){

            $this->simpleFormat = false;

            $this->emit('ClasseProfilSectionSelectedChangedLiveEvent', 'marks');

        }
        else{

            $this->simpleFormat = true;

            $this->emit('ClasseProfilSectionSelectedChangedLiveEvent', 'simple_classe_marks_view');

        }

    }


    public function updateClassePupilsPersoDataFromFile($classe_id)
    {
        $this->emit('UpdateClassePupilsPersoDataFromFile', $classe_id);
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

    public function render()
    {
        $pupils = [];

        $marks = [];

        $printing = false;

        $modality = null;

        $modalitiesActivated = null;

        $hasModalities = false;

        $classe_subject_coef = 1;

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

            $pupils = $classe->getPupils($school_year_model->id, $this->search);

            $classe_subject_coef = $classe->get_coefs($this->classe_subject_selected, $school_year_model->id, true);


            if($this->semestre_selected && $this->subject_selected){

                $semestre = $this->semestre_selected;

                $marks = $classe->getMarks($this->classe_subject_selected, $this->semestre_selected, 2, $school_year_model->school_year);

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

        return view('livewire.classe-marks-header-component', 
                    compact(
                        'classe',
                        'current_period',
                        'marks', 'pupils',
                        'modality', 'modalitiesActivated', 'hasModalities', 'classe_subject_coef', 'classe_subjects', 'school_year_model', 'printing'
                    )
                );
    }



    public function displayRank()
    {
        $this->computedRank = true;

        $this->emit('ClasseDisplayingRankUpdatedLiveEvent', $this->computedRank);

        session()->put('display_classe_pupils_ranks', $this->computedRank);
    }

    public function hideRank()
    {
        $this->computedRank = false;

        $this->emit('ClasseDisplayingRankUpdatedLiveEvent', $this->computedRank);

        session()->put('display_classe_pupils_ranks', $this->computedRank);
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

    public function manageModality($classe_id = null, $subject_id = null, $semestre = null, $modality_id = null)
    {
        $school_year_model = $this->getSchoolYear();

        if($classe_id == null){
            $classe_id = $this->classe_id;
        }
        if($subject_id == null){
            $subject_id = session('classe_subject_selected');
        }
        if($semestre == null){
            $semestre = session('semestre_selected');
        }

        $mod = $this->subject_selected->getAverageModalityOf($classe_id, $school_year_model->school_year, $semestre);
        $modality = $mod ? $mod->id : null;

        $this->emit('manageClasseModalitiesLiveEvent', $classe_id, $this->subject_selected->id, $school_year_model->id, $semestre, $modality);

    }

    public function activateModalities()
    {
        $this->activateModalityOrNot(true);
    }


    public function diseableModalities()
    {
        $this->activateModalityOrNot(false);
    }

    public function editClasseGroup($classe_id = null)
    {
        $this->emit('editClasseGroupLiveEvent', $classe_id);
    }

    public function activateModalityOrNot($activated)
    {

        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);

        $subject_id = session('classe_subject_selected');

        $semestre = session('semestre_selected');

        $modalities = $classe->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre);

        if($modalities->get()->count() > 0){

            $updated = $modalities->each(function($modality) use ($activated){

                $modality->update(['activated' => $activated]);
            });

            if($updated){

                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour', 'message' => "C'est fait!", 'type' => 'success']);

                $this->emit('classeUpdated');
            }
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


    public function insertClasseMarks()
    {
        $this->emit('InsertClassePupilsMarksTogetherLiveEvent', $this->classe_id);
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


    public function updatedClasseSubjectSelected($subject_id)
    {
        $this->classe_subject_selected = $subject_id;

        if($subject_id !== null){

            $this->subject_selected = Subject::find($subject_id);

            session()->put('classe_subject_selected', $subject_id);

            $this->emit('selectedClasseSubjectChangeLiveEvent', $subject_id);
        }
        elseif(is_null($subject_id) || $subject_id == ""){

            $this->classe_subject_selected = null;

            session()->forget('classe_subject_selected');

            $this->emit('selectedClasseSubjectChangeLiveEvent', null);

        }

        
    }

    public function updateParticipatesClasseMarks()
    {

        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->findClasse($this->classe_id);

        $subject_id = session('classe_subject_selected');

        $semestre = session('semestre_selected');


        if($classe && $subject_id && $semestre){

            $subject = Subject::find($subject_id);

            $user = auth()->user();

            // $pupils = $classe->getNotAbandonnedPupils();

            // $tabs = [];


            // if(count($pupils) > 0){

            //     foreach($pupils as $pupil){

            //         $part = $pupil->definedParticipationMark($semestre, $subject_id, $school_year_model->school_year);

            //         $tabs[$pupil->getName()] = $part;


            //     }

            //     dd($tabs);


            // }


            InitiateClasseParticipationMarksEvent::dispatch($classe, $school_year_model, $semestre, $subject, $user);

            $this->dispatchBrowserEvent('Toast', ['title' => 'PROCESSUS LANCE', 'message' => "Les notes de Participations sont en cours de traitement...!", 'type' => 'success']);



        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'ERREURE', 'message' => "Votre formulaire semble incomplet!", 'type' => 'error']);

        }


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
}

