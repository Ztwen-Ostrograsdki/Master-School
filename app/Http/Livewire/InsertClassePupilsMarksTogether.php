<?php

namespace App\Http\Livewire;

use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Jobs\JobInsertClassePupilMarksTogether;
use App\Models\Classe;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InsertClassePupilsMarksTogether extends Component
{
    protected $listeners = ['InsertClassePupilsMarksTogetherLiveEvent' => 'openModal'];

    public $classe_id;

    public $targeted_pupil;

    public $subject_id;

    public $pupils = [];

    public $subjects = [];

    public $marks = [];

    public $epe_marks;

    public $participation_marks;

    public $dev_marks;

    public $type = 'epe-devoir';

    public $semestre_id = 1;

    public $subject;

    public $title = "Insertion de nouvelles notes de classe";

    public $classe;

    public $semestre_type = 'Semestre';

    public $school_year;


    use ModelQueryTrait;


    protected $rules = ['subject_id' => 'required|int', 'type' => 'required|string'];

    public function render()
    {
        $types_of_marks = [
            'devoir' => 'Devoirs',
            'epe' => 'Interrogations',
            'participation' => 'Participations'

        ];

        $subjects = [];

        $semestres = $this->getSemestres();

        if(count($semestres) == 3){

            $this->semestre_type = 'Trimestre';
        }

        $school_years = SchoolYear::orderBy('school_year', 'desc')->get();

        return view('livewire.insert-classe-pupils-marks-together', compact('semestres', 'school_years', 'types_of_marks'));
    }


    public function updatedSubjectId($subject_id)
    {
        $subject = Subject::find($subject_id);

        $this->subject = $subject;

    }


    public function updatedType($type)
    {
        $this->type = $type;

        $marks = $this->marks;

        if(in_array($type, ['epe', 'epe-devoir', 'devoir', 'participation'])){

            if($marks !== []){

                foreach($marks as $p_id => $p_ms){

                    $epes = $p_ms['epe'];

                    $devs = $p_ms['devoir'];

                    $parts = $p_ms['participation'];


                    if($this->type == 'epe'){

                        unset($devs);

                        unset($parts);
                    }
                    elseif($this->type == 'epe-devoir'){

                        unset($parts);

                    }
                    elseif($this->type == 'devoir'){

                        unset($epes);

                        unset($parts);

                    }
                    elseif($this->type == 'participation'){

                        unset($devs);

                        unset($epes);

                    }
                    else{


                    }


                }

            }
        }
        else{

            $this->addError('type', "Type invalide");

            // $this->reset('epe_marks', 'dev_marks', 'participation_marks');

        }
        
    }


    public function openModal(int $classe_id)
    {

        if($classe_id){

            $school_year_model = $this->getSchoolYear();

            $classe = $school_year_model->findClasse($classe_id);

            if($classe){

                $this->classe = $classe;

                $user = auth()->user();

                $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);


                $this->pupils = $this->classe->getNotAbandonnedPupils();

                $this->subjects = $this->classe->subjects;

                if($this->pupils){

                    if($not_secure || ($user->isAdminAs('master'))){

                        if(auth()->user()->teacher){

                            $teacher = auth()->user()->teacher;

                            $subject = $teacher->speciality();

                            $this->subject = $subject;

                            $this->subject_id = $subject->id;

                            $this->semestre_id = session('semestre_selected');

                        }
                        elseif($user->isAdminAs('master')){

                            $subject = $this->subjects->first();

                            $this->subject = $subject;

                            $this->subject_id = $subject->id;

                            $this->semestre_id = session('semestre_selected');

                        }

                        $this->classe_id = $classe_id;

                        $this->school_year = $school_year_model->school_year;

                        $this->dispatchBrowserEvent('modal-insertClassePupilsMarksTogether');

                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment!", 'type' => 'warning']);

                    }
                }
                else{

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VIDE', 'message' => "Vous ne pouvez pas insérer de notes: la classe est vide!", 'type' => 'warning']);


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

        if($this->subject_id && $this->marks && $this->semestre_id){

            $school_year = $this->school_year;

            $semestre = $this->semestre_id;

            $subject_id = $this->subject_id;

            $classe_id = $this->classe_id;

            $classe = $this->classe;

            $school_year_model = $this->getSchoolYear($school_year);

            $subject = $this->subject;

            $user = auth()->user();

            $marksInsertionWasStopped = $school_year_model->marksWasAlreadyStopped($semestre);

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($this->classe_id);

            if(!$marksInsertionWasStopped || auth()->user()->isAdminAs('master')){
                
                $marks = $this->marks;

                if($not_secure){

                    $data = ['user' => $user, 'classe' => $classe, 'subject' => $subject, 'marks' => $marks, 'semestre' => $semestre, 'school_year_model' => $school_year_model];


                    if($marks !== []){

                        ClasseMarksInsertionCreatedEvent::dispatch($data);

                        $this->dispatchBrowserEvent('hide-form');

                        $this->reset('classe_id', 'semestre_id', 'subject_id', 'subject', 'marks', 'school_year', 'classe', 'targeted_pupil');



                    }


                    // $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'NOTES SOUMISES', 'message' => "Les notes ont été soumises avec succès!", 'type' => 'success']);

                   
                   
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ACCES REFUSE', 'message' => "Vous ne pouvez effectuer une telle requête!", 'type' => 'error']);

                }

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'SEMESTRE DEJA FERME OU INDISPONIBLE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment, il est possible que le semestre ait déjà été cloturé (arrêt des notes) ou qu'il n'ait pas encore démarré ou que le programme n'est pas encore été pré-défini!", 'type' => 'info']);

            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE MATIERE', 'message' => "Veuillez préciser la matière!", 'type' => 'error']);
        }

    }


    public function pushIntoMarks($pupil_id)
    {
        $this->reset('targeted_pupil');

        $marks = $this->marks;

        $error = false;

        if($this->epe_marks || $this->dev_marks || $this->participation_marks){

            $epe_marks = explode('-', $this->epe_marks);

            $dev_marks = explode('-', $this->dev_marks);

            $participation_marks = explode('-', $this->participation_marks);

            if($epe_marks !== []){

                foreach($epe_marks as $epe){

                    if($epe && !is_numeric($epe)){

                        $error = true;
                    }
                    elseif($epe && (floatval($epe) > 20 || floatval($epe) < 0)){

                        $error = true;
                    }

                }
            }


            if($dev_marks !== []){

                foreach($dev_marks as $dev){

                    if($dev && !is_numeric($dev)){

                        $error = true;
                    }
                    elseif($dev && (floatval($dev) > 20 || floatval($dev) < 0)){

                        $error = true;
                    }

                }

            }

            if($participation_marks !== []){

                if(count($participation_marks) <= 1){

                    foreach($participation_marks as $part){

                        if($part && !is_numeric($part)){

                            $error = true;
                        }
                        elseif($part && (floatval($part) > 20 || floatval($part) < 0)){

                            $error = true;
                        }

                    }

                }
                else{

                    $error = true;

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'NOTES INVALIDES', 'message' => "Chaque apprenant doit avoir au plus UNE SEULE NOTE de participation. PAS PLUS!", 'type' => 'warning']);

                }

            }

            if(!$error){

                if(isset($marks[$pupil_id]) && array_key_exists($pupil_id, $marks)){

                    unset($marks[$pupil_id]);

                    $marks[$pupil_id] = [
                        'epe' => $this->epe_marks, 
                        'devoir' => $this->dev_marks, 
                        'participation' => $this->participation_marks
                    ];

                }
                else{

                    $marks[$pupil_id] = [
                        'epe' => $this->epe_marks, 
                        'devoir' => $this->dev_marks, 
                        'participation' => $this->participation_marks
                    ];

                }

                $this->marks = $marks;

                $this->reset('epe_marks', 'dev_marks', 'participation_marks');

            }
            else{

                return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des valeurs numériques compris entre 00 et 20!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'NOTES VIDES', 'message' => "Veuillez insérer des notes d'abord!", 'type' => 'warning']);
        }


    }


    public function editMarks($pupil_id)
    {
        $this->targeted_pupil = $pupil_id;

        if(isset($this->marks[$pupil_id])){

            $this->epe_marks = $this->marks[$pupil_id]['epe'];

            $this->dev_marks = $this->marks[$pupil_id]['devoir'];

            $this->participation_marks = $this->marks[$pupil_id]['participation'];

        }
    }

    public function retrievedPupilMarksFromMarksData($pupil_id)
    {
        $marks = $this->marks;

        $this->targeted_pupil 
                            ? (
                                $pupil_id == $this->targeted_pupil 
                                ? $this->reset('targeted_pupil') 
                                : $this->targeted_pupil = $this->targeted_pupil
                               ) 
                            : $this->reset('targeted_pupil');

        unset($marks[$pupil_id]);

        $this->reset('epe_marks', 'dev_marks', 'participation_marks');

        $this->marks = $marks;

    }

    /**
     * To clean the last inserting marks data
     */
    public function toback()
    {
        $this->resetErrorBag();

        $this->reset('epe_marks', 'dev_marks', 'participation_marks', 'targeted_pupil');
    }

    /**
     * To clean all marks insert data
     */
    public function flushMarksTabs()
    {
        $this->reset('epe_marks', 'dev_marks', 'participation_marks', 'marks', 'targeted_pupil');
    }
}

