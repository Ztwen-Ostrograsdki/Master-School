<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\TimePlan;
use Livewire\Component;

class InsertTimePlan extends Component
{
    protected $listeners = ['insertTimePlan' => 'openModal'];
    use ModelQueryTrait;

    public $start = 7;
    public $time_plan = null;
    public $end = 8;
    public $duration = 1;
    public $classe_id = null;
    public $classe;
    public $subject;
    public $subject_id = null;
    public $teacher_id = null;
    public $level_id = null;
    public $school_year_id = null;
    public $day = null;
    public $days = ['Lundi', 'Mardi', 'Mercredi', 'Jeudi', 'Vendredi'];


    protected $rules = [
        'duration' => 'required|numeric|min:1|max:5',
        'start' => 'required|numeric|min:7|max:17',
        'end' => 'required|numeric|min:8|max:19',
        'day' => 'required|string',
        'school_year_id' => 'required',
        'subject_id' => 'required',
        'classe_id' => 'required',
    ];

    public function render()
    {
        $school_year_model = $this->getSchoolYear();

        $classes = $school_year_model->classes;
        
        if($this->classe){
            $subjects = $this->classe->subjects;
        }
        else{
            $subjects = $school_year_model->subjects;
        }
        $levels = Level::all();
        $school_years = SchoolYear::all();

        return view('livewire.insert-time-plan', compact('subjects', 'school_years', 'classes', 'levels'));
    }


    public function openModal($time_plan_id = null)
    {
        $this->school_year_model = $this->getSchoolYear();

        $this->school_year_id = $this->school_year_model->id;
        if($time_plan_id){
            //UPDATING
            $this->time_plan = TimePlan::find($time_plan_id);
        }
        else{
            //CREATING
            $this->reset('time_plan');

        }

        $this->dispatchBrowserEvent('modal-insertTimePlan');
    }


    public function submitTimePlan()
    {
        if($this->start >= $this->end || ($this->end - $this->start) != $this->duration){
            $this->addError('start', "Valeurs ambigües!");
            $this->addError('end', "Valeurs ambigües!");
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vos données sont ambigües, veuillez renseigner des valeurs acceptables", 'type' => 'error']);
        }
        else{
            $this->validate();

            $valid_time_plan = $this->classe->classeWasFreeInThisTime($this->start, $this->end, $this->day, $this->school_year_id);

            if($this->classe->classe_group->hasThisSubject($this->subject_id)){
                if($valid_time_plan){
                    $this->classe->teachers->each(function($teacher){
                        if($teacher->speciality()->id == $this->subject_id){
                            $this->teacher_id = $teacher->id;
                        }
                    });

                    $time_plan = true;

                    $time_plan = TimePlan::create([
                        'classe_id' => $this->classe_id,
                        'subject_id' => $this->subject_id,
                        'teacher_id' => $this->teacher_id,
                        'school_year_id' => $this->school_year_id,
                        'day' => $this->day,
                        'start' => $this->start,
                        'end' => $this->end,
                        'teacher_id' => $this->teacher_id,
                    ]);
                    if($time_plan){
                        $classe_name = $this->classe->name;
                        $subject_name = $this->subject->name;

                        $this->dispatchBrowserEvent('hide-form');
                        $this->resetErrorBag();

                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "La classe {$classe_name} est désormais programmée pour le cours de {$subject_name} les {$this->day}s de {$this->start}H à {$this->end}H ! ", 'type' => 'success']);

                        $this->reset('classe', 'classe_id', 'subject', 'subject_id', 'teacher_id', 'level_id', 'school_year_id', 'day', 'start', 'end', 'duration');
                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur', 'message' => "Une erreure est survenue lors de la mise à jour!", 'type' => 'error']);
                    }
                }
                else{
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'HORAIRE DEJA OCCUPEE', 'message' => "La classe est déjà occupée les $this->day entre {$this->start}H et {$this->end}H ! ", 'type' => 'warning']);
                }
                
            }
            else{
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'MATIERE INCOMPATIBLE', 'message' => "La matière $this->subject->name n'est pas enseignée dans la classe de $this->classe->name !", 'type' => 'warning']);
            }
        }
    }


    public function updatedClasseId($classe_id)
    {
        $this->resetErrorBag('classe_id', 'start', 'duration', 'day', 'end');

        $this->classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();
    }

    public function updatedSubjectId($subject_id)
    {
        $this->resetErrorBag('subject_id');
        $this->subject = $this->school_year_model->subjects()->where('subjects.id', $subject_id)->first();
    }

    public function updatedDuration($duration)
    {
        $this->resetErrorBag('end', 'start', 'duration', 'day');
        $this->end = $this->start + $duration;
    }


    public function updatedStart($start)
    {
        $this->resetErrorBag('end', 'start', 'duration', 'day');
        $this->end = $start + $this->duration;
    }


    



}
