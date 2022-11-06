<?php

namespace App\Http\Livewire;

use App\Models\Pupil;
use App\Models\RelatedMark;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Carbon;
use Livewire\Component;

class InsertPupilRelatedMark extends Component
{
    protected $listeners = ['insertRelatedMarkLiveEvent' => 'addNewMark'];
    public $classe_id;
    public $pupil_id;
    public $subject_id;
    public $marks;
    public $type = 'bonus';
    public $motif;
    public $horaire;
    public $start;
    public $date;
    public $end;
    public $semestre_id = 1;
    public $pupil;
    public $semestre_type = 'Semestre';
    public $school_year;
    public $subjects = [];
    public $classe_subject_selected;

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
        $school_years = SchoolYear::all();
        return view('livewire.insert-pupil-related-mark', compact('semestres', 'school_years', 'types_of_marks'));
    }


    public function addNewMark(int $pupil_id, int $subject_id, int $semestre, int $school_year)
    {

        if($subject_id && $pupil_id && $semestre && $school_year){
            
            $pupil = Pupil::find($pupil_id);
            $subject = Subject::find($subject_id);

            if($pupil && $subject){
                $this->pupil = $pupil;
                
                if($semestre){
                    $this->semestre_id = $semestre;
                }
                $this->date = (new \DateTime(Carbon::today()))->format('Y-m-d');
                $this->start = intval(date('H')) + 1;
                $this->end = intval(date('H')) + 2;
                $this->subject_id = $subject_id;
                $this->classe_id = $pupil->classe_id;
                $this->school_year = $school_year;

                $this->subjects = $pupil->classe->subjects;
                $this->dispatchBrowserEvent('modal-insertPupilRelatedMark');
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
        $start = $this->start;
        $end = $this->end;

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

            return false;

            if($tabs !== []){
                $make = DB::transaction(function($e) use ($tabs, $pupil, $subject_id, $semestre, $classe_id, $type, $school_year_model){
                    foreach($tabs as $validMark){
                        DB::transaction(function($e) use ($validMark, $pupil, $subject_id, $semestre, $classe_id, $type, $school_year_model){
                            $mark = RelatedMark::create([
                                'value' => $validMark, 
                                'pupil_id' => $pupil->id, 
                                'subject_id' => $subject_id, 
                                'classe_id' => $classe_id, 
                                'semestre' => $semestre, 
                                'type' => $type, 
                                'level_id' => $pupil->level_id, 
                                'horaire' => $this->start . 'H - ' . $this->end . 'H',
                                'motif' => $this->motif,
                                'date' => $this->date,
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
                        $this->resetErrorBag();
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "la note a été inséré avec succès!", 'type' => 'success']);

                    });

                });
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Au moins les données de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des données valides!", 'type' => 'warning']);
        }


    }
}
