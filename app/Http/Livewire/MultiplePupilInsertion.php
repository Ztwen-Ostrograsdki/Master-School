<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Pupil;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class MultiplePupilInsertion extends Component
{
    use ModelQueryTrait;

    public $size = 2;
    public $pupils;


    protected $rules = [

    ];


    public function incrementSize()
    {
        $this->size = $this->size--;
    }

    public function decrementSize()
    {
        $this->size = $this->size++;
    }



    public function render()
    {
        $levels = Level::all();
        $classes = Classe::all();
        return view('livewire.multiple-pupil-insertion', compact('levels', 'classes'));
    }


    public function submit()
    {
        dd($this);
        $this->validate();

        return false;
        if($this->classe_id){
            $classe = $this->school_year_model->classes()->where('classes.id', $this->classe_id)->first();
            if($classe->level_id == $this->level_id){
                $pupilNameHasAlreadyTaken = Pupil::where('lastName', $this->lastName)->where('firstName', $this->firstName)->first();
                if(!$pupilNameHasAlreadyTaken){
                    DB::transaction(function($e) {
                        $pupil = Pupil::create(
                            [
                                'firstName' => strtoupper($this->firstName),
                                'lastName' => $this->lastName,
                                'classe_id' => $this->classe_id,
                                'contacts' => $this->contacts,
                                'sexe' => $this->sexe,
                                'birth_day' => $this->birth_day,
                                'nationality' => $this->nationality,
                                'birth_city' => $this->birth_city,
                                'level_id' => $classe->level_id,
                                'residence' => $this->residence,
                                'last_school_from' => $this->last_school_from
                            ]
                        );
                        if($pupil){
                            $this->school_year_model->pupils()->attach($pupil->id);
                            $classe->classePupils()->attach($pupil->id);
                            $this->dispatchBrowserEvent('hide-form');
                            $this->resetErrorBag();
                            $this->reset('firstName', 'lastName', 'classe_id', 'contacts', 'sexe', 'birth_day', 'nationality', 'birth_city', 'level_id', 'residence', 'last_school_from');
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Inscription terminée', 'message' => "l'appreant $pupil->firstName $pupil->lastName a été ajouté à la classe de $classe->name avec succès! ", 'type' => 'success']);
                            $this->emit('classePupilListUpdated', $classe->id);
                        }

                        $this->emit('newPupilHasBeenAdded');

                    });
                }
                else{
                    $this->addError('lastName', "Un apprenant est déjà inscrit sous ce nom et prénoms");
                    $this->addError('firstName', "Un apprenant est déjà inscrit sous ce nom et prénoms");
                }
            }
            else{
                $this->addError('level_id', 'Le cycle ne correspond pas à la classe sélectionnée');
            }
        }
    }


}
