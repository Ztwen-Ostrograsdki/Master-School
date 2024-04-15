<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassePupilSchoolYear;
use App\Models\Level;
use App\Models\Pupil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class AllPupilLister extends Component
{

    protected $listeners = [
        'schoolYearChangedLiveEvent' => 'reloadData',
        'UpdatedGlobalSearch' => 'updatedSearch',
        'pupilUpdated' => 'reloadData',
        'UpdatedSchoolYearData' => 'reloadData',
        'GlobalDataUpdated' => 'reloadData',
    ];

    public $counter = 0;

    public $search = null;

    public $hasErrors = false;

    public $sexe_selected;

    public $classe_id_selected;

    public $pupil_type_selected;

    public $classe_group_id_selected;

    public $level = 'secondary';

    public $level_id;


    use ModelQueryTrait;


    public function render()
    {
        // $pupils = Pupil::all();

        $pupils = [];

        $school_year_model = $this->getSchoolYear();

        $lastYear = $this->getLastYear();

        $pupils = [];

        $classes = [];

        $classe_groups = $school_year_model->classe_groups()->orderBy('classe_groups.name', 'asc')->get();
        $classes = $school_year_model->classes()->orderBy('classes.name', 'asc')->get();

        $this->level_id = Level::where('name', $this->level)->first()->id;
        
        if($this->search && mb_strlen($this->search) >= 2){

            $pupils = Pupil::where('level_id', $this->level_id)->where('firstName', 'like', '%' . $this->search . '%')->orWhere('lastName', 'like', '%' . $this->search . '%')->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
        }
        else{

            $sexe = $this->sexe_selected;

            if($this->classe_id_selected){

                $classe = $school_year_model->classes()->where('classes.id', $this->classe_id_selected)->first();

                $data = [];

                if($this->sexe_selected && $classe){

                    $data = $classe->getPupils($school_year_model->id, null, $this->sexe_selected);
                }
                elseif($classe){

                    $data = $classe->getClasseCurrentPupils($school_year_model->id);
                }

                if(count($data) > 0){

                    foreach($data as $p){
                        
                        if($p->level_id == $this->level_id){

                            $pupils[] = $p;

                        }

                    }

                }



            }
            elseif($this->classe_group_id_selected){

                $classe_group = $school_year_model->classe_groups()->where('classe_groups.id', $this->classe_group_id_selected)->first();

                $pupils_ids = [];    

                if($classe_group){

                    $classes_cg = $classe_group->classes;

                    if(count($classes_cg) > 0){
                        
                        foreach($classes_cg as $classe){
                            
                            $pupils_ids = $classe->getPupils($school_year_model->id, null, null, true);
                        }
                    } 
                }

                if($sexe && $classe_group){

                    $pupils = Pupil::where('level_id', $this->level_id)->whereIn('pupils.id', $pupils_ids)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }
                else{

                    $pupils = Pupil::where('level_id', $this->level_id)->whereIn('pupils.id', $pupils_ids)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
                }

            }
            elseif($sexe){

                $pupils = Pupil::where('level_id', $this->level_id)->where('pupils.sexe', $sexe)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
            }
            else{

                $pupils = Pupil::where('level_id', $this->level_id)->orderBy('firstName', 'asc')->orderBy('lastName', 'asc')->get();
            }

        }


        return view('livewire.all-pupil-lister', compact('pupils', 'school_year_model', 'lastYear', 'classes', 'classe_groups'));
    }


    public function disjoin($pupil_id)
    {
        if($pupil_id){

            $pupil = Pupil::find($pupil_id);


            if($pupil){

                $school_year_model = $this->getSchoolYear();

                $yet = $pupil->isPupilOfThisYear();

                if($yet){

                    DB::transaction(function($e) use ($pupil, $school_year_model){

                        $pupil->pupilDeleter($school_year_model->id, false);

                    });

                    DB::afterCommit(function() use($school_year_model){

                        $this->emit('UpdatedSchoolYearData');

                        if(!$this->hasErrors){

                            $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "L'apprenant a été mise à jour avec succès! Les données de L'apprenant en $school_year_model->school_year ont été supprimé avec succès!", 'type' => 'success']);

                            $this->reset('hasErrors');

                        }


                        
                    });

                }
                else{

                    $this->hasErrors = true;

                    $this->dispatchBrowserEvent('Toast', ['title' => "APPRENANT NON DISPONIBLE EN $school_year_model->school_year", 'message' => "L'apprenant renseignée est introuvable", 'type' => 'error']);
                }
            }
            else{

                $this->hasErrors = true;

                $this->dispatchBrowserEvent('Toast', ['title' => 'APPRENANT INTROUVABLE', 'message' => "L'apprenant renseignée est introuvable", 'type' => 'error']);
            }
        }
        else{

            $this->hasErrors = true;

            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Veuillez renseigner une donnée valide", 'type' => 'warning']);
        }


    }


    public function join($pupil_id)
    {
        $this->reset('hasErrors');

        if($pupil_id){

            $pupil = Pupil::find($pupil_id);

            if($pupil){

                $school_year_model = $this->getSchoolYear();

                $yet = $pupil->isPupilOfThisYear();

                if(!$yet){

                    DB::transaction(function($e) use ($school_year_model, $pupil){

                        $classeVolante = $pupil->polyvalenteClasse();

                        if($classeVolante){

                            ClassePupilSchoolYear::create(
                                [
                                    'classe_id' => $classeVolante->id,
                                    'pupil_id' => $pupil->id,
                                    'school_year_id' => $school_year_model->id,
                                ]
                            );

                            $pupil->update(['classe_id' => $classeVolante->id]);

                            $classeVolante->classePupils()->attach($pupil->id);

                            $school_year_model->pupils()->attach($pupil->id);
                        }
                        else{

                            $this->hasErrors = true;

                            $this->dispatchBrowserEvent('Toast', ['title' => 'CLASSE VOLANTE NON DISPONIBLE', 'message' => "Veuillez créer d'abord une classe volante pour y insérer des apprenants sans classe!", 'type' => 'error']);

                        }
                       
                        DB::afterCommit(function() use($school_year_model){

                            $this->emit('UpdatedSchoolYearData');

                            if(!$this->hasErrors){

                                $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour terminée', 'message' => "L'apprenant a été mise à jour avec succès! Cet apprenant est désormais disponible en $school_year_model->school_year !", 'type' => 'success']);

                                $this->reset('hasErrors');

                            }
                            
                        });
                        
                    });

                    

                }
                else{

                    $this->hasErrors = true;

                    $this->dispatchBrowserEvent('Toast', ['title' => "APPRENANT DEJA DISPONBLE EN $school_year_model->school_year", 'message' => "Il semble que vous être entrain de vouloir repéter des requêtes!", 'type' => 'error']);

                }
            }
            else{

                $this->hasErrors = true;

                $this->dispatchBrowserEvent('Toast', ['title' => 'APPRENANT INTROUVABLE', 'message' => "L'apprenant renseignée est introuvable", 'type' => 'error']);
            }
        }
        else{

            $this->hasErrors = true;

            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure', 'message' => "Veuillez renseigner une donnée valide", 'type' => 'warning']);
        }



    }




    public function resetSearch()
    {
        $this->reset('search');
    }

    public function updatedSearch($value)
    {
        $this->search = $value;
    }

    public function updatedSexeSelected($sexe)
    {
        $this->sexe_selected = $sexe;
    }

    public function updatedClasseIdSelected($classe_id)
    {
        $this->reset('search', 'classe_group_id_selected');

        $this->classe_id_selected = $classe_id;
    }


    public function updatedClasseGroupIdSelected($classe_group_id)
    {
        $this->reset('search', 'classe_id_selected');

        $this->classe_group_id_selected = $classe_group_id;
    }

    public function updatedPupilTypeSelected($pupil_type_selected)
    {
        $this->reset('search', 'pupil_type_selected');

        $this->pupil_type_selected = $pupil_type_selected;
    }


    public function reloadData()
    {
        $this->counter = rand(1, 23);

    }
}
