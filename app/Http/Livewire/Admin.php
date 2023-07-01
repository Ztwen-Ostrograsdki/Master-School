<?php

namespace App\Http\Livewire;

use App\Helpers\ModelsHelpers\ModelQueryTrait;
use App\Helpers\ZtwenAssert;
use App\Models\Classe;
use App\Models\Level;
use App\Models\Mark;
use App\Models\Pupil;
use App\Models\School;
use App\Models\SchoolYear;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;
use Livewire\Component;


class Admin extends Component
{
    use ZtwenAssert;
    use ModelQueryTrait;

    protected $listeners = [
        'throwSchoolBuiding' => 'throwSchoolBuiding',
        'schoolHasBeenCreated',
        'newLevelCreated' => 'reloadData',
        'newClasseGroupCreated' => 'reloadData',
        'newClasseCreated' => 'reloadData',
        'newSubjectCreated' => 'reloadData',
        'newPupilHasBeenAdded' => 'reloadData',

    ];

    protected $rules = [
        'school_name' => 'required|string|between:3,255',
        'school_year_start' => 'required|string|between:10,15',
        'semestre_type' => 'required|string|min:8'

    ];

    public $start_new_school = false;
    public $has_data = false;
    public $school_name = "Mon école";
    public $semestre_type = 'Semestre';
    public $school_year_start;
    public $counter = 0;
    public $semestre_selected = 1;
    public $active_section = 'standard_section';


    public function mount()
    {
        date_default_timezone_set('UTC');
        $school = School::first();
        $semestres = [1, 2];
        if($school){
            if($school->trimestre){
                $this->semestre_type = 'trimestre';
                $semestres = [1, 2, 3];
            }
            else{
                $semestres = [1, 2];
            }
        }


        if(session()->has('semestre_selected') && session('semestre_selected')){
            $semestre = intval(session('semestre_selected'));
            session()->put('semestre_selected', $semestre);
            $this->semestre_selected = $semestre;
        }
        else{
            $this->semestre_selected = 1;
            session()->put('semestre_selected', $this->semestre_selected);
        }
       
    }


    public function setActiveSection($section = null)
    {
        $this->active_section = $section ? 'listing_section' : 'standard_section';
    }
   



    public function render()
    {
        $school_years = SchoolYear::all();
        $schools = School::all();
        $has_school = false;
        $school_years_tabs = [];
        if(count($school_years) < 1 && count($schools) < 1){
            $progress = 1;
            $date = intval(date('Y'));
            $this->school_year_start = $date .' - ' . ($date  + 1);
            for ($i=1995; $i <= $date; $i++) { 
                $y = $i . ' - ' . ($i+1);
                $school_years_tabs[] = $y;
            }
        }
        else{
            $has_school = true;
            $progr = $this->getProgressValue() + 15;
            if($progr > 100){
                $progress = $progr - 15;
            }
            else{
                $progress = $progr;
            }
            if($progress > 100){
                $this->has_data = true;
            }
        }
        return view('livewire.admin', compact('has_school', 'school_years_tabs', 'progress'));
    }


