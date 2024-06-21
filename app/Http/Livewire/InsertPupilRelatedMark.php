<?php

namespace App\Http\Livewire;

use App\Events\ClasseMarksInsertionCreatedEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\RelatedMark;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InsertPupilRelatedMark extends Component
{
    use ModelQueryTrait;
    
    protected $listeners = [
        'insertRelatedMarkLiveEvent' => 'openModal',
        'UpdatePupilRelatedMark' => 'editRelatedMark',
    ];
    public $classe_id;

    public $classe;

    public $pupil_id;

    public $school_year_model;

    public $target;

    public $together = false;

    public $subject_id;

    public $marks = 4;

    public $mark;

    public $tabsMark = [];

    public $type = 'bonus';

    public $motif = "Pertubation de cours - Bavardage en plein cours";

    public $horaire;

    public $start;

    public $date;

    public $end;

    public $semestre_id = 1;

    public $pupil;

    public $semestre_type = 'Semestre';

    public $school_year;

    public $subjects = [];

    public $title = "Insertion de notes relatives";


    public function render()
    {
        $types_of_marks = [
            'minus' => 'Sanction',
            'bonus' => 'Bonus'
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


        return view('livewire.insert-pupil-related-mark', compact('semestres', 'school_years', 'types_of_marks'));
    }


    public function updatedStart($start)
    {
        $this->end = $start + 1;
    }


    public function openModal(int $pupil_id, int $subject_id, int $semestre, int $school_year, $together = false)
    {
        $this->reseter();

        $this->together = $together;

        if($subject_id && $pupil_id && $semestre && $school_year){

            $school_year_model = $this->getSchoolYear();

            if(!$together) {

                $pupil_id = $pupil_id;

                $pupil = $school_year_model->findPupil($pupil_id);

                $subject = Subject::find($subject_id);

                if($pupil && $subject){

                    $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($pupil->classe_id);

                    if($not_secure){

                        $this->pupil = $pupil;

                        $this->target = $pupil;

                        $this->pupil_id = $pupil->id;
                        
                        if($semestre){

                            $this->semestre_id = $semestre;
                        }

                        $this->subject_id = $subject_id;

                        $this->classe_id = $pupil->classe_id;

                        $this->classe = $school_year_model->findClasse($this->classe_id);

                        $this->school_year = $school_year;

                        $this->subjects = $pupil->classe->subjects;

                        $this->date = (new \DateTime(Carbon::today()))->format('Y-m-d');

                        $this->start = intval(date('H')) + 1;

                        $this->end = intval(date('H')) + 2;

                        $semestre_id = $this->semestre_id;

                        $subject_id = $this->subject_id;

                        $classe = $this->classe;

                        $mark_stopped_1 = $classe->classeMarksWasStoppedForThisSchoolYear($semestre_id, $subject_id);

                        $mark_stopped_2 = $classe->classeMarksWasStoppedForThisSchoolYear();

                        if(! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id) && ! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id, session('semestre_selected'))){

                            if(!$mark_stopped_1 && !$mark_stopped_2){

                                $this->dispatchBrowserEvent('modal-insertPupilRelatedMark');

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
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment!", 'type' => 'warning']);

                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos données sont ambigües, nous n'avons trouvé aucun apprenant et ou la matière correspondant(e)!", 'type' => 'error']);
                }
            }
            else{
                // THE PUPIL_ID IS AN ID OF A CLASSE
                
                $classe_id = $pupil_id;

                $classe = $school_year_model->findClasse($classe_id);

                $subject = Subject::find($subject_id);

                if($classe && $subject){

                    $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe_id);

                    if($not_secure){

                        $this->classe = $classe;

                        $this->target = $classe;

                        $this->classe_id = $classe->id;

                        
                        if($semestre){

                            $this->semestre_id = $semestre;
                        }
                        $this->subject_id = $subject_id;

                        $this->classe_id = $classe_id;

                        $this->school_year = $school_year;

                        $this->subjects = $classe->subjects;

                        $this->date = (new \DateTime(Carbon::today()))->format('Y-m-d');

                        $this->start = intval(date('H')) + 1;

                        $this->end = intval(date('H')) + 2;

                        $semestre_id = $this->semestre_id;

                        $mark_stopped_1 = $classe->classeMarksWasStoppedForThisSchoolYear($semestre_id, $subject_id);

                        $mark_stopped_2 = $classe->classeMarksWasStoppedForThisSchoolYear();

                        if(! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id) && ! is_marks_stopped($classe->id, $classe->level_id, $school_year_model->id, session('semestre_selected'))){

                            if(!$mark_stopped_1 && !$mark_stopped_2){

                                $this->dispatchBrowserEvent('modal-insertPupilRelatedMark');

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

                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment!", 'type' => 'warning']);

                    }
                }
                else{

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos données sont ambigües, nous n'avons trouvé aucune classe et ou la matière correspondant(e)!", 'type' => 'error']);
                }


            }
        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Veuillez sélectionner une classe, un apprenant et une matière valides", 'type' => 'warning']);

        }
    }


    public function editRelatedMark($mark_id)
    {
        $this->reseter();

        $mark = RelatedMark::find($mark_id);

        if($mark){

            $classe_id = $mark->classe_id;

            $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($classe_id);

            if($not_secure){

                $heures = explode('-', $mark->horaire);

                $this->start = trim(str_replace('H', '', $heures[0]));

                $this->end = trim(str_replace('H', '', $heures[1]));

                $this->mark = $mark;

                $this->classe = $mark->classe;

                $this->pupil = $mark->pupil;

                $this->pupil_id = $mark->pupil_id;

                $this->target = $this->pupil;

                $this->semestre_id = $mark->semestre;

                $this->subject_id = $mark->subject_id;

                $this->classe_id = $mark->classe_id;

                $this->type = $mark->type;

                $this->school_year_model = $mark->school_year;

                $this->school_year = $mark->school_year_id;

                $this->horaire = $mark->horaire;

                $this->motif = $mark->motif;

                $this->date = $mark->date;

                $this->level_id = $mark->level_id;

                $this->marks = $mark->value;

                $this->subjects = $this->classe->subjects;

                $this->title = "Edition des notes relatives";

                $this->dispatchBrowserEvent('modal-insertPupilRelatedMark');

            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "Vous ne pouvez pas insérer de notes pour le moment!", 'type' => 'warning']);

            }

        }

    }


    public function builder(array $marks, $together = false)
    {

        if($marks){

            $school_year_model = $this->school_year_model;

            $subject = Subject::find($this->subject_id);

            $user = auth()->user();

            $data = [
                'user' => $user, 
                'classe' => $this->classe, 
                'subject' => $subject, 
                'marks' => $marks, 
                'semestre' => $this->semestre_id, 
                'school_year_model' => $school_year_model
            ];
            
            $related_data = [
                'user' => $user, 
                'classe' => $this->classe, 
                'subject' => $subject,
                'subject_id' => $this->subject_id,
                'together' => $together,
                'motif' => $this->motif,
                'date' => $this->date,
                'horaire' => $this->horaire,
                'marks' => $marks,
                'semestre' => $this->semestre_id,
                'type' => $this->type,
                'pupil_id' => $this->pupil_id,
                'school_year_model' => $school_year_model
            ];

            

            ClasseMarksInsertionCreatedEvent::dispatch($data, true, $related_data);

            $this->dispatchBrowserEvent('hide-form');

        }

    }


    public function updater($marks)
    {


        DB::transaction(function($e) use($marks){

            $motif = $this->motif;

            $horaire = $this->horaire;

            $type = $this->type;

            $classe = $this->classe;

            $related_mark = $this->mark;

            $value = $marks[0];

            $related_mark->update([
                    'value' => $value, 
                    'type' => $type, 
                    // 'horaire' => $horaire,
                    'motif' => $motif,
                    // 'date' => $date,
                ]);

            DB::afterCommit(function(){

                $this->reseter();

                $this->dispatchBrowserEvent('hide-form');

                $this->emit('pupilUpdated');

                $this->emit('classeUpdated');

                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Mise à jour réussie avec succès!", 'type' => 'success']);

            });
        });

    }


    public function submitMarks()
    {
        $updating = false;

        if($this->mark){

            $updating = true;

        }

        $school_year = $this->school_year;

        $semestre = $this->semestre_id;

        $subject_id = $this->subject_id;

        $classe_id = $this->classe_id;

        $type = $this->type;

        $marks = $this->marks;

        $pupil = $this->pupil;

        $start = $this->start;

        $end = $this->end;

        $this->horaire = $start . 'H - ' . $end . 'H';


        if($school_year && $subject_id && $classe_id && $semestre && $type && $marks){

            $marks = explode('-', $marks);

            $tabs = [];

            $this->school_year_model = SchoolYear::find($school_year);

            foreach($marks as $mark){

                $tabs[] = floatval($mark);

                if(!is_numeric($mark)){

                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres!", 'type' => 'error']);
                }
                elseif($type == 'bonus' && (floatval($mark) > 10 || floatval($mark) < 0)){

                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les bonus doivent être des nombres compris entre 00 et 10!", 'type' => 'error']);
                }
                elseif($type == 'minus' && (floatval($mark) > 100 || floatval($mark) <= 1)){

                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les moins doivent être des nombres compris entre 01 et 100!", 'type' => 'error']);
                }
            }

            $this->tabsMark = $tabs;

            if($this->tabsMark !== [] && !$updating){

                $this->builder($this->tabsMark, $this->together);
            }
            elseif($this->tabsMark !== [] && $updating){

                $this->updater($this->tabsMark);
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Au moins les données de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des données valides!", 'type' => 'warning']);
        }


    }

    public function reseter()
    {
        $this->reset('classe_id', 'subject_id', 'pupil', 'pupil_id', 'classe', 'mark', 'subjects', 'subject_id', 'target', 'together');
    }


    public function delete()
    {

        if(true){

            $mark = $this->mark;

            if($mark){

                DB::transaction(function($e) use ($mark){

                    $school_year_model = $mark->school_year;

                    $detach = $school_year_model->related_marks()->detach($mark->id);

                    if($detach){

                        $m = $mark->delete();
                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Mise à jour échouée!", 'type' => 'error']);
                    }
                });
                
                DB::afterCommit(function(){

                    $this->reseter();

                    $this->dispatchBrowserEvent('hide-form');

                    $this->emit('pupilUpdated');

                    $this->emit('classeUpdated');

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "Mise à jour réussie avec succès!", 'type' => 'success']);

                });
            }
        }
    }
}
