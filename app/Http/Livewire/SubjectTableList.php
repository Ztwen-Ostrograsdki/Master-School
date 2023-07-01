<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClasseGroup;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class SubjectTableList extends Component
{
    protected $listeners = ['schoolYearChangedLiveEvent' => 'reloadData', 'subjectDataUpdated' => 'reloadData'];

    public $counter = 0;
    public $classe_group_id = null;
    public $level_id = null;
    public $selecteds = [];
    public $search = '';
    public $title = 'Le prof...';


    use ModelQueryTrait;



    public function render()
    {
        $subjects = [];
        $school_year_model = $this->getSchoolYear();

        $levels = $school_year_model->levels;

        if($this->search && mb_strlen($this->search) > 2){
            $subjects = $this->getSubjectsOnSearch();
            $classe_groups = $school_year_model->classe_groups;
        }
        else{
            if($this->level_id){
                $classe_groups = $school_year_model->classe_groups()->where('classe_groups.level_id', $this->level_id)->get();
            }
            else{
                $classe_groups = $school_year_model->classe_groups;
            }

            if($this->classe_group_id || $this->level_id){
                $subjects = $this->getSubjects();
            }
            else{
                $subjects = $school_year_model->subjects;
            }
        }

       

        return view('livewire.subject-table-list', compact('subjects', 'levels', 'classe_groups'));
    }



    public function resetSelectedData()
    {
        $this->reset('classe_group_id', 'level_id');
    }

    public function updatedLevelId($level_id)
    {
        $this->level_id = $level_id;
        $this->reset('classe_group_id');
    }

    public function updatedClasseGroupId($classe_group_id)
    {
        $this->classe_group_id = $classe_group_id;
    }

    public function updatedSearch($search)
    {
        $this->search = $search;
    }


    public function getSubjectsOnSearch()
    {
        $school_year_model = $this->getSchoolYear();
        $search = $this->search;
        $target = '%' . $search . '%';

        $subjects = [];

        $teachers = $school_year_model->teachers()->where('teachers.name', 'like', $target)->orWhere('teachers.surname', 'like', $target)->pluck('teachers.id')->toArray();
        if(count($teachers) > 0){
            foreach($teachers as $teacher){
                if($teachers->ae){
                    $subjects[$teacher->ae->subject_id] = $teacher->ae->subject;
                }
            }
        }

        $data = $school_year_model->subjects()->where('subjects.name', 'like', $target)->get();

        if(count($data) > 0){
            foreach($data as $subject){
                $subjects[$subject->id] = $subject;
            }
        }

        return $subjects;

    }


    public function getSubjects()
    {
        $school_year_model = $this->getSchoolYear();
        $subjects = [];

        $levels = $school_year_model->levels;

        if($this->level_id){
            if($this->classe_group_id){
                $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id)->first();
                if($classe_group){
                    $subjects = $classe_group->subjects;
                }
            }
            else{
                $subjects = $school_year_model->subjects()->where('subjects.level_id', $this->level_id)->get();
            }
        }
        elseif($this->classe_group_id){
            $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id)->first();
            if($classe_group){
                $subjects = $classe_group->subjects;
            }
        }
        return $subjects;
    }

    public function addNewSubject()
    {
        $this->emit('createNewSubjectLiveEvent');
    }

    public function updateSubject($subject_id)
    {
        $this->emit('UpdateSubjectDataLiveEvent', $subject_id);
    }



    public function reloadData()
    {
        $this->counter = rand(1, 7);
    }


    public function deleteSubject($subject_id)
    {
        $school_year_model = $this->getSchoolYear();
        $subject = $school_year_model->subjects()->where('subject_id', $subject_id)->first();
        if($subject){
            // $school_year_model->subjects()->detach($subject_id);
        }
    }


    public function retrieveAE($subject_id)
    {
        $school_year_model = $this->getSchoolYear();
        $subject = $school_year_model->subjects()->where('subject_id', $subject_id)->first();
        DB::transaction(function($e) use($school_year_model, $subject){
            if($subject){
                $ae = $subject->ae;
                if($ae){
                    if($ae->school_year_id == $school_year_model->id){
                        $school_year_model->aes()->detach($ae->id);
                        if($ae->delete()){
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à lour réussie', 'message' => "L'AE de cette matière a été retiré avec succès!", 'type' => 'success']);
                            $this->refreshData();
                        }
                        else{
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE ', 'message' => "La mise à jour a échoué!", 'type' => 'error']);
                        }
                    }
                    else{
                        if($ae->delete()){
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à lour réussie', 'message' => "L'AE de cette matière a été retiré avec succès!", 'type' => 'success']);
                            $this->refreshData();
                        }
                        else{
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE ', 'message' => "La mise à jour a échoué!", 'type' => 'error']);
                        }
                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE ', 'message' => "La mise à jour a échoué!", 'type' => 'error']);
                }
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "La mise à jour est impossible, car la requête semble ambigües!", 'type' => 'error']);
            }
        });
    }
}

