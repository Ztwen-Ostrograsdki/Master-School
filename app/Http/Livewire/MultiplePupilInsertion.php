<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\ClassePupilSchoolYear;
use App\Models\Level;
use App\Models\Pupil;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Livewire\Component;

class MultiplePupilInsertion extends Component
{
    use ModelQueryTrait;
    protected $listeners = ['insertMultiplePupils' => 'openModal'];
    public $pupil;
    public $firstName;
    public $firstNames;
    public $lastName;
    public $lastNames;
    public $sexe = 'male';
    public $level_id;
    public $birth_day;
    public $contacts = '01010101';
    public $nationality = 'Béninoise';
    public $birth_city = 'Ville de naissance';
    public $residence = 'Ma résidence';
    public $last_school_from = 'Mon ancienne école';
    public $school_year_model;
    public $classe_id;
    public $classe;

    protected $rules = [
        'firstName' => 'required|string|min:2',
        'lastName' => 'required|string|min:2',
        'classe_id' => 'required|numeric',
        'contacts' => 'string|min:8',
        'sexe' => 'string',
        'birth_day' => 'date',
        'nationality' => 'string',
        'birth_city' => 'string',
        'level_id' => 'required|numeric',
        'residence' => 'string',
        'last_school_from' => 'required|string|min:2',
    ];


    public function mount()
    {
        $this->birth_day = (new \DateTime(Carbon::today()))->format('Y-m-d');
    }

    public function render()
    {
        $classes = [];
        $levels = Level::all();
        $this->school_year_model = $this->getSchoolYear();
        if($this->school_year_model){
            $classes = $this->school_year_model->classes;
        }
        return view('livewire.multiple-pupil-insertion', compact('levels', 'classes'));
    }


    public function openModal($classe_id)
    {
        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
        $this->school_year_model = $this->getSchoolYear();
        $this->classe = $classe;
        $this->classe_id = $classe->id;
        $this->level_id = $classe->level_id;
        $this->dispatchBrowserEvent('modal-insertMultiplePupils');
    }


    public function submit()
    {

        $firstNames = explode(';', trim($this->firstNames));
        $lastNames = explode(';', trim($this->lastNames));

        $pupils = [];

        if($this->classe_id){
            $this->classe = $this->school_year_model->classes()->where('classes.id', $this->classe_id)->first();
            if($this->classe->level_id == $this->level_id){
                if($firstNames && $lastNames && count($lastNames) && count($firstNames) && count($lastNames) == count($firstNames)){
                    $size = count($lastNames);

                    DB::transaction(function($e) use ($lastNames, $firstNames, $size){
                        for ($i=0; $i < $size; $i++) { 
                            $this->firstName = $firstNames[$i];
                            $this->lastName = $lastNames[$i];
                            $last_id = 0;
                            $last = Pupil::latest()->first();
                            if($last){
                                $last_id = $last->id;
                            }

                            $matricule = date('Y'). '' .Str::random(3) . 'CSNDA' . ($last_id + 1);
                            $pupilNameHasAlreadyTaken = Pupil::where('lastName', $this->lastName)->where('firstName', $this->firstName)->first();
                            if(!$pupilNameHasAlreadyTaken){
                                try {
                                    $pupil = Pupil::create(
                                        [
                                            'firstName' => trim(strtoupper($this->firstName)),
                                            'lastName' => trim(ucwords($this->lastName)),
                                            'classe_id' => $this->classe_id,
                                            'contacts' => $this->contacts,
                                            'sexe' => $this->sexe,
                                            'matricule' => $matricule,
                                            'birth_day' => $this->birth_day,
                                            'nationality' => $this->nationality,
                                            'birth_city' => $this->birth_city,
                                            'level_id' => $this->classe->level_id,
                                            'residence' => $this->residence,
                                            'last_school_from' => $this->last_school_from
                                        ]
                                    );
                                    if($pupil){
                                        try {
                                            $joinedToClasseAndSchoolYear = ClassePupilSchoolYear::create(
                                                [
                                                    'classe_id' => $this->classe_id,
                                                    'pupil_id' => $pupil->id,
                                                    'school_year_id' => $this->school_year_model->id,
                                                ]
                                            );
                                            if($joinedToClasseAndSchoolYear){
                                                try {
                                                    $this->school_year_model->pupils()->attach($pupil->id);
                                                    $this->classe->classePupils()->attach($pupil->id);
                                                } catch (Exception $e3) {
                                                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'apprenant a échoué!", 'type' => 'error']);
                                                    
                                                }
                                            }
                                            
                                        } catch (Exception $e2) {
                                            $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'apprenant a échoué!", 'type' => 'error']);
                                        }
                                    }
                                    else{
                                        $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'apprenant a échoué!", 'type' => 'error']);
                                    }
                                    
                                } catch (Exception $e1) {
                                    $this->dispatchBrowserEvent('Toast', ['title' => 'Erreure serveur', 'message' => "L'insertion de l'apprenant a échoué!", 'type' => 'error']);
                                    
                                }

                            }

                            
                        }
                    });
                    DB::afterCommit(function(){
                        $this->dispatchBrowserEvent('hide-form');
                        $this->resetErrorBag();
                        $this->reset('firstNames', 'lastNames', 'lastName', 'firstName', 'classe_id', 'contacts', 'sexe', 'nationality', 'birth_city', 'level_id', 'residence', 'last_school_from');
                        $this->dispatchBrowserEvent('Toast', ['title' => 'Inscription terminée', 'message' => "La classe a été mise à jour avec succès! ", 'type' => 'success']);
                        $this->emit('classePupilListUpdated');
                        $this->emit('newPupilHasBeenAdded');

                    });

                }






            }






        }










    }



}
