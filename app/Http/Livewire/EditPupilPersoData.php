<?php

namespace App\Http\Livewire;

use App\Models\Pupil;
use Livewire\Component;
use App\Helpers\ModelsHelpers\ModelQueryTrait;

class EditPupilPersoData extends Component
{
    use ModelQueryTrait;
    protected $listeners = [
        'editPupilPersoDataLiveEvent' => 'loadDataAndOpenModal'
    ];
    public $pupil;
    public $pupil_id;
    public $firstName;
    public $lastName;
    public $sexe;
    public $birth_day;
    public $contacts;
    public $nationality;
    public $birth_city;
    public $residence;
    public $last_school_from;
    public $school_year_model;

    protected $rules = [
        'firstName' => 'required|string|min:2',
        'lastName' => 'required|string|min:2',
    ];


    public function mount()
    {
        $this->school_year_model = $this->getSchoolYear();
    }

    public function render()
    {
        return view('livewire.edit-pupil-perso-data');
    }


    public function loadDataAndOpenModal($pupil_id)
    {
        $pupil = Pupil::find($pupil_id);
        $this->pupil = $pupil;
        if($pupil){
            $this->pupil_id = $pupil_id;
            $this->firstName = $pupil->firstName;
            $this->lastName = $pupil->lastName;
            $this->contacts = $pupil->contacts;
            $this->birth_day = $pupil->birth_day;
            $this->birth_city = $pupil->birth_city;
            $this->residence = $pupil->residence;
            $this->nationality = $pupil->nationality;
            $this->sexe = $pupil->sexe;
            $this->last_school_from = $pupil->last_school_from;
            $this->dispatchBrowserEvent('modal-editPupilPersoData');
        }
        
    }


    public function submit()
    {
        $this->validate();
        $pupilNameHasAlreadyTaken = Pupil::where('lastName', $this->lastName)->where('firstName', $this->firstName)->where('id', '<>', $this->pupil_id)->first();
        if(!$pupilNameHasAlreadyTaken){
            $pupil = $this->pupil->update([
                'firstName' => strtoupper($this->firstName),
                'lastName' => $this->lastName,
                'contacts' => $this->contacts,
                'sexe' => $this->sexe,
                'birth_day' => $this->birth_day,
                'nationality' => $this->nationality,
                'birth_city' => $this->birth_city,
                'residence' => $this->residence,
                'last_school_from' => $this->last_school_from
            ]);
            if($pupil){
                $pupil = Pupil::find($this->pupil_id);
                $this->dispatchBrowserEvent('hide-form');
                $this->resetErrorBag();
                $this->reset('firstName', 'lastName', 'contacts', 'sexe', 'birth_day', 'nationality', 'birth_city', 'residence', 'last_school_from', 'pupil_id');
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Mise ?? jour termin??e', 'message' => "Les donn??es de l'apprenant $pupil->firstName $pupil->lastName ont ??t?? mise ?? jour avec succ??s! ", 'type' => 'success']);
                $this->emit('classePupilListUpdated', $pupil->classe_id);
                $this->emit('pupilUpdated', $pupil->id);
            }
        }
        else{
            $this->addError('lastName', "Un apprenant est d??j?? inscrit sous ce nom et pr??noms");
            $this->addError('firstName', "Un apprenant est d??j?? inscrit sous ce nom et pr??noms");
        }
    }

}
