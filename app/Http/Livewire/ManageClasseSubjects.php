<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ManageClasseSubjects extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'manageClasseSubjectsLiveEvent', 
        'manageClasseGroupSubjectsLiveEvent',
        'classeSubjectUpdated' => 'reloadData',
        'classeGroupSubjectsUpdated' => 'reloadData',
    ];
    public $classe_id;
    public $onClasseGroup = false;
    public $classe_group_id;
    public $classe_group;
    public $target;
    public $classe;
    public $school_year_model;
    public $school_year;
    public $counter = 0;
    public $title = '';

    protected $rules = [
        'subjects_selecteds' => 'required'
    ];

    public function render()
    {
        $subjects_selecteds = [];
        $subjects = [];
        if($this->target){
            $subjects = Subject::where('level_id', $this->target->level_id)->get();
            $subjects_selecteds = $this->target->subjects->pluck('id')->toArray();
        }
        return view('livewire.manage-classe-subjects', compact('subjects_selecteds', 'subjects'));
    }


    public function manageClasseSubjectsLiveEvent($classe_id)
    {
        $this->school_year_model = $this->getSchoolYear();
        if($classe_id && $this->school_year_model){
            $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
            $this->classe = $classe;
            $this->target = $this->classe;
            $this->title = "Classe de " . $classe->name;
            $this->dispatchBrowserEvent('modal-manageClasseSubjects');
        }   
    }

    public function manageClasseGroupSubjectsLiveEvent($classe_group_id)
    {
        $this->onClasseGroup = true;
        if($classe_group_id){
            $classe_group = ClasseGroup::find($classe_group_id);
            $this->title = "Promotion de " . $classe_group->name;
            $this->classe_group = $classe_group;
            $this->target = $this->classe_group;
            $this->dispatchBrowserEvent('modal-manageClasseSubjects');
        }   
    }

   public function removeSubject($subject_id)
    {
        $subjects_selecteds = [];

        if($this->subjects_selecteds){
            foreach ($this->subjects_selecteds as $subject) {
                if(intval($subject) !== intval($subject_id)){
                    $subjects_selecteds[] = $subject;
                }
            }
        }
        $this->subjects_selecteds = $subjects_selecteds;
    }



    public function join($subject_id)
    {
        $subject = Subject::find($subject_id);
        if($subject){
            if($this->onClasseGroup){
                if($this->classe_group && $this->classe_group->level_id == $subject->level_id){
                    if(!in_array($subject->id, $this->classe_group->subjects()->pluck('subjects.id')->toArray())){
                        $joined = $this->classe_group->subjects()->attach($subject->id);
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La liste des matières de cette promotion a été mise à jour avec succès!", 'type' => 'success']);
                            $this->emit('classeGroupSubjectsUpdated');
                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Cette matière est déjà liée à la promotion!", 'type' => 'question']);

                    }
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter car cette matière n'est pas compatible avec cette promotion!", 'type' => 'error']);

                }

            }
            elseif(!$this->onClasseGroup){
                if($this->classe && $this->classe->level_id == $subject->level_id){
                    if(!in_array($subject->id, $this->classe->subjects()->pluck('subjects.id')->toArray())){
                        $joined = $this->classe->subjects()->attach($subject->id);
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La liste des matières de cette classe a été mise à jour avec succès!", 'type' => 'success']);
                            $this->emit('classeSubjectUpdated');

                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Cette matière est déjà liée à la classe!", 'type' => 'question']);

                    }
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter car cette matière n'est pas compatible avec cette classe!", 'type' => 'error']);
                }
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter, veuillez sélectionner une matière valide!", 'type' => 'error']);

        }
    }


    public function disjoin($subject_id)
    {
        $subject = Subject::find($subject_id);
        if($subject){
            if($this->onClasseGroup){
                if($this->classe_group && $this->classe_group->level_id == $subject->level_id){
                    if(in_array($subject->id, $this->classe_group->subjects()->pluck('subjects.id')->toArray())){
                        $joined = $this->classe_group->subjects()->detach($subject->id);
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La liste des matières de cette promotion a été mise à jour avec succès!", 'type' => 'success']);
                            $this->emit('classeGroupSubjectsUpdated');

                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Cette matirère est n'est pas liée à la promotion!", 'type' => 'question']);

                    }
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter car cette matière n'est pas compatible avec cette promotion!", 'type' => 'error']);

                }

            }
            elseif(!$this->onClasseGroup){
                if($this->classe && $this->classe->level_id == $subject->level_id){
                    if(in_array($subject->id, $this->classe->subjects()->pluck('subjects.id')->toArray())){
                        $joined = $this->classe->subjects()->attach($subject->id);
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La liste des matières de cette classe a été mise à jour avec succès!", 'type' => 'success']);
                            $this->emit('classeSubjectUpdated');

                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Cette matirère n'est pas liée à la classe!", 'type' => 'question']);

                    }
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter car cette matière n'est pas compatible avec cette classe!", 'type' => 'error']);
                }
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreur', 'message' => "Votre requête ne peut être traiter, veuillez sélectionner une matière valide!", 'type' => 'error']);

        }

    }


    public function reloadData()
    {
        $this->counter = 1;
    }


    public function hideForm()
    {
        $this->dispatchBrowserEvent('hide-form');
    }
}
