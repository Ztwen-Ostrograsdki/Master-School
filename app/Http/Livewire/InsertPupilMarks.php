<?php

namespace App\Http\Livewire;

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
    public $type = 'epe';
    public $semestre_id = 1;
    public $pupil;
    public $pupilName = 'Elève';
    public $classe;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $subjects = [];
    public $classe_subject_selected;

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
        $school_years = SchoolYear::all();
        return view('livewire.insert-pupil-marks', compact('semestres', 'school_years', 'types_of_marks'));
    }


    public function addNewMarks(int $pupil_id, int $classe_id, int $subject_id, int $semestre, int $school_year)
    {
        if($subject_id && $pupil_id && $classe_id){
            
            $pupil = Pupil::find($pupil_id);
            $subject = Subject::find($subject_id);
            $classe = Classe::find($classe_id);

            if($pupil && $subject && $classe && $semestre){
                $this->pupil = $pupil;
                $this->pupilName = $pupil->getName();
                $this->semestre_id = $semestre;
                $this->subject_id = $subject_id;
                $this->classe_id = $classe_id;
                $this->school_year = $school_year;

                $this->classe = $classe;
                $this->subjects = $pupil->classe->subjects;

                $this->dispatchBrowserEvent('modal-insertPupilMarks');
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos données sont ambigües, nous n'avons trouvé aucun apprenant et ou la matière correspondant(e)!", 'type' => 'error']);
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
        $marks = $this->marks;
        $pupil = $this->pupil;

        if($school_year && $subject_id && $classe_id && $semestre && $type && $marks){
            $marks = explode('-', $marks);
            $tabs = [];

            $school_year_model = SchoolYear::find($school_year);

            foreach($marks as $mark){
                $tabs[] = floatval($mark);
                if(!is_numeric($mark)){
                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres!", 'type' => 'error']);
                }
                elseif(floatval($mark) > 20 || floatval($mark) < 0){
                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent être des nombres compris entre 00 et 20!", 'type' => 'error']);
                }
            }

            if($tabs !== []){
                $make = DB::transaction(function($e) use ($tabs, $pupil, $subject_id, $semestre, $classe_id, $type, $school_year_model){
                    foreach($tabs as $validMark){
                        DB::transaction(function($e) use ($validMark, $pupil, $subject_id, $semestre, $classe_id, $type, $school_year_model){
                            $mark = Mark::create([
                                'value' => $validMark, 
                                'pupil_id' => $pupil->id, 
                                'subject_id' => $subject_id, 
                                'classe_id' => $classe_id, 
                                'semestre' => $semestre, 
                                'type' => $type, 
                                'level_id' => $pupil->level_id, 
                            ]);
                            if ($mark) {
                                $school_year_model->marks()->attach($mark->id);
                            }

                        });
                    }

                    DB::afterCommit(function(){
                        $this->emit('pupilUpdated');
                        $this->emit('classeUpdated');
                        $this->dispatchBrowserEvent('hide-form');
                        $this->reset('marks');
                        $this->resetErrorBag();
                        // $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "la note a été inséré avec succès!", 'type' => 'success']);

                    });

                });
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Au moins les données de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des données valides!", 'type' => 'warning']);
        }


    }
}
