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

        $classe = $school_year_model->findClasse($this->classe_id);

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

    public function makeRelatedMarkTogether($classe_id = null, $semestre = null, $school_year = null, $together = true)
    {
        $classe_id ? $id = $classe_id : $id = $this->classe_id;

        $subject_id = session('classe_subject_selected');

        if($subject_id){

            $semestre = session('semestre_selected');

            $school_year_model = $this->getSchoolYear();

            $this->emit('insertRelatedMarkLiveEvent', $id, $subject_id, $semestre, $school_year_model->id, $together);
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Vous devez choisir une matière en premier!", 'type' => 'error']);
        }
    }


    
    public function refreshClasseRelatedsMarks($classe_id)
    {
        if ($classe_id) {

            $classe = Classe::find($classe_id);
        }
        else{
            $classe = Classe::whereSlug($this->slug)->first();
        }
        if ($classe) {

            $school_year_model = $this->getSchoolYear();

            if (session()->has('semestre_selected') && session('semestre_selected')) {

                $semestre = session('semestre_selected');
            }

            $subject_id = session('classe_subject_selected');

            if($semestre && $subject_id){

                $this->emit('ThrowClasseMarksDeleterLiveEvent', $classe->id, $school_year_model->id, $semestre, $subject_id, 'bonus');
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'FORMULAIRE EST INVALIDE', 'message' => "Le formulaire n'est pas valide et ne peut être soumis!", 'type' => 'error']);
            }


        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE INTROUVABLE', 'message' => "La classe est introuvable!", 'type' => 'error']);
        }
        
    }

    public function refreshPupilRelatedsMarks($pupil_id)
    {
        if ($pupil_id) {

            $pupil = Pupil::find($pupil_id);
        }
        if ($pupil) {

            $school_year_model = $this->getSchoolYear();

            $classe = $school_year_model->findClasse($this->classe_id);

            if (session()->has('semestre_selected') && session('semestre_selected')) {

                $semestre = session('semestre_selected');
            }

            $subject_id = session('classe_subject_selected');

            if($semestre && $subject_id && $classe && $pupil){

                $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe->id);

                if($not_secure){

                    $this->emit('ThrowClasseMarksDeleterLiveEvent', $classe->id, $school_year_model->id, $semestre, $subject_id, 'bonus', $pupil->id);

                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE TEMPORAIREMENT', 'message' => "Vous ne pouvez pas supprimer les notes!", 'type' => 'warning']);
                }

            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'FORMULAIRE EST INVALIDE', 'message' => "Le formulaire n'est pas valide et ne peut être soumis!", 'type' => 'error']);
            }


        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE INTROUVABLE', 'message' => "La classe est introuvable!", 'type' => 'error']);
        }
        
    }


    public function reloadClasseData($school_year = null)
    {
        $this->counter = 1;
    }

}
