<?php

namespace App\Http\Livewire;
use App\Events\FreshAveragesIntoDBEvent;
use App\Events\UpdateClasseAveragesIntoDatabaseEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobUpdateClasseAllSemestresAverageIntoDatabase;
use App\Jobs\JobUpdateClasseAnnualAverageIntoDatabase;
use App\Jobs\JobUpdateClasseSemestrialAverageIntoDatabase;
use App\Models\Classe;
use App\Models\ClassePupilSchoolYear;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\Responsible;
use App\Models\School;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class ClasseProfil extends Component
{

    use ModelQueryTrait;

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadClasseData',
        'classePupilListUpdated' => 'reloadClasseData',
        'classeUpdated' => 'reloadClasseData',
        'newLevelCreated' => 'reloadClasseData',
        'timePlanTablesWasUpdatedLiveEvent' => 'reloadClasseData',
        'NewClasseMarksInsert' => 'reloadClasseData',
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
    public $search = null;

    public $section_selected = 'liste';

    public $sections = [
        'liste' => 'Liste des apprenants', 
        'time_plan' => 'Emploi du temps', 
        'marks' => 'Les notes', 
        'related_marks' => 'Les Participations', 
        'lates_absences' => 'Les absences / retards', 
        'classe_general_stats' => 'Tableau des stats', 
        'averages' => 'Les moyennes'
    ];

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


    public function updatedSemestreSelected($semestre)
    {
        $this->semestre_selected = $semestre;

        session()->put('semestre_selected', $semestre);

        $this->emit('semestreWasChanged', $semestre);
    }


    public function updatedSectionSelected($section)
    {
        $this->section_selected = $section;

        session()->put('classe_profil_section_selected', $section);

    }


    public function resetSearch()
    {
        $this->reset('search');

        $this->emit('UpdatedClasseListOnSearch', null);
    }

    public function render()
    {


        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $this->semestre_type = 'trimestre';

        }

        $school_year_model = $this->getSchoolYear();

        $school_year = session('school_year_selected');

        $pupils = [];

        $classesToShow = [];

        $classeSelf = Classe::where('slug', urldecode($this->slug))->first();
        
        $classe = $school_year_model->classes()->where('slug', urldecode($this->slug))->first();

        if($classe){

            $this->classe_id = $classe->id;

            $classesToShow[] = $classe;
        }

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


        if(session()->has('classe_profil_section_selected') && session('classe_profil_section_selected')){

            $section_selected = session('classe_profil_section_selected');

            session()->put('classe_profil_section_selected', $section_selected);

            $this->section_selected = $section_selected;
        }
        else{

            session()->put('classe_profil_section_selected', $this->section_selected);
        }


        if($classeSelf){

            $this->classe_id = $classeSelf->id;

            $classe_name = $classeSelf->name;

            session()->put('classe_selected', $classe_name);

            if($classe){

                $pupils = $classe->getClassePupils();
            }
            else{

                $classesToShow = [];

            }


        }
        else{

            $msg = "La classe " . urldecode($this->slug) . " est si possible inexistante ou a déjà été supprimée!";

            return abort(404, $msg);
        }

        return view('livewire.classe-profil', compact('classe', 'pupils', 'semestres', 'classeSelf', 'classesToShow', 'school_year_model'));
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


    public function settingsOnMarks($classe_id = null)
    {
        if($classe_id == null){
            $classe_id = $this->classe_id;
        }
        $classe = Classe::where('id', $this->classe_id)->first();
        $this->emit('onMarksSettingsEvent', $classe_id, session('semestre_selected'), session('classe_subject_selected'));
    }


    public function destroyPupil($pupil_id)
    {
        $school_year_model = $this->getSchoolYear();

        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $pupil->pupilDestroyer($school_year_model->id, true);
        }

        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "L'apprenant a été supprimé définitivement!", 'type' => 'success']);

        $this->emit('classeUpdated');

    }




    public function deleteAllPupil($classe_id)
    {
        $school_year_model = $this->getSchoolYear();

        $classe = Classe::where('id', $classe_id)->first();

        if($classe){

            $pupils = $classe->getClassePupils();

            if(count($pupils) > 0){

                foreach($pupils as $pupil){

                    $pupil->pupilDeleter($school_year_model->id, false);

                    
                }
            }
        }

        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès!", 'type' => 'success']);
        $this->emit('classeUpdated');
    }

    public function joinClasseToSchoolYear($classe_id)
    {
        $school_year_model = $this->getSchoolYear();

        $classe = Classe::find($classe_id);

        $yet = $school_year_model->classes()->where('classes.id', $classe_id)->first();

        DB::transaction(function($e) use ($yet, $school_year_model, $classe){

            if(!$yet && $classe){

                $school_year_model->classes()->attach($classe->id);

                Responsible::create(['school_year_id' => $school_year_model->id, 'classe_id' => $classe->id]);

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Elle est désormais disponible en $school_year_model->school_year !", 'type' => 'success']);

                $this->emit('classeUpdated');
            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ATTENTION', 'message' => "Cette  $classe->name semble déjà être liée à l'année-scolaire $school_year_model->school_year !", 'type' => 'warning']);

                $this->emit('classeUpdated');
            }

        });

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

            $classe->deleteClasseAbsences($semestre, $school_year_model->id, $subject_id);
        }
        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Les absences ont été rafraîchies!", 'type' => 'success']);

        $this->emit('classeUpdated');

        $this->makePresence = true;

    }



    public function resetLates($classe_id = null)
    {
        $school_year_model = $this->getSchoolYear();

        if ($classe_id) {

            $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();
        }
        else{

            $classe = $school_year_model->classes()->whereSlug($this->slug)->first();
        }
        if ($classe) {

            $school_year_model = $this->getSchoolYear();

            $semestre = $this->semestre_selected;

            if (session()->has('semestre_selected') && session('semestre_selected')) {

                $semestre = session('semestre_selected');
            }

            $subject_id = session('classe_subject_selected');

            $classe->deleteClasseLates($semestre, $school_year_model->id, $subject_id);
        }

        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise à jour terminée', 'message' => "la classe  $classe->name a été mise à jour avec succès! Les retards ont été rafraîchies!", 'type' => 'success']);

        $this->counter = 1;

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

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe->id);

            if($not_secure){

                $this->emit('ThrowClasseMarksDeleterLiveEvent', $classe->id, $school_year_model->id, $semestre, $subject_id);

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE TEMPORAIREMENT', 'message' => "Vous ne pouvez pas supprimer les notes!", 'type' => 'warning']);
            }

        }
        
    } 

    public function restorMarks($classe_id)
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

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe->id);

            if($not_secure){

                $this->emit('ThrowMarksRestorationLiveEvent', $classe->id, $school_year_model->id, $semestre, $subject_id);

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE TEMPORAIREMENT', 'message' => "Vous ne pouvez pas supprimer les notes!", 'type' => 'warning']);
            }

        }
        
    }


    public function deleteClasseTimePlans($classe_id = null)
    {
        $school_year_model = $this->getSchoolYear();

        $classe_id = $this->classe_id;

        if($classe_id){

            $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

            if($classe){

                $classe_name = $classe->name;

                DB::transaction(function($e) use($classe, $school_year_model){

                    $times_plans = $classe->timePlans()->where('time_plans.school_year_id', $school_year_model->id)->each(function($time_plan){

                        $time_plan->delete();

                    });
                });

                DB::afterCommit(function() use($classe_name, $classe){

                    $this->dispatchBrowserEvent('Toast', ['type' => 'success', 'title' => 'SUPPRESSION REUSSIE',  'message' => "Les emplois du temps de la classe de $classe_name ont été rafraîchies avec succès!"]);

                    $this->emit('RefreshTimePlanIntoClasseProfilLiveEvent', $classe->id);

                    $this->reloadClasseData();

                });

            }
            else{

                $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE INTROUVABLE', 'message' => "Cette classe est inconnue ou a été supprimé ou bloqué momentanement!", 'type' => 'warning']);
            }
        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE INTROUVABLE', 'message' => "Cette classe est inconnue ou a été supprimé ou bloqué momentanement!", 'type' => 'warning']);
        }

        
            
    }


    public function addTimePlan()
    {
        $school_year_model = $this->getSchoolYear();

        $quotas =  $school_year_model->qotHours;

        if(count($quotas) > 0){

            $this->emit('AddNewTimePlanForThisClasse', $this->classe_id);

        }
        else{

            $this->dispatchBrowserEvent('Toast', ['title' => 'QUOTA HORAIRE PAS ENCORE DEFINI', 'message' => "Les quotas horaires n'ont pas encore été définis! Veuillez insérer les quotas horaires en premier avant l'insertion d'un emploi du temps !", 'type' => 'warning']);

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

    public function insertClasseMarks()
    {
        $this->emit('InsertClassePupilsMarksTogetherLiveEvent', $this->classe_id);
    }


    public function reloadClasseData($school_year = null)
    {
        $this->counter = rand(1, 14);
    }


    public function optimizeSemestrialAverageFromDatabase($classe_id, $semestre = 1)
    {
        $semestre = session('semestre_selected');

        if($semestre){

            $classe = Classe::find($classe_id);

            $user = auth()->user();

            if($classe && $user){

                $school_year_model = $this->getSchoolYear();

                FreshAveragesIntoDBEvent::dispatch($user, $classe, $school_year_model, $semestre);
                
            }

        }
        else{

            $semestre_type = strtoupper($this->semestre_type);

            $this->dispatchBrowserEvent('Toast', ['title' => "semestre_type INCONNU", 'message' => "Veuillez sélectionner d'abord le $semestre_type dont vous voudriez charger les données!", 'type' => 'warning']);


        }

    }

    public function optimizeClasseAveragesIntoDatabase($classe_id)
    {
        $classe = Classe::find($classe_id);

        $semestres = $this->getSemestres();

        $user = auth()->user();


        if($classe && $semestres){

            $school_year_model = $this->getSchoolYear();

            FreshAveragesIntoDBEvent::dispatch($user, $classe, $school_year_model, null);
            
        }

    }

    public function addNewsPupils($classe_id)
    {
        $school_year = session('school_year_selected');

        $school_year_model = $this->getSchoolYear($school_year);

        $classe = $school_year_model->findClasse($classe_id);

        if($classe){

            $this->emit('insertMultiplePupils', $classe->id);
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
        }

    }


    public function importPupilsIntoClasse($classe_id)
    {
        $this->emit('ImportPupilsIntoClasse', $classe_id);
    }


    public function movePupilFromThisClasse($classe_id)
    {
        // $this->emit('ImportPupilsIntoClasse', $classe_id);
    }


}