    public function buildSchool()
    {
        $this->validate();

        $school_year_start  = $this->school_year_start;
        $school_name  = $this->school_name;
        $semestre_type  = $this->semestre_type;
        $parts = explode(' - ', $school_year_start);
        $start = intval($parts[0]);
        $end = intval($parts[1]);
        $now = intval(date('Y'));
        $school_year_now = date('Y') . ' - ' . intval(date('Y') + 1);

        if($school_year_now == $school_year_start){
            DB::transaction(function($e) use($semestre_type, $school_name, $school_year_start) {
                try {
                    $school = School::create([
                        'name' => $school_name,
                        'semestre' => $this->areEquals($semestre_type, 'semestre'),
                        'trimestre' => $this->areEquals($semestre_type, 'trimestre')
                    ]);
                    if($school){
                        try {
                            SchoolYear::create(['school_year' => $school_year_start]);
                        } 
                        catch (Exception $e) {
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "$school_name n'a pu être créée, une erreure est survenue lors de la création des années scolaires!", 'type' => 'error']);
                        }

                    }
                } 
                catch (Exception $ee) {
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "$school_name n'a pu être créée, une erreure est survenue lors de la création!", 'type' => 'error']);
                }
            });

            DB::afterCommit(function() use ($school_name){
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Validation réussie!', 'message' => "$school_name a été créée avec succès", 'type' => 'success']);
                $this->emit('schoolHasBeenCreated');
            });
        }
        else{
            DB::transaction(function($e) use($semestre_type, $school_name, $start, $now) {
                try {
                    $school = School::create([
                        'name' => $school_name,
                        'semestre' => $this->areEquals($semestre_type, 'semestre'),
                        'trimestre' => $this->areEquals($semestre_type, 'trimestre')
                    ]);
                    if($school){
                        for ($i = $start; $i <= $now; $i++) { 
                            try {
                                $school_year = $i . ' - ' .intval($i + 1);
                                SchoolYear::create(['school_year' => $school_year]);
                            } catch (Exception $ee) {
                                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "$school_name n'a pu être créée, une erreure est survenue lors de la création des années scolaires!", 'type' => 'error']);
                            }
                        }
                    }
                    
                } catch (Exception $e) {
                    $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure serveur!', 'message' => "$school_name n'a pu être créée, une erreure est survenue lors de la création!", 'type' => 'error']);
                }
            });


            DB::afterCommit(function() use ($school_name){
                $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Validation réussie!', 'message' => "$school_name a été créée avec succès", 'type' => 'success']);
                $this->emit('schoolHasBeenCreated');
            });
        }

    }

    public function getProgressValue()
    {
        $l = 0;
        $c = 0; 
        $p = 0; 
        $s = 0; 

        $level = count(Level::all());
        $classe = count(Classe::all());
        $pupil = count(Pupil::all());
        $subject = count(Subject::all());

        if($level){
            $l = 1;
        }
        if($classe){
            $c = 1;
        }
        if($pupil){
            $p = 1;
        }
        if($subject){
            $s = 1;
        }
        


        return (($l + $c + $p + $s) / 4) * 100;



    }



    public function make()
    {
        $marks = Mark::where('id', '>', 2499)->get();
        foreach($marks as $mark){
            $mark->update(['creator' => auth()->user()->id, 'user_id' => auth()->user()->id]);

        }


    }


    public function generateSchoolYear($direction = 1)
    {
        DB::transaction(function($e) use($direction){
            if($direction == -1){
                $school_years1 = SchoolYear::orderBy('school_year', 'asc')->pluck('school_year')->toArray();
                if(count($school_years1) > 0){
                    $min = $school_years1[0];
                    $min_year = (int)trim(explode('-', $min)[0]);
                    $school_year = ($min_year - 1) . ' - ' . $min_year;
                    $school_year_was_exited = SchoolYear::where('school_year', $school_year)->first();

                    if(!$school_year_was_exited){
                        $school_year_model = SchoolYear::create(['school_year' => $school_year]);
                        if($school_year_model){
                            $this->emit('schoolHasBeenCreated');
                            $this->reloadData();
                            $this->dispatchBrowserEvent('Toast', ['title' => 'NOUVELLE ANNEE-SCOLIARE CREEE', 'message' => "L'année-scolaire $school_year a été créée!", 'type' => 'success']);
                        }
                        else{
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "Une erreure est survenue lors de la création de la nouvelle année scolaire $school_year", 'type' => 'error']);
                        }
                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE ANNEE-SCOLIARE EXISTENTE', 'message' => "L'année scolaire $school_year ne pourrait être créee de nouveau car, elle existe déjà!", 'type' => 'error']);

                    }
                }
            }
            elseif($direction == 1){
                $school_years2 = SchoolYear::orderBy('school_year', 'desc')->pluck('school_year')->toArray();
                if(count($school_years2) > 0){
                    $max = $school_years2[0];
                    $max_year = (int)trim(explode('-', $max)[1]);
                    $school_year = $max_year . ' - ' . ($max_year + 1);
                    $school_year_model = SchoolYear::create(['school_year' => $school_year]);
                    $school_year_was_exited = SchoolYear::where('school_year', $school_year)->first();


                    if(!$school_year_was_exited){
                        $school_year_model = SchoolYear::create(['school_year' => $school_year]);
                        if($school_year_model){
                            $this->emit('schoolHasBeenCreated');
                            $this->reloadData();
                            $this->dispatchBrowserEvent('Toast', ['title' => 'NOUVELLE ANNEE-SCOLIARE CREEE', 'message' => "L'année-scolaire $school_year a été créée!", 'type' => 'success']);
                        }
                        else{
                            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE SERVEUR', 'message' => "Une erreure est survenue lors de la création de la nouvelle année scolaire $school_year", 'type' => 'error']);
                        }
                    }
                    else{
                        $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'ERREURE ANNEE-SCOLIARE EXISTENTE', 'message' => "L'année scolaire $school_year ne pourrait être créee de nouveau car, elle existe déjà!", 'type' => 'error']);

                    }
                    
                }

            }
        });
    }



    public function throwSchoolBuiding()
    {
        $this->start_new_school = true;
    }

    public function createNewLevel()
    {
        $this->emit('createNewLevelLiveEvent');
    }

    public function createNewSubject()
    {
        $this->emit('createNewSubjectLiveEvent');
    }
    
    public function schoolHasBeenCreated()
    {
        $this->reset('start_new_school');


    }

    public function createNewClasse()
    {
        $this->emit('createNewClasseLiveEvent');
    }

    public function reloadData()
    {
        $this->counter = 1;
    }

    public function addNewPupilToClasse()
    {
        $school_year = session('school_year_selected');
        $school_year_model = SchoolYear::where('school_year', $school_year)->first();
        $classe = $school_year_model->classes()->first();
        if($classe){
            $this->emit('addNewPupilToClasseLiveEvent', $classe->id);
        }
        else{
            $this->dispatchBrowserEvent('ToastDoNotClose', ['title' => 'Erreure', 'message' => "Vous ne pouvez pas encore de ajouter d'apprenant sans avoir au préalable créer au moins une classe!", 'type' => 'error']);
        }

    }

    public function addNewClasseGroup()
    {
        $this->emit('createNewClasseGroupLiveEvent');
    }

    public function definedSemestrePeriod()
    {
        $this->emit('definedSemestresPeriodsLiveEvent');
    }

    




}
