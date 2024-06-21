<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InsertPupilMarks extends Component
{
    protected $listeners = ['addNewsMarksLiveEvent' => 'addNewMarks'];
    public $classe_id;
    public $pupil_id;
    public $subject_id;
    public $marks;
    public $epe_marks;
    public $dev_marks;
    public $type = 'epe';
    public $mark_index;
    public $epe_mark_index;
    public $dev_mark_index;
    public $semestre_id = 1;
    public $pupil;
    public $subject;
    public $pupilName = 'Elève';
    public $classe;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $subjects = [];
    public $classe_subject_selected;

    use ModelQueryTrait;

    public function render()
    {
        $types_of_marks = [
            'devoir' => 'Devoirs',
            'epe' => 'Interrogations',
            'participation' => 'Participations'

        ];
        $semestres = [1, 2];
        $school = School::first();
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'Trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        $school_years = SchoolYear::orderBy('school_year', 'desc')->get();

        return view('livewire.insert-pupil-marks', compact('semestres', 'school_years', 'types_of_marks'));
    }


    public function addNewMarks(int $pupil_id, int $classe_id, int $subject_id, int $semestre, int $school_year = null, $type = 'epe')
    {
        if($subject_id && $pupil_id && $classe_id){

            $school_year_model = $this->getSchoolYear();

            $classe = $school_year_model->classes()->where('classes.id', $classe_id)->first();

            if($classe){

                $user = auth()->user();

                $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);

                if($not_secure){

                    $subject = Subject::find($subject_id);

                    $pupil = $school_year_model->pupils()->where('pupils.id', $pupil_id)->first();

                    if($pupil && $semestre && $subject){

                        $has_marks_index = $school_year_model->marks()->where('pupil_id', $pupil_id)->where('classe_id', $classe_id)->where('subject_id', $subject_id)->where('semestre', $semestre)->where('type', $type)->pluck('mark_index')->toArray();


                        if(count($has_marks_index) > 0){

                            $mark_index = max($has_marks_index) + 1;
                        }
                        else{

                            $mark_index = 1;
                        }

                        $this->pupil = $pupil;

                        $this->subject = $subject;

                        $this->pupilName = $pupil->getName();

                        $this->semestre_id = $semestre;

                        $this->subject_id = $subject_id;

                        $this->classe_id = $classe_id;

                        $this->school_year = $school_year;

                        $this->mark_index = $mark_index;

                        $this->type = $type;

                        $semestre_id = $this->semestre_id;

                        $mark_stopped_1 = $classe->classeMarksWasStoppedForThisSchoolYear($semestre_id, $subject_id);

                        $mark_stopped_2 = $classe->classeMarksWasStoppedForThisSchoolYear();

                        if(! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id) && ! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id, session('semestre_selected'))){

                            if(!$mark_stopped_1 && !$mark_stopped_2){

                                $this->dispatchBrowserEvent('modal-insertPupilMarks');

                            }
                            else{

                                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "ARRET NOTE", 'message' => "Aucune action n'est possible sur les notes de cette classe!", 'type' => 'info']);

                            }
                        }
                        else{

                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "ARRET NOTE", 'message' => "Aucune action n'est possible sur les notes de cette classe!", 'type' => 'info']);

                        }


                        
                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos données sont ambigües, nous n'avons trouvé aucun apprenant et ou la matière correspondant(e)!", 'type' => 'error']);
                    }

                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment!", 'type' => 'warning']);

                }

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure classe', 'message' => "Vos données sont ambigües, la classe est introuvable!", 'type' => 'warning']);
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Veuillez sélectionner une classe, un apprenant et une matière valides", 'type' => 'warning']);

        }
    }


    public function submitMarks()
    {
        $school_year = $this->school_year;

        $semestre = $this->semestre_id;

        $subject_id = $this->subject_id;

        $classe_id = $this->classe_id;

        $type = $this->type;

        $epe_marks = $this->epe_marks;

        $dev_marks = $this->dev_marks;

        $pupil = $this->pupil;

        $mark_index = $this->mark_index;

        $school_year_model = $this->getSchoolYear($school_year);

        $marksInsertionWasStopped = $school_year_model->marksWasAlreadyStopped($semestre);

        if(!$marksInsertionWasStopped || auth()->user()->isAdminAs('master')){

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($this->classe_id);

            if($not_secure){

                if($school_year && $subject_id && $classe_id && $semestre && $type && ($epe_marks || $dev_marks)){

                    $epes = [];

                    $devs = [];

                    if($epe_marks){
                        $epes = explode('-', $epe_marks);
                    } 

                    if($dev_marks){
                        $devs = explode('-', $dev_marks);
                    }
                    
                    $tabs = [];

                    $epe_tabs = [];

                    $dev_tabs = [];

                    $key_index = $this->mark_index;

                    $epe_key_index = 1;

                    $dev_key_index = 1;

                    
                    if($epes !== []){
                        $has_epe_marks_index = $school_year_model->marks()
                                                                 ->where('pupil_id', $this->pupil->id)
                                                                 ->where('classe_id', $classe_id)
                                                                 ->where('subject_id', $subject_id)
                                                                 ->where('semestre', $semestre)
                                                                 ->where('type', 'epe')
                                                                 ->pluck('mark_index')
                                                                 ->toArray();

                        if(count($has_epe_marks_index) > 0){

                            $this->epe_mark_index = max($has_epe_marks_index) + 1;
                        }
                        else{

                           $this->epe_mark_index = 1;
                        }

                        $epe_key_index = $this->epe_mark_index;

                        foreach($epes as $epe){

                            $mark_index_was_existed = $pupil->marks()
                                                            ->where('classe_id', $classe_id)
                                                            ->where('subject_id', $subject_id)
                                                            ->where('semestre', $semestre)->where('type', "epe")
                                                            ->where('mark_index', $epe_key_index)
                                                            ->first();

                            if($mark_index_was_existed){

                                if($mark_index_was_existed->school_years()->first()->id == $school_year_model->id){

                                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "L'index $epe_key_index de la note est déjà existante!", 'type' => 'warning']);
                                }

                            }
                            else{
                                $epe_tabs[$epe_key_index] = floatval($epe);

                                if(!is_numeric($epe)){

                                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres!", 'type' => 'error']);
                                }
                                elseif(floatval($epe) > 20 || floatval($epe) < 0){

                                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres compris entre 00 et 20!", 'type' => 'error']);
                                }
                                $epe_key_index++;
                            }
                            
                        }

                    }

                    if($devs !== []){
                        $has_dev_marks_index = $school_year_model->marks()
                                                                 ->where('pupil_id', $this->pupil->id)
                                                                 ->where('classe_id', $classe_id)
                                                                 ->where('subject_id', $subject_id)
                                                                 ->where('semestre', $semestre)
                                                                 ->where('type', 'devoir')
                                                                 ->pluck('mark_index')
                                                                 ->toArray();

                        if(count($has_dev_marks_index) > 0){
                            $this->dev_mark_index = max($has_dev_marks_index) + 1;
                        }
                        else{
                           $this->dev_mark_index = 1;
                        }

                        $dev_key_index = $this->dev_mark_index;

                        foreach($devs as $dev){

                            $mark_index_was_existed = $pupil->marks()
                                                            ->where('classe_id', $classe_id)->where('subject_id', $subject_id)
                                                            ->where('semestre', $semestre)
                                                            ->where('type', "devoir")
                                                            ->where('mark_index', $dev_key_index)
                                                            ->first();

                            if($mark_index_was_existed){

                                if($mark_index_was_existed->school_years()->first()->id == $school_year_model->id){

                                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "L'index $dev_key_index de la note est déjà existante!", 'type' => 'warning']);
                                }

                            }
                            else{

                                $dev_tabs[$dev_key_index] = floatval($dev);

                                if(!is_numeric($dev)){

                                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres!", 'type' => 'error']);
                                }
                                elseif(floatval($dev) > 20 || floatval($dev) < 0){

                                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres compris entre 00 et 20!", 'type' => 'error']);
                                }

                                $dev_key_index++;
                            }
                            
                        }
                    }

                    if($epe_tabs !== []){

                        $make = DB::transaction(function($e) use ($epe_tabs, $pupil, $subject_id, $semestre, $classe_id, $school_year_model){
                            
                            foreach($epe_tabs as $epe_k_index => $validEpe){

                                DB::transaction(function($e) use ($validEpe, $pupil, $subject_id, $semestre, $classe_id, $school_year_model, $epe_k_index){

                                    $epe_mark = Mark::create([
                                        'value' => $validEpe, 
                                        'pupil_id' => $pupil->id, 
                                        'user_id' => auth()->user()->id, 
                                        'creator' => auth()->user()->id, 
                                        'subject_id' => $subject_id, 
                                        'school_year_id' => $school_year_model->id, 
                                        'classe_id' => $classe_id, 
                                        'semestre' => $semestre, 
                                        'type' => 'epe', 
                                        'mark_index' => $epe_k_index, 
                                        'level_id' => $pupil->level_id, 
                                    ]);

                                    if ($epe_mark) {

                                        $school_year_model->marks()->attach($epe_mark->id);
                                    }

                                });
                            }
                        });
                    }

                    if($dev_tabs !== []){
                            $make = DB::transaction(function($e) use ($dev_tabs, $pupil, $subject_id, $semestre, $classe_id, $school_year_model){
                                
                                foreach($dev_tabs as $dev_k_index => $validDev){

                                    DB::transaction(function($e) use ($validDev, $pupil, $subject_id, $semestre, $classe_id, $school_year_model, $dev_k_index){
                                        $dev_mark = Mark::create([
                                            'value' => $validDev, 
                                            'pupil_id' => $pupil->id, 
                                            'user_id' => auth()->user()->id, 
                                            'creator' => auth()->user()->id, 
                                            'school_year_id' => $school_year_model->id, 
                                            'subject_id' => $subject_id, 
                                            'classe_id' => $classe_id, 
                                            'semestre' => $semestre, 
                                            'type' => 'devoir', 
                                            'mark_index' => $dev_k_index, 
                                            'level_id' => $pupil->level_id, 
                                        ]);
                                        if ($dev_mark) {

                                            $school_year_model->marks()->attach($dev_mark->id);
                                        }

                                    });
                                }
                            });
                        }

                    DB::afterCommit(function(){

                        $this->emit('pupilUpdated');

                        $this->emit('classeUpdated');

                        $this->dispatchBrowserEvent('hide-form');

                        $this->reset('epe_marks', 'dev_marks', 'epe_mark_index', 'dev_mark_index');

                        $this->resetErrorBag();

                    });
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Au moins les données de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des données valides!", 'type' => 'warning']);
                }
            }

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'SEMESTRE DEJA FERME OU INDISPONIBLE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment, il est possible que le semestre ait déjà été cloturé (arrêt des notes) ou qu'il n'ait pas encore démarré ou que le programme n'est pas encore été pré-défini!", 'type' => 'info']);

        }

    }
}
