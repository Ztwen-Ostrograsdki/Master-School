<?php

namespace App\Http\Livewire;

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
    protected $listeners = ['insertRelatedMarkLiveEvent' => 'addNewMark'];
    public $classe_id;
    public $pupil_id;
    public $school_year_model;
    public $target;
    public $together = false;
    public $subject_id;
    public $marks = 4;
    public $tabsMark = [];
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


    public function addNewMark(int $target_id, int $subject_id, int $semestre, int $school_year, $together = false)
    {
        $this->together = $together;
        if($subject_id && $target_id && $semestre && $school_year){
            if(!$together) {
                $pupil_id = $target_id;
                $pupil = Pupil::find($pupil_id);
                $subject = Subject::find($subject_id);

                if($pupil && $subject){
                    $this->pupil = $pupil;
                    $this->target = $pupil;
                    $this->pupil_id = $pupil->id;
                    
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
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos donn??es sont ambig??es, nous n'avons trouv?? aucun apprenant et ou la mati??re correspondant(e)!", 'type' => 'error']);
                }
            }
            else{

                $classe_id = $target_id;
                $classe = Classe::find($classe_id);
                $subject = Subject::find($subject_id);

                if($classe && $subject){
                    $this->classe = $classe;
                    $this->target = $classe;
                    $this->classe_id = $classe->id;
                    
                    if($semestre){
                        $this->semestre_id = $semestre;
                    }
                    $this->date = (new \DateTime(Carbon::today()))->format('Y-m-d');
                    $this->start = intval(date('H')) + 1;
                    $this->end = intval(date('H')) + 2;
                    $this->subject_id = $subject_id;
                    $this->classe_id = $classe_id;
                    $this->school_year = $school_year;

                    $this->subjects = $classe->subjects;
                    $this->dispatchBrowserEvent('modal-insertPupilRelatedMark');
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos donn??es sont ambig??es, nous n'avons trouv?? aucune classe et ou la mati??re correspondant(e)!", 'type' => 'error']);
                }


            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de proc??dure', 'message' => "Veuillez s??lectionner une classe, un apprenant et une mati??re valides", 'type' => 'warning']);

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
        $this->horaire = $start . 'H - ' . $end . 'H';

        if($school_year && $subject_id && $classe_id && $semestre && $type && $marks){
            $marks = explode('-', $marks);
            $tabs = [];

            $this->school_year_model = SchoolYear::find($school_year);

            foreach($marks as $mark){
                $tabs[] = floatval($mark);
                if(!is_numeric($mark)){
                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les notes doivent ??tre des nombres!", 'type' => 'error']);
                }
                elseif($type == 'bonus' && (floatval($mark) > 10 || floatval($mark) < 0)){
                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les bonus doivent ??tre des nombres compris entre 00 et 10!", 'type' => 'error']);
                }
                elseif($type == 'minus' && (floatval($mark) > 100 || floatval($mark) <= 1)){
                    return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Note invalide', 'message' => "Les moins doivent ??tre des nombres compris entre 01 et 100!", 'type' => 'error']);
                }
            }

            $this->tabsMark = $tabs;

            if($this->tabsMark !== []){
                $make = DB::transaction(function($e){
                    foreach($this->tabsMark as $validMark){
                        DB::transaction(function($e) use ($validMark){
                            if(!$this->together){
                                $mark = RelatedMark::create([
                                    'value' => $validMark, 
                                    'pupil_id' => $this->pupil_id, 
                                    'subject_id' => $this->subject_id, 
                                    'classe_id' => $this->classe_id, 
                                    'semestre' => $this->semestre_id, 
                                    'type' => $this->type, 
                                    'level_id' => $this->pupil->level_id, 
                                    'horaire' => $this->horaire,
                                    'motif' => $this->motif,
                                    'date' => $this->date,
                                ]);
                                if ($mark) {
                                    $this->school_year_model->related_marks()->attach($mark->id);
                                }
                            }
                            elseif($this->together){
                                $this->classe->getPupils($this->school_year_model->id)->each(function($pupil) use ($validMark){
                                    $mark = RelatedMark::create([
                                        'value' => $validMark, 
                                        'pupil_id' => $pupil->id, 
                                        'subject_id' => $this->subject_id, 
                                        'classe_id' => $this->classe->id, 
                                        'semestre' => $this->semestre_id, 
                                        'type' => $this->type, 
                                        'level_id' => $this->classe->level_id, 
                                        'horaire' => $this->horaire,
                                        'motif' => 'Note collective' . $this->motif,
                                        'date' => $this->date
                                    ]);
                                    if ($mark) {
                                        $this->school_year_model->related_marks()->attach($mark->id);
                                    }

                                });
                            }
                            else{
                                return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de proc??dure', 'message' => "Au moins les donn??es de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des donn??es valides!", 'type' => 'warning']);
                            }
                        });
                    }
                    DB::afterCommit(function(){
                        $this->emit('pupilUpdated');
                        $this->emit('classeUpdated');
                        $this->dispatchBrowserEvent('hide-form');
                        $this->resetErrorBag();
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise ?? jour r??ussie', 'message' => "la note a ??t?? ins??r?? avec succ??s!", 'type' => 'success']);

                    });

                });
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de proc??dure', 'message' => "Au moins les donn??es de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des donn??es valides!", 'type' => 'warning']);
        }


    }
}
