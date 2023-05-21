<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\Pupil;
use App\Models\School;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class PupilProfil extends Component
{
    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadPupilData',
        'classePupilListUpdated' => 'reloadPupilData',
        'pupilUpdated' => 'reloadPupilData',
        'updatedImages' => 'reloadPupilData',
    ];

    public $slug;
    public $pupil_id;
    public $classe;
    public $classe_id;
    public $pupilName;
    public $counter = 0;
    public $editingPupilName = false;
    public $semestre_type = 'semestre';
    public $school_year;
    public $classe_subject_selected;
    public $semestre_selected = 1;
    public $joinedToThisYear = false;




    public function mount(int $id)
    {
        $pupil_id = $id;
        if ($pupil_id) {
            $pupil = Pupil::find($pupil_id);
            if($pupil){
                $this->pupil_id = $pupil_id;
            }
            else{
                return abort(404);
            }
        }
        else{
            return abort(404);
        }
    }



    public function render()
    {
        $school = School::find(1);
        $semestres = [1, 2];
        $classes = [];
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        $school_year_model = $this->getSchoolYear();
        $school_year = session('school_year_selected');

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }
        else{
            $this->semestre_selected = 1;
            session()->put('semestre_selected', $this->semestre_selected);
        }
        $pupil_id = $this->pupil_id;
        if($pupil_id){
            $pupil = Pupil::find($pupil_id);
            if($pupil){
                $best = $pupil->getBestSubject($this->semestre_selected);
                $marks_counter = $pupil->getMarksCounter($this->semestre_selected);
                $succeeds_marks_counter = $pupil->getSucceedsMarksCounter($this->semestre_selected);
                $joined = $pupil->school_years()->where('school_years.id', $school_year_model->id)->first();
                $classes = Classe::where('classes.level_id', $pupil->level_id)->get();
                if($joined){
                    $this->joinedToThisYear = true;
                }
                else{
                    $this->joinedToThisYear = false;
                }

            }
            else{
                $pupil = null;
            }
        }
        return view('livewire.pupil-profil', compact('semestres', 'pupil', 'classes', 'marks_counter', 'succeeds_marks_counter', 'best'));
    }

    

    public function editPupilName($pupil_id = null)
    {
        $classe = Pupil::find($this->pupil_id);
        $this->pupilName = $classe->firstName;
        $this->editingPupilName = true;
    }

    public function cancelEditingName()
    {
        $this->editingPupilName = false;
    }
    
    public function updatePupilName()
    {
        $classeNameHasBeenTaken = Pupil::where('firstName', $this->pupilName)->first();
        $classe = Pupil::find($this->pupil_id);
        if(!$classeNameHasBeenTaken && $classe){
            
            if(true){
                $this->reset('editingPupilName');
                $this->resetErrorBag();
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "....a été mise à jour avec succès!", 'type' => 'success']);
            }
        }
    }


    public function resetAbsences($allyears = false)
    {
        $pupil = Pupil::find($this->pupil_id);
        if ($pupil) {
            $school_year_model = $this->getSchoolYear();
            $semestre = $this->semestre_selected;
            if (session()->has('semestre_selected') && session('semestre_selected')) {
                $semestre = session('semestre_selected');
            }

            $subject_id = session('classe_subject_selected');
            $a = $pupil->resetAllAbsences($school_year_model->id, $semestre, $subject_id, $allyears);
            if($a){
                $this->emit('pupilUpdated', $pupil->id);
                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Les absences de $pupil->getName() ont été rafraîchies avec succès!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Le rafraichissement des absences de $pupil->getName() a échoué!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'apprenant est introuvable!", 'type' => 'error']);
        }
        

    }



    public function resetLates($allyears = false)
    {
        $pupil = Pupil::find($this->pupil_id);
        if ($pupil) {
            $school_year_model = $this->getSchoolYear();
            $semestre = $this->semestre_selected;
            if (session()->has('semestre_selected') && session('semestre_selected')) {
                $semestre = session('semestre_selected');
            }

            $subject_id = session('classe_subject_selected');
            $l = $pupil->resetAllLates($school_year_model->id, $semestre, $subject_id, $allyears);
            if($l){
                $this->emit('pupilUpdated', $pupil->id);
                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Les retards de $pupil->getName() ont été rafraîchies avec succès!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Le rafraichissement des retards de $pupil->getName() a échoué!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'apprenant est introuvable!", 'type' => 'error']);

        }
        
    }

    public function joinPupilToSchoolYear()
    {
        if($this->classe_id){
            $classe = Classe::find($this->classe_id);
            if($classe){
                $school_year_model = $this->getSchoolYear();
                $pupil = Pupil::find($this->pupil_id);
                $year_yet = $school_year_model->pupils()->where('pupils.id', $this->pupil_id)->first();
                $classe_yet = $classe->pupils()->where('pupils.id', $this->pupil_id)->first();
                if(!$year_yet && !$classe_yet && $pupil){
                    DB::transaction(function($e) use ($school_year_model, $pupil, $classe){
                        if($classe->alreadyJoinedToThisYear($school_year_model->id)){
                            
                            $joinedToClasseAndSchoolYear = ClassePupilSchoolYear::create(
                                [
                                    'classe_id' => $classe->id,
                                    'pupil_id' => $pupil->id,
                                    'school_year_id' => $school_year_model->id,
                                ]
                            );

                            if($joinedToClasseAndSchoolYear){
                                $school_year_model->pupils()->attach($pupil->id);
                                $classe->classePupils()->attach($pupil->id);
                            }
                           
                            DB::afterCommit(function() use($school_year_model){
                                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "L'apprenant a été mise à jour avec succès! Cet apprenant est désormais disponible en $school_year_model->school_year !", 'type' => 'success']);
                            });
                        }
                        else{
                            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Il semble que vous être entrain de vouloir lier une classe qui n'est pas encore liéée à l'année scolaire sélectionnée!", 'type' => 'error']);
                        }
                        
                    });

                    $this->emit('pupilUpdated');

                }
                else{
                    $this->dispatchBrowserEvent('Toast', ['title' => 'Action amigüe', 'message' => "Il semble que vous être entrain de vouloir repéter des requêtes!", 'type' => 'error']);

                }
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "la classe renseignée est introuvable", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Veuillez sélectionner une classe valide", 'type' => 'warning']);
        }
    }


    public function resetMarks($allyears = false)
    {
        $pupil = Pupil::find($this->pupil_id);
        if ($pupil) {
            $school_year_model = $this->getSchoolYear();
            $semestre = $this->semestre_selected;
            if (session()->has('semestre_selected') && session('semestre_selected')) {
                $semestre = session('semestre_selected');
            }
            $subject_id = session('classe_subject_selected');

            $done = $pupil->resetAllMarks($school_year_model->id, $semestre, $subject_id, $type, $allyears);
            if($done){
                $this->emit('pupilUpdated', $pupil->id);
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "Les notes de $pupil->getName() ont été rafraîchies!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Le rafraichissement des notes  $pupil->getName() a échoué. Veuillez réessayer!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "L'apprenant est introuvable!", 'type' => 'error']);

        }
        
    }


    public function changeSchoolYear($school_year_selected)
    {
        $this->emit("schoolYearChangedLiveEvent", $school_year_selected);
        $this->emit("schoolYearChangedExternallyLiveEvent", $school_year_selected);
        $this->reloadPupilData();
    }


    
    public function editPupilPersoData()
    {
        $this->emit('editPupilPersoDataLiveEvent', $this->pupil_id);
    }
    
    public function editPupilProfilImage()
    {
        $class = "App\Models\Pupil";
        $this->emit('editImageEvent', $this->pupil_id, $class);
    }


    public function reloadPupilData($school_year = null)
    {
        $this->counter = 1;
    }

    public function changeSemestre()
    {
        $this->count = 1;
        session()->put('semestre_selected', $this->semestre_selected);
        $this->emit('semestreWasChanged', $this->semestre_selected);
    }


    public function setPupilProfilActiveSection($section)
    {
        session()->put('pupil_profil_section_selected', $section);
    }
}



