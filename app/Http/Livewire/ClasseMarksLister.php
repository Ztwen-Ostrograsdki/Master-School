<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
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
    public $semestre_type = 'semestre';
    public $school_year;
    public $classe_subject_selected;
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


        $school_year_model = $this->getSchoolYear();
        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
        $this->classe = $classe;
        $this->classe_subjects = $classe->subjects;

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }

        if(session()->has('classe_subject_selected') && session('classe_subject_selected')){
            $subject_id = intval(session('classe_subject_selected'));
            if(in_array($subject_id, $this->classe_subjects->pluck('id')->toArray())){
                session()->put('classe_subject_selected', $subject_id);
                $this->classe_subject_selected = $subject_id;
            }
        }
        if($classe){
            $pupils = $classe->getPupils($school_year_model->id);
        }

        $marks = $this->classe->getMarks($this->classe_subject_selected, $this->semestre_selected, $school_year_model->school_year);

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



        return view('livewire.classe-marks-lister', compact('pupils', 'marks', 'epeMaxLenght', 'devMaxLenght', 'participMaxLenght', 'noMarks', 'marks_lenght'));
    }


    public function setTargetedMark($pupil_id, $mark_id)
    {
        if ($mark_id && $pupil_id) {
            $this->emit('editPupilMarkLiveEvent', $pupil_id, $mark_id);
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => "Une erreure s'est produite", 'message' => "La note est introuvable", 'type' => 'error']);
        }
    }


    public function updateMark()
    {
        $edit_mark_value = $this->edit_mark_value;
        if($this->targetedMark){
            $mark = $this->targetedMark;
            if(is_numeric($edit_mark_value) && $edit_mark_value >= 0 && $edit_mark_value <= 20){
                $updated = $mark->update(['value' => $edit_mark_value]);
                if($updated){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "la note a été inséré avec succès!", 'type' => 'success']);
                    $this->reset('edit_mark_value', 'edit_mark_type', 'edit_key', 'invalid_mark', 'editing_mark');
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => "Une erreure s'est produite", 'message' => "L'erreure est inconnue veuillez réessayer!", 'type' => 'error']);
                    $this->reset('edit_mark_value', 'edit_mark_type', 'edit_key', 'invalid_mark', 'editing_mark');

                }
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => "La note est invalide", 'message' => "La note doit être un nombre compris entre 00 et 20", 'type' => 'warning']);
                $this->invalid_mark = true;
            }
        }
        else{
            if(is_numeric($edit_mark_value) && $edit_mark_value >= 0 && $edit_mark_value <= 20){
                $pupil = Pupil::find($this->pupil_id);
                if ($pupil) {
                    $subject_id = session('classe_subject_selected');
                    $semestre = session('semestre_selected');
                    $classe_id = $pupil->classe_id;
                    $school_year_model = $this->getSchoolYear();
                    $value = $edit_mark_value;
                    $edit_mark_type = $this->edit_mark_type;

                    $mark = Mark::create([
                        'value' => $value, 
                        'pupil_id' => $pupil->id, 
                        'subject_id' => $subject_id, 
                        'classe_id' => $classe_id, 
                        'semestre' => $semestre, 
                        'type' => $edit_mark_type, 
                        'level_id' => $pupil->level_id, 
                    ]);
                    if ($mark) {
                        $school_year_model->marks()->attach($mark->id);
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "la note a été inséré avec succès!", 'type' => 'success']);

                        $this->reset('edit_mark_value', 'edit_mark_type', 'edit_key', 'invalid_mark', 'editing_mark');
                    }
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => "Une erreure s'est produite", 'message' => "L'apprenant est introuvable", 'type' => 'error']);
                }
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => "La note est invalide", 'message' => "La note doit être un nombre compris entre 00 et 20", 'type' => 'warning']);
                $this->invalid_mark = true;
            }

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

    public function insertMarks($pupil_id)
    {
        $subject_id = session('classe_subject_selected');
        if($subject_id){
            $semestre = session('semestre_selected');
            $classe_id = $this->classe_id;
            $school_year_model = $this->getSchoolYear();
            $this->emit('addNewsMarksLiveEvent', $pupil_id, $classe_id, $subject_id, $semestre, $school_year_model->id);
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
