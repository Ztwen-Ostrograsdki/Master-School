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

class MarkManager extends Component
{
    use ModelQueryTrait;
    protected $listeners = ['editPupilMarkLiveEvent' => 'editPupilMark'];
    public $pupil_id;
    public $mark_id;
    public $mark;
    public $type = 'epe';
    public $mark_index;
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
            $school_year_model = $this->getSchoolYear();
            $user = auth()->user();
            $mark = Mark::find($mark_id);

            if($mark){
                $classe_id = $mark->classe_id;
                $not_secure = $user->ensureThatTeacherCanAccessToClass($classe_id);

                $pupil = $mark->pupil;
            }

            if($pupil && $mark){ 

                if($not_secure){
                    $this->pupil = $pupil;
                    
                    $this->markModel = $mark;
                    $this->mark = $mark->value;

                    $this->type = $mark->type;
                    $this->mark_index = $mark->mark_index;

                    $this->semestre_id = $mark->semestre;
                    $this->dispatchBrowserEvent('modal-markManager');
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "La mise à jour ou l'insertion des notes est temporairement indisponible pour cette classe!", 'type' => 'warning']);
                }

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
        $this->markModel->update(['forced_mark' => false, 'forget' => false]);
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

    public function toForgetMark()
    {
        $this->markModel->update(['forget' => true, 'forced_mark' => false]);
        $this->emit('pupilUpdated');
        $this->emit('classeUpdated');
        $this->dispatchBrowserEvent('hide-form');
        $this->resetErrorBag();

    }

    public function toUnforgetMark()
    {
        $this->markModel->update(['forget' => false]);
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
        $mark_index = $this->mark_index;

        $mark_index_was_existed = $pupil->marks()->where('classe_id', $this->markModel->classe_id)->where('subject_id', $this->markModel->subject_id)->where('semestre', $this->markModel->semestre)->where('type', $type)->where('mark_index', $this->mark_index)->where('id', '<>', $this->markModel->id)->first();

        if($mark_index_was_existed){
            if($mark_index_was_existed->school_years()->first()->id == $this->markModel->school_years()->first()->id){
                return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "L'index $this->mark_index de la note est déjà existante!", 'type' => 'warning']);
            }

        }


        $not_secure = auth()->user()->ensureThatTeacherCanAccessToClass($mark->classe_id);
        if($not_secure){
            if($semestre && $type && $mark && $pupil){
                DB::transaction(function($e) use ($mark, $pupil, $semestre, $type, $mark_index){
                    $this->markModel->update([
                        'value' => $mark,
                        'semestre' => $semestre,
                        'type' => $type,
                        'mark_index' => $mark_index,
                        'editor' => auth()->user()->id, 
                    ]);

                    DB::afterCommit(function(){
                        $this->emit('pupilUpdated');
                        $this->emit('classeUpdated');
                        $this->dispatchBrowserEvent('hide-form');
                        $this->resetErrorBag();
                    });

                });

            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure de procédure', 'message' => "Au moins les données de l'un des champs sont invalides. Veuillez bien renseigner tous les champs avec des données valides!", 'type' => 'warning']);
            }
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE VERROUILLEE', 'message' => "La mise à jour ou l'insertion des notes est temporairement indisponible pour cette classe!", 'type' => 'warning']);
        }


       

    }
}