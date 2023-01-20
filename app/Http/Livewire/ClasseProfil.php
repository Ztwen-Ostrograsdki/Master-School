<?php

namespace App\Http\Livewire;
use App\Models\Mark;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\School;
use Livewire\Component;
use App\Helpers\ModelsHelpers\ModelQueryTrait;

class ClasseProfil extends Component
{

    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'newLevelCreated' => 'reloadClasseData'
    ];

    public $slug;
    public $classe_id;
    public $classeName;
    public $counter = 0;
    public $editingClasseName = false;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $classe_subject_selected;
    public $semestre_selected = 1;
    public $classe_subjects = [];
    public $search = null;

    public function mount($slug = null)
    {
        $this->school_year_model = $this->getSchoolYear();
        if($slug){
            $this->slug = $slug;
        }
        else{
            return abort(404);
        }
    }


    public function updatedSearch($value)
    {
        $this->search = $value;
        if(strlen($value) > 2){
            $this->emit('UpdatedClasseListOnSearch', $value);
        }
        else{
            $this->emit('UpdatedClasseListOnSearch', null);
        }
    }


    public function resetSearch()
    {
        $this->reset('search');
        $this->emit('UpdatedClasseListOnSearch', null);
    }

    public function render()
    {
        $school = School::first();
        $semestres = [1, 2];
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
        $pupils = [];
        $allClasses = Classe::where('slug', urldecode($this->slug))->get();
        
        $classe = $school_year_model->classes()->where('slug', urldecode($this->slug))->first();

        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }
        else{
            $this->semestre_selected = 1;
            session()->put('semestre_selected', $this->semestre_selected);
        }


        if(session()->has('semestre_type') && session('semestre_type')){
            $semestre_type = session('semestre_type');
            session()->put('semestre_type', $semestre_type);
            $this->semestre_type = $semestre_type;
        }
        else{
            session()->put('semestre_type', $this->semestre_type);
        }


        if($allClasses->count() > 0){
            $classe_name = $allClasses->first()->name;
            session()->put('classe_selected', $classe_name);
            if($classe){
                $pupils = $classe->getClassePupils();
            }
                 
        }

        else{
            return abort(404, 'Cette classe est inexistante');
        }
        return view('livewire.classe-profil', compact('classe', 'pupils', 'semestres'));
    }


    public function editClasseSubjects($classe_id = null)
    {
        $school_year = session('school_year_selected');
        $classe = Classe::where('id', $classe_id)->first();
        if($classe){
            $this->emit('manageClasseSubjectsLiveEvent', $classe->id);
        }

    }
    
    public function editClasseName($classe_id)
    {
        $classe = Classe::where('id', $classe_id)->first();
        $this->classe_id = $classe->id;
        $this->classeName = $classe->name;
        $this->editingClasseName = true;
    }

    public function cancelEditingName()
    {
        $this->editingClasseName = false;
    }
    
    public function updateClasseName()
    {
        $classeNameHasBeenTaken = Classe::where('name', $this->classeName)->first();
        $classe = Classe::where('id', $this->classe_id)->first();
        if(!$classeNameHasBeenTaken && $classe){
            $c = $classe->update(
                [
                    'name' => $this->classeName,
                    'slug' => str_replace(' ', '-', $this->classeName),
                ]
            );
            if($c){
                $this->reset('editingClasseName');
                $this->resetErrorBag();
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès!", 'type' => 'success']);
                return redirect()->route('classe_profil', [urlencode($classe->slug)]);
            }
        }
    }




    public function deleteAllPupil($classe_id)
    {
        $school_year_model = $this->getSchoolYear();
        $classe = Classe::where('id', $classe_id)->first();
        if($classe){
            $pupils = $classe->getClassePupils();
            if(count($pupils) > 0){
                foreach($pupils as $pupil){
                    $marks = $pupil->marks;
                    if(count($marks) > 0){
                        foreach($marks as $mark){
                            if($mark->school_year() && $mark->school_year()->school_year == $school_year_model->school_year){
                                $school_year_model->marks()->detach($mark->id);
                                $mark->forceDelete();
                            }
                            
                        }
                    }
                    $school_year_model->pupils()->detach($pupil->id);

                    $order_school_years = $pupil->school_years;
                    if(!$order_school_years){
                        $pupil->forceDelete();
                    }
                }
            }
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès!", 'type' => 'success']);
        $this->emit('classeUpdated');
    }

    public function joinClasseToSchoolYear($classe_name = null)
    {
        $school_year_model = $this->getSchoolYear();
        $classe = Classe::where('slug', urldecode($this->slug))->first();
        $yet = $school_year_model->classes()->where('slug', urldecode($this->slug))->first();
        if(!$yet && $classe){
            $school_year_model->classes()->attach($classe->id);
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Elle est désormais disponible en $school_year_model->school_year !", 'type' => 'success']);
            $this->emit('classeUpdated');
        }
    }


    public function resetAbsences($classe_id = null)
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

            $classe->resetAllAbsences($school_year_model->id, $semestre, $subject_id);
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Les absences ont été rafraîchies!", 'type' => 'success']);
        $this->emit('classeUpdated');

        $this->makePresence = true;

    }



    public function resetLates($classe_id = null)
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

            $classe->resetAllLates($school_year_model->id, $semestre, $subject_id);
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Les retards ont été rafraîchies!", 'type' => 'success']);
        $this->counter = 1;

    }


    public function resetMarks($school_year = null, $semestre = null, $subject_id = null, $type = null, $classe_id = null)
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

            $done = $classe->resetAllMarks($school_year_model->id, $semestre, $subject_id, $type);
            if($done){
                $this->emit('classeUpdated');
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Les notes ont été rafraîchies!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Le rafraichissement des notes de la classe  $classe->name a échoué. Veuillez réessayer!", 'type' => 'error']);
            }
            $this->emit('classeUpdated');
        }
        
    }

    public function refreshAllMarks($classe_id = null)
    {
        if ($classe_id) {
            $classe = Classe::find($classe_id);
        }
        else{
            $classe = Classe::whereSlug($this->slug)->first();
        }
        if ($classe) {
            $done = $classe->resetAllMarks(null, null, null, null);
            if($done){
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "Toutes les notes de la $classe->name de toutes les années ont été rafraîchies!", 'type' => 'success']);
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Le rafraichissement des notes de la classe  $classe->name a échoué. Veuillez réessayer!", 'type' => 'error']);
            }
            $this->emit('classeUpdated');
        }
        
    }




    public function createNewClasse()
    {
        $this->emit('createNewClasseLiveEvent');
    }

    public function editClasseGroup($classe_id)
    {
        $this->emit('editClasseGroupLiveEvent', $classe_id);
    }


    public function reloadClasseData($school_year = null)
    {
        $this->counter = 1;
    }

    public function changeSemestre()
    {
        $this->count = 1;
        session()->put('semestre_selected', $this->semestre_selected);
        $this->emit('semestreWasChanged', $this->semestre_selected);
    }


    public function setClasseProfilActiveSection($section)
    {
        session()->put('classe_profil_section_selected', $section);
    }
}
