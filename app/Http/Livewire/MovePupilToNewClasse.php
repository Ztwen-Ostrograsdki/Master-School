<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\ClasseGroup;
use App\Models\Pupil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MovePupilToNewClasse extends Component
{
    use ModelQueryTrait;

    protected $listeners = ['MovePupilToNewClasse' => 'openModal'];

    public $pupil;

    public $current_classe;

    public $position;

    public $school_year_model;

    public $classe_group_id;

    public $move_type = 'just_move';

    public $classe_id;

    protected $docs = [
        'just_move' => "Just change the pupil's classe and use they data for the new classe",
        'same_promotion' => "Moove the pupil to a same classe's promotion and use they data for the new classe",
        'migrate' => "Upgrade the pupil to a high classe level like a new school year start",
        'reset_data' => "Remove the pupil's classe and send it into a polyvalense classe after clearing all they data",
        'to_polyvalence' => "Remove the pupil's classe and send it into a polyvalense classe no data will be clear",
    ];


    protected $rules = ['move_type' => 'required|string|bail', 'classe_id' => 'required|int'];

    public $title = "Définition de la nouvelle classe de l'apprenant";


    public function render()
    {
        $classes = [];

        $classe_groups = [];

        if($this->pupil){

            $classes = $this->getClasses();

            $classe_groups = $this->getClasseGroups();

        }
        return view('livewire.move-pupil-to-new-classe', compact('classe_groups', 'classes'));
    }


    public function updatedClasseGroupId($classe_group_id)
    {
        $this->classe_group_id = $classe_group_id;
    }

    public function updatedClasseId($classe_id)
    {
        $this->classe_id = $classe_id;
    }

    public function updatedMoveType($move_type)
    {
        $this->move_type = $move_type;
    }


    public function openModal($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);

        if($pupil){

            $this->pupil = $pupil;

            $this->school_year_model = $this->getSchoolYear();

            $current_classe = $this->pupil->getCurrentClasse($this->school_year_model->id);

            if($current_classe){

                $this->current_classe = $current_classe;

                $this->position = $this->current_classe->position;
            }

            $this->title = "Définition de la nouvelle classe de l'apprenant " . $pupil->getName();


            $this->dispatchBrowserEvent('modal-movePupilToNewClasse');

        }

    }


    public function getClasses()
    {
        $classes = [];

        if($this->move_type == 'just_move'){

            if($this->classe_group_id){

                $classe_group = ClasseGroup::find($this->classe_group_id);

                $classes = $classe_group->classes;

            }
            else{

                $classes = $this->school_year_model->classes;
            }


        }
        elseif($this->move_type == 'same_promotion'){

            $classe_group = $this->current_classe->classe_group;

            $classes = $classe_group->classes;

        }
        elseif($this->move_type == 'migrate'){

            $position = $this->position;

            if($position && $this->current_classe){

                $positions = [$position, $position + 1];

                $classe_groups = $this->school_year_model->classe_groups;

                $classes = $this->school_year_model->classes()->where('classes.level_id', $this->pupil->level_id)->whereIn('classes.position', $positions)->get();

            }
            else{

                if($this->classe_group_id){

                    $classe_group = ClasseGroup::find($this->classe_group_id);

                    $classes = $classe_group->classes;

                }
                else{

                    $classes = $this->school_year_model->classes;
                }

            }

        }
        elseif($this->move_type == 'reset_data' || $this->move_type == 'to_polyvalence'){

            $classes = Classe::where('name', 'like', '%polyvalente%')->where('level_id', $this->pupil->level_id)->get();
            
        }
        else{

            $classes = [];

        }

        return $classes;

    }


    public function getClasseGroups()
    {
        $classe_groups = [];

        if($this->move_type == 'just_move'){

            $classe_groups = $this->school_year_model->classe_groups;

        }
        elseif($this->move_type == 'same_promotion'){

            $data = [];

            $data[] = $this->current_classe->classe_group;

            $classe_groups = $data;

        }
        elseif($this->move_type == 'migrate'){

            $classe_groups = $this->school_year_model->classe_groups;

        }
        elseif($this->move_type == 'reset_data' || $this->move_type == 'to_polyvalence'){

            $classe_groups = [];
            
        }
        else{

            $classe_groups = [];

        }

        return $classe_groups;


    }


    public function submit()
    {
        $this->validate();

        $move_type = $this->move_type;

        DB::transaction(function($e) use ($move_type){

            $pupils = [];

            $pupil = $this->pupil;

            $school_year_model = $this->school_year_model;

            if($move_type == 'reset_data'){

                $pupil->pupilDeleter($school_year_model->id, false);

            }
            else{

                if(in_array($move_type, ['just_move', 'migrate', 'same_promotion', 'to_polyvalence'])){

                    $juste_move = in_array($move_type, ['just_move', 'same_promotion', 'to_polyvalence']) ? true : false;

                    $pupils[] = $pupil;

                    $classe = $school_year_model->findClasse($this->classe_id);

                    if($classe){

                        if(count($pupils)){

                            $classe->pupilsMigraterToClasseForNewSchoolYear($pupils, $school_year_model->id, $juste_move);
                        }

                    }
                    else{
                        $this->dispatchBrowserEvent('Toast', ['title' => 'ECHEC DE LA MISE A JOUR', 'message' => "La migration a échoué!", 'type' => 'error']);

                    }

                }
                
            }

        });


        DB::afterCommit(function(){

            $this->emit('GlobalDataUpdated');

            $this->cancel();

            $this->dispatchBrowserEvent('Toast', ['title' => 'MISE A JOUR TERMINEE', 'message' => "La migration été faite avec succès!", 'type' => 'success']);

            $this->emit('GlobalDataUpdated');


        });
    }


    public function cancel()
    {
        $this->dispatchBrowserEvent('hide-form');
        
        $this->reset('move_type', 'current_classe', 'pupil', 'position', 'classe_id', 'classe_group_id', 'school_year_model');

    }

}
