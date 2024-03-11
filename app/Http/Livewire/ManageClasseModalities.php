<?php

namespace App\Http\Livewire;

use App\Events\FreshAveragesIntoDBEvent;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\AverageModality;
use App\Models\Classe;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Livewire\Component;

class ManageClasseModalities extends Component
{
    use ModelQueryTrait;


    protected $listeners = [
        'manageClasseModalitiesLiveEvent' => 'openModal', 
    ];

    public $classe_id;
    public $subject_id;
    public $semestre_id = 1;
    public $semestre_type = 'Semestre';

    public $school_year;
    public $subject;
    public $classe;
    public $modality;
    public $value = 3;
    public $school_year_model;
    public $counter = 0;

    protected $rules = [
        'value' => 'required'
    ];
    public function render()
    {
        $classes = [];

        $subjects = [];

        $school_years = SchoolYear::all();

        $semestres = [1, 2];

        $school = School::first();

        if($this->classe && $this->school_year_model){

            $subjects = Subject::where('level_id', $this->classe->level_id)->get();

            $classes = $this->school_year_model->classes;
        }

        if($school){

            if($school->trimestre){

                $this->semestre_type = 'Trimestre';

                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }
        return view('livewire.manage-classe-modalities', compact('classes', 'subjects', 'semestres', 'school_years'));
    }


    public function openModal($classe_id, $subject_id, $school_year_id, $semestre_id, $modality_id = null)
    {
        // dd($classe_id, $subject_id, $school_year_id, $semestre_id, $modality_id);

        if($classe_id && $school_year_id && $subject_id && $semestre_id){

            $school_year_model = SchoolYear::find($school_year_id);

            if($school_year_model){

                $this->school_year_model = $school_year_model;

                $this->school_year = $this->school_year_model->id;

                $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
                if($classe){

                    $this->classe = $classe;

                    $this->classe_id = $classe->id;

                    $subject = Subject::find($subject_id);

                    if($subject){

                        $this->subject = $subject;

                        $this->subject_id = $subject_id;

                        $this->semestre_id = $semestre_id;

                        if($modality_id){

                            $modality = AverageModality::find($modality_id);

                            if($modality){
                                
                                $this->modality = $modality;
                                
                                $this->value = $modality->modality;
                            }
                            else{
                                return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Il semble que votre requête soit corrompue, la modalité n'est pas précisée ou précise!", 'type' => 'question']);
                            }

                        }
                        $this->dispatchBrowserEvent('modal-manageClasseModalities');
                    }
                    else{
                        return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure matière ou discipline', 'message' => "Veuillez vérifier vos données, la matière est introuvable!", 'type' => 'error']);

                    }
                }
                else{
                    return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure classe', 'message' => "Veuillez vérifier vos données, la classe est introuvable!", 'type' => 'error']);

                }
            }
            else{
                return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure Année scolaire', 'message' => "Veuillez choisir une année scolaire valide!", 'type' => 'question']);

            }
        }
        else{
            return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "Votre requête ne peut aboutir, le formulaire est incomplet!", 'type' => 'warning']);
        }   
    }


    public function submit()
    {
        $this->validate();

        if($this->modality){

            $updated = $this->modality->update([
                'modality' => $this->value
            ]);

            if($updated){

                $this->dispatchBrowserEvent('hide-form');

                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La mise à jour s'est déroulée avec succès!", 'type' => 'success']);

                $this->emit('classeUpdated');

                $this->reset('classe_id', 'subject_id', 'modality', 'value', 'school_year', 'semestre_id');
            }
            else{

                return $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour interrompue', 'message' => "Une erreure inconnue s'est produite!", 'type' => 'error']);
            }                       

        }
        else{

            $school_year = SchoolYear::find($this->school_year);

            if($school_year){

                $this->modality = AverageModality::create([
                    'modality' => $this->value,
                    'classe_id' => $this->classe_id,
                    'subject_id' => $this->subject_id,
                    'school_year' => $school_year->school_year,
                    'semestre' => $this->semestre_id,

                ]);
                if($this->modality){

                    $this->optimizeSemestrialAverageFromDatabase($this->classe_id);

                    $this->dispatchBrowserEvent('hide-form');

                    $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La mise à jour s'est déroulée avec succès!", 'type' => 'success']);

                    $this->reset('classe_id', 'subject_id', 'modality', 'value', 'school_year', 'semestre_id');
                }
                else{
                    return $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour interrompue', 'message' => "Une erreure inconnue s'est produite!", 'type' => 'error']);
                }  
            }
            else{
                return $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure Année scolaire', 'message' => "Une erreure s'est produite car l'année scolaire est invalide!", 'type' => 'error']);
            }
        }


    }


    public function optimizeSemestrialAverageFromDatabase($classe_id, $semestre = 1)
    {
        $semestre = session('semestre_selected');

        if($semestre){

            $classe = $this->classe;

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


    public function deleteThisModality()
    {
        if($this->modality){
            
            $del = $this->modality->delete();
            
            if($del){

                $this->optimizeSemestrialAverageFromDatabase($this->classe_id);

                $this->dispatchBrowserEvent('hide-form');

                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "La mise à jour s'est déroulée avec succès!", 'type' => 'success']);

                $this->emit('classeUpdated');

                $this->reset('classe_id', 'subject_id', 'modality', 'value', 'school_year', 'semestre_id');
            }
            else{
                return $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour interrompue', 'message' => "Une erreure inconnue s'est produite!", 'type' => 'error']);
            } 
        }
        else{
            return $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour interrompue', 'message' => "Une erreure inconnue s'est produite!", 'type' => 'error']);
        }


    }

}
