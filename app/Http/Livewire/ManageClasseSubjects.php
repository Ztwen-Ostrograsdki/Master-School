<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Subject;
use Livewire\Component;

class ManageClasseSubjects extends Component
{
    use ModelQueryTrait;


    protected $listeners = ['manageClasseSubjectsLiveEvent'];
    public $classe_id;
    public $school_year_model;
    public $school_year;
    public $subjects = [];
    public $classe_subjects = [];
    public $classe_subjects_tabs = [];
    public $counter = 0;

    protected $rules = [
        'classe_subjects' => 'required'
    ];

    public function mount()
    {
        $this->school_year_model = $this->getSchoolYear();
        $this->school_year = $this->school_year_model->school_year;
    }

    public function render()
    {
        return view('livewire.manage-classe-subjects');
    }


    public function manageClasseSubjectsLiveEvent($classe_id)
    {
        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
        $this->classe = $classe;
        $this->subjects = Subject::where('level_id', $classe->level_id)->get();
        foreach($this->subjects as $s){
            $this->classe_subjects_tabs[$s->id] = $s->name;
        }
        $this->classe_subjects = $classe->subjects->pluck('id')->toArray();
        $this->dispatchBrowserEvent('modal-manageClasseSubjects');
    }



    public function changeSubjects($subject_id)
    {
        $this->counter = 1;
    }

    public function removeSubject($subject_id)
    {
        $this->counter = 1;
    }
    public function submit()
    {
        $this->validate();
        $subjects = [];

        foreach($this->classe_subjects as $subject_id){
            $s = Subject::find($subject_id);
            if($s){
                $subjects[] = $s;
            }
        }

        foreach($subjects as $subject){
            if(!in_array($subject->id, $this->classe->subjects->pluck('id')->toArray())){
                $this->classe->subjects()->attach($subject->id);
            }
        }
        $this->dispatchBrowserEvent('hide-form');
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "La liste des matières de cette a été mise à jour avec succès!", 'type' => 'success']);
        $this->emit('classeSubjectUpdated');

    }
}
