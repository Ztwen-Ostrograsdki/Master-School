<?php

namespace App\Http\Livewire;

use App\Events\MakeClassePresenceLateEvent;
use App\Helpers\DateFormattor;
use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Pupil;
use App\Models\PupilAbsences;
use Illuminate\Support\Carbon;
use Livewire\Component;

class PresenceLateModal extends Component
{

    use ModelQueryTrait, DateFormattor;

    protected $listeners = ['makeClassePresence' => 'openModal'];

    public $abs_data = [];

    public $data = [];

    public $lates_data = [];

    public $hiddens = [];

    public $school_year_model;

    public $coming_hour_H;
    
    public $coming_hour_M;

    public $classe_id;

    public $classe;

    public $horaire;

    public $pupil_id;

    public $public;

    public $motif = 'Sans motif';

    public $object = 'absent';

    public $subject;

    public $subject_id;

    public $start;

    public $end;

    public $date_as_string = "Veuillez renseigner la date et l'horaire";

    public $last_pupil_id;

    protected $rules = [
        'date' => 'required|date',
        'start' => 'required|numeric|min:7|max:19',
        'end' => 'required|numeric|min:7|max:19',
        'motif' => 'required|string|min:5'
    ];


    public function render()
    {

        $pupils = [];

        $semestre_type = 'Semestre';

        $semestres = $this->getSemestres();

        if(count($semestres) > 2){

            $semestre_type = 'Trimestre';

        }



        if($this->classe){

            $pupils = $this->classe->getPupils($this->school_year_model->id);
        }

        return view('livewire.presence-late-modal', compact('pupils', 'semestres', 'semestre_type'));
    }


    public function hidePupil($pupil_id)
    {
        $hiddens = $this->hiddens;

        if(!in_array($pupil_id, $hiddens)){

            $hiddens[$pupil_id] = $pupil_id;

        }

        $this->hiddens = $hiddens;

    }


    public function pushIntoAbsents($pupil_id)
    {
        $abs_data = $this->abs_data;

        if(!array_key_exists($pupil_id, $abs_data)){

            $this->validate();

            $this->last_pupil_id = $pupil_id;

            $abs_data[$pupil_id] = [
                'pupil_id' => $pupil_id,
                'motif' => $this->motif,

            ];

        }

        $this->abs_data = $abs_data;

        $this->reset('motif');

    }

    public function pushIntoLates($pupil_id)
    {
        $lates_data = $this->lates_data;

        if(!array_key_exists($pupil_id, $lates_data)){

            $this->validate();

            $this->validate([
                'duration' => 'required|numeric|min:1',
                'coming_hour_H' => 'required|numeric|min:7|max:19', 
                'coming_hour_M' => 'required|int|min:0|max:59'
            ]);

            $this->last_pupil_id = $pupil_id;

            $coming_hour = $this->coming_hour_H . ':' . $this->coming_hour_M . ':' . 00; 
            
            $lates_data[$pupil_id] = [
                'pupil_id' => $pupil_id,
                'motif' => $this->motif,
                'duration' => $this->duration,
                'coming_hour' => $coming_hour,

            ];

        }

        $this->lates_data = $lates_data;

        $this->reset('motif');

    }


    public function cancelLast()
    {
        $lates_data = $this->lates_data;

        $abs_data = $this->abs_data;

        $pupil_id = $this->last_pupil_id;

        if(array_key_exists($pupil_id, $lates_data)){

            unset($lates_data[$pupil_id]);

            $this->lates_data = $lates_data;

        }
        elseif (array_key_exists($pupil_id, $abs_data)) {
            
            unset($abs_data[$pupil_id]);

            $this->abs_data = $abs_data;
        }
    }


    public function remake()
    {
        $this->reset('lates_data', 'abs_data', 'hiddens', 'last_pupil_id', 'motif');
    }


