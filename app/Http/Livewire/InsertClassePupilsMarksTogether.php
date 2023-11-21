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

    public $dev_marks;

    public $type = 'epe';

    public $semestre_id = 1;

    public $subject;

    public $title = "Insertion de nouvelle notes de classe";

    public $classe;

    public $semestre_type = 'Semestre';

    public $school_year;


    use ModelQueryTrait;


    protected $rules = ['subject_id' => 'required|int'];

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




    public function openModal(int $classe_id)
    {

        if($classe_id){

            $school_year_model = $this->getSchoolYear();

            $classe = $school_year_model->findClasse($classe_id);

            if($classe){

                $this->classe = $classe;

                $user = auth()->user();

                $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);


                $this->pupils = $this->classe->getPupils();

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

                    ClasseMarksInsertionCreatedEvent::dispatch($data);

                    $this->dispatchBrowserEvent('hide-form');

                    // $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'NOTES SOUMISES', 'message' => "Les notes ont été soumises avec succès!", 'type' => 'success']);

                    $this->reset('classe_id', 'semestre_id', 'subject_id', 'subject', 'marks', 'school_year', 'classe', 'targeted_pupil');
                   
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

        if($this->epe_marks || $this->dev_marks ){

            $epe_marks = explode('-', $this->epe_marks);

            $dev_marks = explode('-', $this->dev_marks);

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
                    elseif($dev && (floatval($dev) > 20 || floatval($epe) < 0)){

                        $error = true;
                    }

                }

            }

            if(!$error){

                if(isset($marks[$pupil_id]) && array_key_exists($pupil_id, $marks)){

                    unset($marks[$pupil_id]);

                    $marks[$pupil_id] = ['epe' => $this->epe_marks, 'devoir' => $this->dev_marks];

                }
                else{

                    $marks[$pupil_id] = ['epe' => $this->epe_marks, 'devoir' => $this->dev_marks];

                }

                $this->marks = $marks;

                $this->reset('epe_marks', 'dev_marks');

            }
            else{

                return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des valeurs numériques compris entre 00 et 20!", 'type' => 'error']);

            }

        }
        else{
            $this->dispatchBrowserEvent('Toast', ['title' => 'NOTES VIDES', 'message' => "Veuillez insérer des notes d'abord!", 'type' => 'warning']);
        }


    }


    public function editMarks($pupil_id)
    {
        $this->targeted_pupil = $pupil_id;

        if(isset($this->marks[$pupil_id])){

            $this->epe_marks = $this->marks[$pupil_id]['epe'];

            $this->dev_marks = $this->marks[$pupil_id]['devoir'];

        }
    }

    public function retrieveFromMarks($pupil_id)
    {
        $marks = $this->marks;

        unset($data[$pupil_id]);

        $this->marks = $marks;

    }
}

