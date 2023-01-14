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

class MarkManager extends Component
{
    protected $listeners = ['editPupilMarkLiveEvent' => 'editPupilMark'];
    public $pupil_id;
    public $mark_id;
    public $type = 'epe';
    public $semestre_id = 1;
    public $pupil;
    public $semestre_type = 'Semestre';
    public $school_year;
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
        $subject_selected = session('classe_subject_selected');
        if($subject_selected){
            $subject_selected = Subject::find($subject_selected)->name;
        }
        else{
            $subject_selected  = "matière inconnue";
        }

        return view('livewire.mark-manager', compact('semestres', 'school_years', 'types_of_marks', 'subject_selected'));
    }


    public function editPupilMark(int $mark_id)
    {
        if($mark_id){
            
            $mark = Mark::find($mark_id);
            $pupil = $mark->pupil;

            if($pupil && $mark){
                $this->pupil = $pupil;
                
                $this->markModel = $mark;
                $this->mark = $mark->value;

                $this->type = $mark->type;
                $this->semestre_id = $mark->semestre;
                $this->dispatchBrowserEvent('modal-markManager');
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Vos données sont ambigües, nous n'avons trouvé aucun apprenant et ou la matière correspondant(e)!", 'type' => 'error']);
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Veuillez sélectionner une classe, un apprenant et une matière valides", 'type' => 'warning']);

        }
    }


    public function toUnforcedMark()
    {
        $this->markModel->update(['forced_mark' => false]);
        $this->emit('pupilUpdated');
        $this->emit('classeUpdated');
        $this->dispatchBrowserEvent('hide-form');
        $this->resetErrorBag();

    }

    public function toForcedMark()
    {
        $this->markModel->update(['forced_mark' => true]);
        $this->emit('pupilUpdated');
        $this->emit('classeUpdated');
        $this->dispatchBrowserEvent('hide-form');
        $this->resetErrorBag();

    }


    public function delete()
    {
        $mark = $this->markModel;

        DB::transaction(function($e) use ($mark){
            $school_year = $mark->school_year();
            $detach = $school_year->marks()->detach($mark->id);
            if($detach){
                $mark->forceDelete();
            }

            DB::afterCommit(function(){
                $this->emit('pupilUpdated');
                $this->emit('classeUpdated');
                $this->dispatchBrowserEvent('hide-form');
                $this->resetErrorBag();
                // $this->dispatchBrowserEvent('Toast', ['title' => 'Suupression terminée', 'message' => "La note a été suuprimée", 'type' => 'success']);
            });

        });
    }


    public function submitMark()
    {
        $semestre = $this->semestre_id;
        $type = $this->type;
        $mark = $this->mark;
        $pupil = $this->pupil;


        if($semestre && $type && $mark && $pupil){
            DB::transaction(function($e) use ($mark, $pupil, $semestre, $type){
                $this->markModel->update([
                    'value' => $mark,
                    'semestre' => $semestre,
                    'type' => $type

                ]);

                DB::afterCommit(function(){
                    $this->emit('pupilUpdated');
                    $this->emit('classeUpdated');
                    $this->dispatchBrowserEvent('hide-form');
                    $this->resetErrorBag();
                    // $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La note a été mise à jour avec succès!", 'type' => 'success']);
                });

            });

        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Au moins les données de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des données valides!", 'type' => 'warning']);
        }


    }
}