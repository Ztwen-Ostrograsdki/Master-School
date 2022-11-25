<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\Subject;
use Livewire\Component;

class ClasseMarksLister extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'classeSubjectUpdated' => 'reloadData',
        'classePupilListUpdated' => 'reloadData',
        'classeUpdated' => 'reloadData',
        'semestreWasChanged',
    ];
    public $classe_id;
    public $pupil_id;
    public $classe;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $classe_subject_selected;
    public $subject_selected;
    public $semestre_selected = 1;
    public $classe_subjects = [];
    public $classe_marks = [];
    public $edit_mark_value = 0;
    public $edit_mark_type = 'epe';
    public $editing_mark = false;
    public $invalid_mark = false;
    public $edit_key;
    public $mark_key;
    public $targetedMark;
    public $count = 0;

    public function mount()
    {

    }

    public function render()
    {
        $pupils = [];
        $noMarks = false;
        $modality = null;
        $modalitiesActivated = null;
        $hasModalities = false;


        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        $this->classe = $classe;
        $this->classe_subjects = $classe->subjects;

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
            if(in_array($subject_id, $this->classe_subjects->pluck('id')->toArray())){
                session()->put('classe_subject_selected', $subject_id);
                $this->classe_subject_selected = $subject_id;
                $this->subject_selected = Subject::find($this->classe_subject_selected);
            }
        }

        if($classe){
            $pupils = $classe->getPupils($school_year_model->id);
        }

        $marks = $this->classe->getMarks($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year);

        $averageEPETab = $this->classe->getMarksAverage($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year, 'epe');

        $averageTab = $this->classe->getAverage($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year);
        
        $ranksTab = $this->classe->getClasseRank($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year);

        $classe_subject_coef = $this->classe->get_coefs($this->classe_subject_selected, $school_year_model->id, true);


        $epeMaxLenght = $this->classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_yea, 'epe') + 1;

        $devMaxLenght = $this->classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_yea, 'devoir') + 1;

        $participMaxLenght = $this->classe->getMarksTypeLenght($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_yea, 'participation') + 1;

        $marks_lenght = Mark::withTrashed('deleted_at')->get()->count();

        if(($epeMaxLenght < 1 && $participMaxLenght < 1 && $devMaxLenght < 1)){
            $noMarks = true;
        }

        if($epeMaxLenght < 2){
            $epeMaxLenght = 2;
        }
        if($participMaxLenght < 2){
            $participMaxLenght = 2;
        }
        if($devMaxLenght < 2){
            $devMaxLenght = 2;
        }
        if(!($epeMaxLenght && $devMaxLenght && $participMaxLenght)){
            $noMarks = true;
        }

        if($classe && $this->semestre_selected && $this->subject_selected){
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



        return view('livewire.classe-marks-lister', 
                    compact(
                        'pupils', 'marks', 'epeMaxLenght', 'devMaxLenght', 'participMaxLenght', 'noMarks', 'marks_lenght', 'modality', 'modalitiesActivated', 'hasModalities', 'averageEPETab', 'averageTab', 'classe_subject_coef', 'ranksTab'
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

        $this->emit('manageClasseModalitiesLiveEvent', $classe_id, $subject_id, $school_year_model->id, $semestre, $modality);

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
        $classe_id = $this->classe_id;
        $subject_id = session('classe_subject_selected');
        $semestre = session('semestre_selected');

        $modalities = $this->classe->averageModalities()->where('school_year', $school_year_model->school_year)->where('semestre', $semestre);

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

    public function insertMarks($pupil_id)
    {
        $subject_id = session('classe_subject_selected');
        if($subject_id){
            $semestre = session('semestre_selected');
            $school_year_model = $this->getSchoolYear();
            $this->emit('addNewsMarksLiveEvent', $pupil_id, $this->classe_id, $subject_id, $semestre, $school_year_model->id);
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


    public function changeSubject()
    {
        $this->count = 1;
        session()->put('classe_subject_selected', $this->classe_subject_selected);
        $this->subject_selected = Subject::find($this->classe_subject_selected);
        $this->emit('selectedClasseSubjectChangeLiveEvent', $this->classe_subject_selected);
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
}
