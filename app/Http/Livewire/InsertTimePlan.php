<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Models\Classe;
use App\Models\Level;
use App\Models\SchoolYear;
use App\Models\Subject;
use App\Models\TimePlan;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class InsertTimePlan extends Component
{
    protected $listeners = [
        'insertTimePlan' => 'openModal', 
        'InsertTeacherTimePlans' => 'openModalForTeacherTimePlans', 
        'AddNewTimePlanForThisClasse' => 'openModalForClasse',
        'EditTimePlan' => 'openModalForUpdate',
    ];
    use ModelQueryTrait;

    public $start = 7;
    public $time_plan = null;
    public $end = 8;
    public $duration = 1;
    public $classe_id = null;
    public $for_classe = false;
    public $hasErrors = false;
    public $teacher_with_classe = false;
    public $classe;
    public $targetClasse;
    public $subject;
    public $teacher = null;
    public $subject_id = null;
    public $teacher_id = null;
    public $level_id = null;
    public $school_year_id = null;
    public $day = null;
    public $times_plans = [];
    public $days = [
        1 => 'Lundi',
        2 => 'Mardi', 
        3 =>'Mercredi',
        4 => 'Jeudi', 
        5 => 'Vendredi'
    ];


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

        $levels = [];

        $classes = [];

        $classes = $school_year_model->classes;
        
        if($this->teacher){

            if($this->teacher_with_classe){

                $classes[] = $this->classe;
            }
            else{

                $classes = $this->teacher->getTeachersCurrentClasses();
            }

            $subjects = $this->teacher->subjects;

            $levels[] = $this->teacher->level;
        }
        else{

            if($this->classe){

                $subjects = $this->classe->subjects;

            }
            else{

                $subjects = $school_year_model->subjects;
            }

            $levels = Level::all();
        }

        
        $school_years = SchoolYear::all();

        return view('livewire.insert-time-plan', compact('subjects', 'school_years', 'classes', 'levels'));
    }


    public function retrieveFromTimesPlans()
    {
        if(count($this->times_plans) > 0){
            $last = count($this->times_plans);
            array_pop($this->times_plans);
            $this->dispatchBrowserEvent('Toast', ['title' => 'DERNIER PROGRAMME EFFACEE', 'message' => "Le dernier programme ajouté a été retiré!", 'type' => 'info']);
        }
    }


    public function openModalForUpdate($classe_id, $start, $end, $day, $from_classe = false, $school_year = null)
    {
        $school_year_model = $this->getSchoolYear($school_year);

        $classe = Classe::find($classe_id);
        $this->resetErrorBag();

        if($classe){

            $time_plan = $classe->getTimePlan($day, $start, $end, null,  $school_year);

            if($time_plan){

                $this->time_plan = $time_plan;

                $this->classe_id = $classe_id;

                $this->start = $time_plan->start;

                $this->end = $time_plan->end;

                $this->duration = $time_plan->duration;

                $this->subject_id = $time_plan->subject_id;

                $this->school_year_id = $time_plan->school_year_id;

                $this->level_id = $time_plan->level_id;

                $this->day = $time_plan->day;

                $this->classe = $classe;

                $this->for_classe = $from_classe;

                if($this->for_classe){

                    $this->targetClasse = $classe;
                }

                $this->dispatchBrowserEvent('modal-insertTimePlan');
            }


        }

    }

    

    public function openModal($time_plan_id = null)
    {
        $this->reset('hasErrors');
        $this->resetErrorBag();

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



    public function openModalForClasse($classe_id)
    {
        $this->reset('hasErrors');
        $this->resetErrorBag();

        $this->school_year_model = $this->getSchoolYear();

        $this->school_year_id = $this->school_year_model->id;

        $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();

        if($classe){

            $this->classe = $classe;

            $this->targetClasse = $classe;

            $this->for_classe = true;

            $this->classe_id = $classe_id;

            $this->level_id = $classe->level_id;

            $this->dispatchBrowserEvent('modal-insertTimePlan');
            
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Données introuvables!", 'type' => 'error']);

        }

        
    }


    public function openModalForTeacherTimePlans($teacher_id, $classe_id)
    {
        $this->reset('hasErrors');
        $this->resetErrorBag();

        $this->school_year_model = $this->getSchoolYear();

        if($teacher_id){

            $teacher = $this->school_year_model->teachers()->where('teachers.id', $teacher_id)->first();

            if($teacher){

                $this->school_year_id = $this->school_year_model->id;

                $this->teacher = $teacher;

                $this->teacher_id = $teacher_id;

                $this->subject = $teacher->speciality();

                $this->subject_id = $this->subject->id;

                $this->level_id = $teacher->level_id;

                if($classe_id){

                    $classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();

                    if($classe){

                        $this->teacher_with_classe = true;

                        $this->classe = $classe;

                        $this->classe_id = $classe->id;
                    }
                    else{

                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Données introuvables!", 'type' => 'error']);
                    }
                }
                else{

                    $this->teacher_with_classe = false;

                }
                $this->dispatchBrowserEvent('modal-insertTimePlan');

            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Données introuvables!", 'type' => 'error']);
            }
        }

        
    }

    public function pushIntoTimesPlans()
    {
        $this->reset('hasErrors');

        if($this->start >= $this->end || ($this->end - $this->start) != $this->duration){

            $this->addError('start', "Valeurs ambigües!");

            $this->addError('end', "Valeurs ambigües!");

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vos données sont ambigües, veuillez renseigner des valeurs acceptables", 'type' => 'error']);
        }
        else{

            $this->validate();

            $valid_time_plan_for_classe = $this->classe->classeWasFreeInThisTime($this->start, $this->end, $this->day, $this->school_year_id);

            $hasAlreadyQuota = $this->classe->hasQuota($this->subject_id, $this->school_year_id);

            if(!$hasAlreadyQuota){

                $classe_name = $this->classe->name;

                $subject_name = $this->classe->subjects()->where('subjects.id', $this->subject_id)->first() ? $this->classe->subjects()->where('subjects.id', $this->subject_id)->first()->name : '';

                $this->dispatchBrowserEvent('Toast', ['title' => 'QUOTA HORAIRE PAS ENCORE DEFINI', 'message' => "Le quota horaire de $subject_name de la classe $classe_name n'a pas encore été définis ! Veuillez insérer le quota horaire en premier avant l'insertion d'un emploi du temps !", 'type' => 'warning']);
                
                return false;

            }



            if($this->classe->classe_group->hasThisSubject($this->subject_id)){

                if($valid_time_plan_for_classe){

                    $this->classe->teachers->each(function($teacher){

                        if($teacher->speciality()->id == $this->subject_id){

                            $this->teacher_id = $teacher->id;
                        }

                    });

                    if($this->teacher){

                        $valid_time_plan_for_teacher = $this->teacher->teacherWasFreeInThisTime($this->start, $this->end, $this->day, $this->school_year_id);

                        if($valid_time_plan_for_teacher){

                            $user = auth()->user();

                            $time_plan = [
                                'creator' => $user->id,
                                'user_id' => $user->id,
                                'level_id' => $this->classe->level_id,
                                'classe_id' => $this->classe_id,
                                'subject_id' => $this->subject_id,
                                'teacher_id' => $this->teacher_id,
                                'school_year_id' => $this->school_year_id,
                                'day' => $this->day,
                                'day_index' => (array_flip($this->days))[$this->day],
                                'start' => $this->start,
                                'end' => $this->end,
                                'duration' => $this->duration,
                                'teacher_id' => $this->teacher_id
                            ];

                            $this->times_plans[] = $time_plan;

                            $classe_name = $this->classe->name;

                            $subject_name = $this->subject->name;

                            $this->dispatchBrowserEvent('Toast', ['title' => 'PROGRAMME AJOUTEE', 'message' => "La classe {$classe_name} fera désormais pour le cours de {$subject_name} les {$this->day}s de {$this->start}H à {$this->end}H ! ", 'type' => 'success']);

                            if($this->teacher_with_classe){

                                $this->reset('day', 'start', 'end', 'duration');
                            }
                            elseif($this->teacher){

                                $this->reset('subject', 'subject_id', 'teacher_id', 'day', 'start', 'end', 'duration');
                            }
                            else{

                                $this->reset('classe', 'classe_id', 'subject', 'subject_id', 'teacher_id', 'day', 'start', 'end', 'duration');
                            }
                        }
                        else{

                            $this->addError('start', "Occupées!");

                            $this->addError('end', "Occupées!");

                            $this->addError('day', "Occupés!");

                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'PROF DEJA OCCUPEE', 'message' => "Le prof est déjà occupée les $this->day entre {$this->start}H et {$this->end}H ! ", 'type' => 'warning']);
                        }
                    }

                    else{

                        $user = auth()->user();

                        $time_plan = [
                            'creator' => $user->id,
                            'user_id' => $user->id,
                            'level_id' => $this->classe->level_id,
                            'classe_id' => $this->classe_id,
                            'subject_id' => $this->subject_id,
                            'teacher_id' => $this->teacher_id,
                            'school_year_id' => $this->school_year_id,
                            'day' => $this->day,
                            'day_index' => (array_flip($this->days))[$this->day],
                            'start' => $this->start,
                            'end' => $this->end,
                            'duration' => $this->duration,
                            'teacher_id' => $this->teacher_id
                        ];
                        
                        $this->times_plans[] = $time_plan;
                        
                        $classe_name = $this->classe->name;
                        
                        $subject_name = $this->subject->name;
                        
                        $this->dispatchBrowserEvent('Toast', ['title' => 'PROGRAMME AJOUTEE', 'message' => "La classe {$classe_name} fera désormais le cours de {$subject_name} les {$this->day}s de {$this->start}H à {$this->end}H ! ", 'type' => 'success']);
                        
                        if($this->teacher_with_classe){
                            
                            $this->reset('day', 'start', 'end', 'duration');

                        }
                        
                        elseif($this->teacher){
                            
                            $this->reset('subject', 'subject_id', 'teacher_id', 'day', 'start', 'end', 'duration');
                        }
                        else{
                        
                            $this->reset('classe', 'classe_id', 'subject', 'subject_id', 'teacher_id', 'day', 'start', 'end', 'duration');
                        }
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


    public function updaterManager()
    {

        $school_year_model = $this->getSchoolYear($this->school_year_id);

        if($this->start >= $this->end || ($this->end - $this->start) != $this->duration){

            $this->addError('start', "Valeurs ambigües!");

            $this->addError('end', "Valeurs ambigües!");

            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vos données sont ambigües, veuillez renseigner des valeurs acceptables", 'type' => 'error']);
        }
        else{

            $this->validate();

            $valid_time_plan_for_teacher = true;

            $cursus = $school_year_model->teacherCursus()->where('teacher_cursuses.classe_id', $this->classe_id)->where('teacher_cursuses.subject_id', $this->subject_id)->whereNull('teacher_cursuses.end')->first();

            if($cursus){

                $teacher = $cursus->teacher;

                $valid_time_plan_for_teacher = $teacher->teacherWasFreeInThisTime($this->start, $this->end, $this->day, $this->school_year_id, $this->time_plan->id);

            }

            $valid_time_plan_for_classe = $this->classe->classeWasFreeInThisTime($this->start, $this->end, $this->day, $this->school_year_id, $this->time_plan->id);

            if($valid_time_plan_for_classe){

                if($valid_time_plan_for_teacher){

                    $user = auth()->user();

                    $time_plan = [
                        'editor' => $user->id,
                        'user_id' => $user->id,
                        'level_id' => $this->classe->level_id,
                        'classe_id' => $this->classe_id,
                        'subject_id' => $this->subject_id,
                        'teacher_id' => $this->teacher_id,
                        'school_year_id' => $this->school_year_id,
                        'day' => $this->day,
                        'day_index' => (array_flip($this->days))[$this->day],
                        'start' => $this->start,
                        'end' => $this->end,
                        'duration' => $this->duration,
                        'teacher_id' => $this->teacher_id
                    ];

                    $subject_id = $time_plan['subject_id'];

                    $duration = $time_plan['duration'];

                    $canPushThisDuration = $this->classe->canPushThisDurationTo($subject_id, $duration, $this->school_year_id, $this->time_plan->id);

                    if($canPushThisDuration){

                        DB::transaction(function($e) use ($time_plan){

                        
                            $updated = $this->time_plan->update($time_plan);

                            if($updated){

                                $subject = Subject::find($this->subject_id);

                                $classe_name = $this->classe->name;
                            
                                $subject_name = $subject->name;

                                $this->resetErrorBag();

                                $this->dispatchBrowserEvent('hide-form');
                                
                                $this->dispatchBrowserEvent('Toast', ['title' => 'PROGRAMME MISE A JOUR', 'message' => "La classe {$classe_name} fera désormais le cours de {$subject_name} les {$this->day}s de {$this->start}H à {$this->end}H ! ", 'type' => 'success']);

                                $this->emit('timePlanTablesWasUpdatedLiveEvent');

                                $this->emit('RefreshTimePlanLiveEvent');

                                if($this->targetClasse){

                                    $this->emit('RefreshTimePlanIntoClasseProfilLiveEvent', $this->targetClasse->id);
                                }

                                $this->reset('classe', 'classe_id', 'subject', 'subject_id', 'teacher_id', 'level_id', 'school_year_id', 'day', 'start', 'end', 'duration', 'times_plans', 'teacher_with_classe', 'teacher', 'targetClasse');

                            }

                        });

                    }
                    else{

                        return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "LIMITE QUOTA ATTEINT", 'message' => "EMPLOI NON INSERE: Le quota horaire a peut-être été atteint ou n'a pas encore été défini!", 'type' => 'warning']);
                    }



                }
                else{

                    $this->addError('start', "Occupées!");

                    $this->addError('end', "Occupées!");

                    $this->addError('day', "Occupés!");

                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'PROF DEJA OCCUPEE', 'message' => "Le prof est déjà occupée les $this->day entre {$this->start}H et {$this->end}H ! ", 'type' => 'warning']);

                }

            }
            else{

                $this->addError('start', "Occupées!");

                $this->addError('end', "Occupées!");

                $this->addError('day', "Occupés!");

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'HORAIRE DEJA OCCUPEE', 'message' => "La classe est déjà occupée les $this->day entre {$this->start}H et {$this->end}H ! ", 'type' => 'warning']);

            }

        }
    }

    public function submitTimePlan()
    {

        if($this->time_plan){
            //UPDATING
            $this->updaterManager();
        }


        // $this->school_year_model->timePlans()->delete();
        if(!$this->time_plan){

            if(count($this->times_plans) > 0){

                DB::transaction(function($e){

                    foreach($this->times_plans as $plan){

                        $classe = Classe::find($plan['classe_id']);

                        $subject_id = $plan['subject_id'];

                        $duration = $plan['duration'];

                        $canPushThisDuration = $classe->canPushThisDurationTo($subject_id, $duration);

                        if($canPushThisDuration){

                            $time_plan = TimePlan::create($plan);
                        }
                        else{

                            $this->hasErrors = true;

                            // TOTAL QUOTA ACCORDING WAS ATTEMPS OR NO QUOTA WAS'NT YET DEFINED FOR 

                        }
                    }
                });

                DB::afterCommit(function(){

                    if($this->hasErrors){

                        return $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => "LIMITE QUOTA ATTEINT", 'message' => "EMPLOI NON INSERE: Le quota horaire a peut-être été atteint ou n'a pas encore été défini!", 'type' => 'warning']);

                    }
                    else{

                        $this->emit('timePlanTablesWasUpdatedLiveEvent');

                        if($this->targetClasse){

                            $this->emit('RefreshTimePlanIntoClasseProfilLiveEvent', $this->targetClasse->id);
                        }

                        $this->dispatchBrowserEvent('hide-form');

                        $this->resetErrorBag();

                        $this->dispatchBrowserEvent('Toast', ['title' => 'Mise à jour réussie', 'message' => "Les programmes ont été inséré avec succès!", 'type' => 'success']);

                        $this->reset('classe', 'classe_id', 'subject', 'subject_id', 'teacher_id', 'level_id', 'school_year_id', 'day', 'start', 'end', 'duration', 'times_plans', 'teacher_with_classe', 'teacher', 'targetClasse');

                    }
                    
                });

            }
            else{

                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'AUCUN PROGRAMME RENSEIGNE', 'message' => "Vous n'avez ajouté aucun programme!", 'type' => 'error']);
            }

        }
    }


    public function updatedClasseId($classe_id)
    {
        $this->resetErrorBag('classe_id', 'start', 'duration', 'day', 'end');

        $this->classe = $this->school_year_model->classes()->where('classes.id', $classe_id)->first();

        $this->level_id = $this->classe->level_id;
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

    public function updatedEnd($end)
    {
        $this->resetErrorBag('end', 'start', 'duration', 'day');

        if(abs($this->end - $this->start) < 6){

            $this->duration = abs($this->end - $this->start);
        }
        else{

            $this->duration = 5;

            $this->end = $this->start + 5;
        }
    }


    public function updatedDay($day)
    {

        $this->resetErrorBag('day');

        $this->day = $day;

    }


    



}
