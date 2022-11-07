<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\SchoolYear;
use Livewire\Component;

class ClassePupilRelatedMark extends Component
{

    use ModelQueryTrait;
    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
    ];
    
    
    public $classe_id;

    public $counter = 0;
    public $selected;
    public $selectedAction;


    public function render()
    {
        $school_year = session('school_year_selected');
        $school_year_model = $this->getSchoolYear();

        $classe = $school_year_model->classes()->where('classes.id', $this->classe_id)->first();
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

        return view('livewire.classe-pupil-related-mark', compact('classe', 'pupils', 'classe_subject_selected'));
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


    public function deleteAllRelatedMarks()
    {
        $subject_id = session('classe_subject_selected');
        if($subject_id){
            $semestre = session('semestre_selected');
            $school_year_model = $this->getSchoolYear();
            $classe = Classe::find($this->classe_id);
            if($classe){
                $del = $classe->deleteAllRelatedMarks($subject_id, $semestre, $school_year_model->id);
                if($del){
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Supression réussie', 'message' => "Les notes ont été bien rafraîchi!", 'type' => 'success']);
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une erreure inconnue s'est produite. Veuillez réessayer!", 'type' => 'error']);
                }
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une classe valider!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une matière en premier!", 'type' => 'error']);
        }
    }

    public function deleteAllPupilRelatedMarks($pupil_id)
    {
        if($pupil_id){
            $subject_id = session('classe_subject_selected');
            if($subject_id){
                $semestre = session('semestre_selected');
                $school_year_model = $this->getSchoolYear();
                $pupil = Pupil::find($pupil_id);
                if($pupil){
                    $del = $pupil->deleteAllPupilRelatedMarks($this->classe_id, $subject_id, $semestre, $school_year_model->id);
                    if($del){
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Supression réussie', 'message' => "Les notes ont été bien rafraîchi!", 'type' => 'success']);
                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Une erreure inconnue s'est produite. Veuillez réessayer!", 'type' => 'error']);
                    }
                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'apprenant est introuvable!", 'type' => 'error']);
                }
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une matière en premier!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une classe valide en premier!", 'type' => 'error']);
        }
    }


    public function reloadClasseData($school_year = null)
    {
        $this->counter = 1;
    }

}