    public function submit()
    {
        $lates_data = $this->lates_data;

        $abs_data = $this->abs_data;

        $classe_id = $this->classe_id;

        $start = $this->start;

        $end = $this->end;

        $user = auth()->user();

        if($this->date){

            setlocale(LC_TIME, "fr_FR.utf8", 'fra');

            $time = ucfirst(Carbon::parse($this->date)->timestamp);

            $day = strftime('%A', $time);

        }

        $has_courses_at_this_horar = $user->teacher->teacherHasCourseAtThis($classe_id, $start, $end, $day);

        if($has_courses_at_this_horar){

            if(count($abs_data) > 0 || count($lates_data) > 0){

                

                $data = [

                    'default' => [

                        'horaire' => $this->start . 'H - ' . $this->end . 'H',
                        'date' => $this->date,
                        'school_year_model' => $this->school_year_model,
                        'semestre' => $this->semestre_selected,
                        'subject_id' => $this->subject->id,
                    ],

                    'lates_data' => $lates_data,

                    'abs_data' => $abs_data,

                ];

                MakeClassePresenceLateEvent::dispatch($user, $this->classe, $data);

                $this->resetor();

                $this->dispatchBrowserEvent('Toast', ['title' => 'LE PROCESSUS A ETE LANCE', 'message' => "Le processus de présence a été lancé en arrière plan!", 'type' => 'success']);

            }

        }
        else{

            $name = $user->teacher->getFormatedName();

            $respect = $user->sexe ? ($user->sexe == 'male' ? "Mr" : "Mme") : "Mr/Mme";

            $sentence = "Désolé $respect $name car, vous ne faites pas cours les " . ucfirst($day) . "s de " . $start . "H à " . $end . "H !";

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'HORAIRE INVALIDE', 'message' => $sentence, 'type' => 'error']);

        }
    }


    public function openModal($classe_id)
    {
        $this->reset('lates_data', 'abs_data', 'hiddens', 'last_pupil_id', 'motif');

        $user = auth()->user();

        $classe = Classe::find($classe_id);

        $subject = $user->teacher->speciality();

        $time_plans_was_already_set = $user->teacher->hasTimePlansForThisClasse($classe->id);

        $time_plans_was_already_set = 1;

        if($time_plans_was_already_set){

            if($classe && $subject){

                $this->classe = $classe;

                $this->subject = $subject;

                $this->classe_id = $classe->id;

                $this->semestre_selected = session('semestre_selected');

                $this->school_year_model = $this->getSchoolYear();

                $this->date = (new \DateTime(Carbon::today()))->format('Y-m-d');

                $this->start = intval(date('H')) + 1;

                $this->end = intval(date('H')) + 2;

                $this->coming_hour_H = $this->start;

                $this->coming_hour_M = intval(date('i'));

                $this->duration =  $this->coming_hour_M;

                $this->motif = 'Sans motif';

                $this->setDate($this->date);

                $this->dispatchBrowserEvent('modal-makeClassePresenceLate');

            }

        }
        elseif($classe){

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'EMPLOI DU TEMPS PAS ENCORE DEFINI!', 'message' => "Veuillez renseigner votre emploi du temps pour la classe de $classe->name en premier avant de faire la présence. Si cela n'est pas votre prérogative, veuillez donc vous rapprocher de l'administration. Merci!", 'type' => 'warning']);

        }
        else{

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'CLASSE INTROUVABLE!', 'message' => "Il semble que vous n'ayez pas accès à cette classe ou qu'elle ait été temporairement fermé. Veuillez vous rapprocher de l'administration. Merci!", 'type' => 'info']);


        }
    }

    public function resetor()
    {
        $this->reset('last_pupil_id', 'horaire', 'start', 'end', 'coming_hour_M', 'coming_hour_H', 'abs_data', 'hiddens', 'lates_data', 'motif');

        $this->dispatchBrowserEvent('hide-form');
    }


    public function updatedDate($date)
    {
        $this->date = $date;

        $this->setDate($date);
    }

    public function updatedStart($start)
    {
        $this->setDate($this->date);

        $this->coming_hour_H = $start;
    }

    public function updatedEnd($end)
    {
        $this->setDate($this->date);
    }


    public function setDate($date)
    {
        if($date){

            $this->date_as_string = "Date de cours: Le " . ucwords($this->__getDateAsString($this->date, false)) . " | Horaire : "  . $this->start . "H à " . $this->end . "H!  |  Cours: " . $this->subject->name;
        }
    }


}
